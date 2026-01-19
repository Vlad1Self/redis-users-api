<?php

namespace App\Contracts;

use App\Data\UserData;
use App\Models\User;

interface UserServiceContract
{
    public function register(UserData $data): User;

    public function index();
}
