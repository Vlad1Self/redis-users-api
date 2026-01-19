<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_user_can_register_with_valid_data()
    {
        $nickname = $this->faker->userName;
        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->postJson('/api/register', [
            'nickname' => $nickname,
            'avatar' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nickname',
                    'avatar',
                ],
                'status' => [
                    'code',
                    'message',
                    'description',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'nickname' => $nickname,
        ]);

        $user = User::where('nickname', $nickname)->first();
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertExists($user->avatar);
    }

    public function test_user_cannot_register_without_nickname()
    {
        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->postJson('/api/register', [
            'avatar' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nickname'], 'data');
    }

    public function test_user_cannot_register_without_avatar()
    {
        $nickname = $this->faker->userName;

        $response = $this->postJson('/api/register', [
            'nickname' => $nickname,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avatar'], 'data');
    }

    public function test_user_cannot_register_with_duplicate_nickname()
    {
        $existingUser = User::factory()->create();
        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->postJson('/api/register', [
            'nickname' => $existingUser->nickname,
            'avatar' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nickname'], 'data');
    }

    public function test_user_cannot_register_with_invalid_avatar_type()
    {
        $nickname = $this->faker->userName;
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/register', [
            'nickname' => $nickname,
            'avatar' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avatar'], 'data');
    }
}
