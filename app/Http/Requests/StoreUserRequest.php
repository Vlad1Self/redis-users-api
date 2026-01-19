<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Data\UserData;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nickname' => 'required|string|unique:users,nickname|max:50',
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function getDTO(): UserData
    {
        $data = $this->validated();

        if ($this->hasFile('avatar')) {
            $data['avatar'] = $this->file('avatar')->store('avatars', 'public');
        }

        return UserData::from($data);
    }
}
