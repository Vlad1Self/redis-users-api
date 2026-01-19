<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_index_page_loads_correctly()
    {
        User::factory()->count(3)->create();

        $response = $this->get('/users');

        $response->assertStatus(200)
            ->assertViewIs('users.index')
            ->assertViewHas('users');
    }

    public function test_user_index_uses_caching()
    {
        User::factory()->count(2)->create();

        $this->get('/users');

        $this->assertTrue(Cache::has('users:all'));

        $cachedUsers = Cache::get('users:all');
        $this->assertCount(2, $cachedUsers);

        User::factory()->create();

        $response = $this->get('/users');
        $this->assertCount(2, $response->viewData('users'));
    }
}
