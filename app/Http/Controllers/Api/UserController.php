<?php

namespace App\Http\Controllers\Api;

use App\Contracts\UserServiceContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;

class UserController extends Controller
{
    public function __construct(
        private UserServiceContract $userService
    ) {}

    public function register(StoreUserRequest $request)
    {
        $dto = $request->getDTO();

        $user = $this->userService->register($dto);

        return ApiResponse::success(new UserResource($user));
    }

    public function index()
    {
        $users = $this->userService->index();

        return view('users.index', [
            'users' => UserResource::collection($users)
        ]);
    }
}
