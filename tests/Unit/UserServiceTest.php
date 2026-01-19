<?php

namespace Tests\Unit;

use App\Data\UserData;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }

    public function test_it_can_register_a_user()
    {
        Cache::spy();

        $data = UserData::from([
            'nickname' => 'testuser',
            'avatar' => 'avatars/test.jpg'
        ]);

        $user = $this->userService->register($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('testuser', $user->nickname);
        $this->assertEquals('avatars/test.jpg', $user->avatar);
        $this->assertDatabaseHas('users', ['nickname' => 'testuser']);

        Cache::shouldHaveReceived('forget')
            ->once()
            ->with('users:all');
    }

    public function test_it_can_get_all_users_from_cache_or_db()
    {
        User::factory()->count(2)->create();

        $users = $this->userService->index();

        $this->assertCount(2, $users);
        $this->assertTrue(Cache::has('users:all'));
    }
}
