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
    }

    /** @test */
    public function images_can_be_removed()
    {
        $this->signIn();

        $image = $this->fileFactory->image('foobar.jpg');

        $this->postJson(route('trixImage.store'), [
                'image' => $image
            ]);

        Storage::disk('public')->assertExists('images/trix/' . $image->hashName());

        $this->deleteJson(route('trixImage.destroy'), ['image' => 'images/trix/' . $image->hashName()]);

        Storage::disk('public')->assertMissing('images/trix/' . $image->hashName());
    }

    /** @test */
    public function filePath_of_images_will_be_added_to_redis_after_uploaded()
    {
        // 在圖片上傳後(尚未與db同步), 產生出來的圖片路徑將會存入redis
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
}
