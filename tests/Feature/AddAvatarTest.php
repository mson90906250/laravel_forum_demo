<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddAvatarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_members_can_upload_avatar()
    {
        $this->withExceptionHandling();

        $this->json('POST', '/api/users/1/avatar', [])
            ->assertStatus(401);
    }

    /** @test */
    public function avatar_must_be_valid()
    {
        $this->withExceptionHandling()
            ->signIn();

        $this->json('POST', '/api/users/1/avatar', [
            'avatar' => 'not-an-image'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_user_can_upload_avatars_to_their_profile()
    {
        $this->withExceptionHandling()
            ->signIn();

        Storage::fake('public');

        $this->json('POST', '/api/users/1/avatar', [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg')
        ]);

        $this->assertEquals(Storage::url('avatars/' . $file->hashName()), auth()->user()->avatar_path);

        Storage::disk('public')->assertExists('avatars/' . $file->hashName());

    }

    /** @test */
    public function a_user_can_determine_their_avatar()
    {
        $user = create('App\User');

        $this->assertEquals(Storage::url('avatars/default.png'), $user->avatar_path);

        $user->update([
            'avatar_path' => 'avatars/me.jpg'
        ]);

        $this->assertEquals(Storage::url('avatars/me.jpg'), $user->avatar_path);
    }
}
