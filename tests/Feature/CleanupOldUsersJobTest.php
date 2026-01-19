<?php

namespace Tests\Feature;

use App\Jobs\CleanupOldUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanupOldUsersJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_old_users_and_their_avatars()
    {
        Storage::fake('public');

        $oldUser = User::factory()->create([
            'created_at' => now()->subMonths(2),
            'avatar' => 'avatars/old.jpg'
        ]);
        Storage::disk('public')->put('avatars/old.jpg', 'content');

        $newUser = User::factory()->create([
            'created_at' => now()->subDays(1),
            'avatar' => 'avatars/new.jpg'
        ]);
        Storage::disk('public')->put('avatars/new.jpg', 'content');

        (new CleanupOldUsers())->handle();

        $this->assertDatabaseMissing('users', ['id' => $oldUser->id]);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $disk->assertMissing('avatars/old.jpg');

        $this->assertDatabaseHas('users', ['id' => $newUser->id]);
        $disk->assertExists('avatars/new.jpg');
    }
}
