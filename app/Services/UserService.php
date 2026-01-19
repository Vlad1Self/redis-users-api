<?php

namespace App\Services;

use App\Contracts\UserServiceContract;
use App\Data\UserData;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserService implements UserServiceContract
{
    public function register(UserData $data): User
    {
        $user = User::create([
            'nickname' => $data->nickname,
            'avatar' => $data->avatar,
        ]);

        Cache::forget('users:all');

        return $user;
    }

    public function index()
    {
        return Cache::remember('users:all', 10, function () {
            return User::all();
        });
    }
}
