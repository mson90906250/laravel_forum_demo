<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AddThreadImageTest extends TestCase
{
    use RefreshDatabase;

    protected $fileFactory;

    protected function setUp() : void
    {
        parent::setUp();

        $this->withExceptionHandling();

        Storage::fake('public');

        $this->fileFactory = UploadedFile::fake();
    }

    /** @test */
    public function only_authenticated_users_can_add_images()
    {
        $thread = create('App\Thread');

        $this->postJson(route('threadImage.store', $thread), ['image' => null])
            ->assertStatus(401);
    }

    /** @test */
    public function a_user_can_add_images_to_thread_body()
    {
        $this->signIn();

        $normalImage = $this->fileFactory->image('normal_image.jpg')->size(512);

        $response = $this->postJson(route('threadImage.store'), [
                'image' => $normalImage
            ])->json();

        $this->assertEquals('images/threads/' . $normalImage->hashName(), $response['filePath']);

        $this->assertEquals(Storage::url('images/threads/' . $normalImage->hashName()), $response['url']);

        Storage::disk('public')->assertExists('images/threads/' . $normalImage->hashName());
    }

    /** @test */
    public function images_bigger_than_512kb_can_not_be_uploaded()
    {
        $this->signIn();

        $bigImage = $this->fileFactory->image('big_image.jpg')->size(5 * 1024);

        $this->postJson(route('threadImage.store'), [
                'image' => $bigImage
            ])->assertStatus(422);

        Storage::disk('public')->assertMissing('images/threads/' . $bigImage->hashName());
    }
}
