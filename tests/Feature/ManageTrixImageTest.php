<?php

namespace Tests\Feature;

use App\TrixImage;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageTrixImageTest extends TestCase
{
    use RefreshDatabase;

    protected $fileFactory;

    protected function setUp() : void
    {
        parent::setUp();

        $this->withExceptionHandling();

        Storage::fake('public');

        $this->fileFactory = UploadedFile::fake();

        TrixImage::reset();
    }

    /** @test */
    public function only_authenticated_users_can_add_images()
    {
        $thread = create('App\Thread');

        $this->postJson(route('trixImage.store', $thread), ['image' => null])
            ->assertStatus(401);

        TrixImage::reset();
    }

    /** @test */
    public function a_user_can_add_images_to_thread_body()
    {
        $this->signIn();

        $normalImage = $this->fileFactory->image('normal_image.jpg')->size(512);

        $response = $this->postJson(route('trixImage.store'), [
                'image' => $normalImage
            ])->json();

        $this->assertEquals('images/trix/' . $normalImage->hashName(), $response['filePath']);

        $this->assertEquals(Storage::url('images/trix/' . $normalImage->hashName()), $response['url']);

        Storage::disk('public')->assertExists('images/trix/' . $normalImage->hashName());

        TrixImage::reset();
    }

    /** @test */
    public function images_bigger_than_512kb_can_not_be_uploaded()
    {
        $this->signIn();

        $bigImage = $this->fileFactory->image('big_image.jpg')->size(5 * 1024);

        $this->postJson(route('trixImage.store'), [
                'image' => $bigImage
            ])->assertStatus(422);

        Storage::disk('public')->assertMissing('images/trix/' . $bigImage->hashName());

        TrixImage::reset();
    }

    /** @test */
    public function an_image_can_be_removed()
    {
        $this->signIn();

        $image = $this->fileFactory->image('foobar.jpg');

        $this->postJson(route('trixImage.store'), [
                'image' => $image
            ]);

        Storage::disk('public')->assertExists('images/trix/' . $image->hashName());

        $this->deleteJson(route('trixImage.destroy'), ['images' => 'images/trix/' . $image->hashName()]);

        Storage::disk('public')->assertMissing('images/trix/' . $image->hashName());

        TrixImage::reset();
    }

    /** @test */
    public function multiple_images_can_be_removed_at_once()
    {
        $this->signIn();

        $image = $this->fileFactory->image('foobar.jpg');
        $otherImage = $this->fileFactory->image('other.jpg');

        $this->postJson(route('trixImage.store'), [
            'image' => $image
        ]);

        $this->postJson(route('trixImage.store'), [
            'image' => $otherImage
        ]);

        $this->deleteJson(route('trixImage.destroy'), [
            'images' => [
                'images/trix/' . $image->hashName(),
                'images/trix/' . $otherImage->hashName(),
            ]
        ]);

        Storage::disk('public')->assertMissing('images/trix/' . $image->hashName());
        Storage::disk('public')->assertMissing('images/trix/' . $otherImage->hashName());

        TrixImage::reset();
    }

    /** @test */
    public function filePath_of_images_will_be_added_to_redis_after_uploaded()
    {
        // 在圖片上傳後(尚未與db同步), 產生出來的圖片路徑將會存入redis的待刪名單中
        $this->signIn();

        $normalImage = $this->fileFactory->image('normal_image.jpg')->size(512);

        $response = $this->postJson(route('trixImage.store'), [
                'image' => $normalImage
            ])->json();

        $this->assertEquals(
            $response['filePath'],
            TrixImage::get(TrixImage::rpop())
        );

        TrixImage::reset();
    }

    /** @test */
    public function images_should_be_persisted_after_updated_or_stored()
    {
        // 在文章更新或發布時, 上傳的圖片必須從待刪除名單(redis)中去掉, 以免被誤刪
        $this->signIn();

        $image = $this->fileFactory->image('normal_image.jpg');

        // 儲存圖片並加入redis待刪名單中
        $response = $this->postJson(route('trixImage.store'), [
                'image' => $image
            ])->json();

        $persistList = [$response['cacheKey']];

        // 與db同步
       $this->patchJson(
            route('trixImage.update'),
            ['persistList' => $persistList]
        );

        $this->assertFalse(TrixImage::exists($response['cacheKey']));

        TrixImage::reset();
    }

    /** @test */
    public function images_not_persisted_should_be_deleted()
    {
        // 刪除圖片會交由worker執行, 在這裡會模擬整個刪除流程
        $this->signIn();

        $this->postJson(route('trixImage.store'), [
            'image' => $this->fileFactory->image('image.jpg')
        ])->json();

        // 假設文章無法順利更新或創建
        $cacheKey = TrixImage::rpop();  // 從待刪名單中取出一筆

        if (! TrixImage::exists($cacheKey)) return; // 該圖片已不存在於待刪名單中, 即已經與db同步了

        $filePath = TrixImage::get($cacheKey);

        tap(Storage::disk('public'), function ($storage) use ($filePath) {
            $storage->delete($filePath);
            $storage->assertMissing($filePath);
        });

        TrixImage::delete($cacheKey);
        $this->assertFalse(TrixImage::exists($cacheKey));

        TrixImage::reset();
    }
}
