<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $avatars = Storage::disk('public')->files('avatars/default');

        $baseNickname = Str::lower(
            fake()->unique()->userName()
        );
        if (fake()->boolean(60)) {
            $baseNickname .= fake()->numberBetween(1, 9999);
        }

        return [
            'nickname' => $baseNickname,
            'avatar' => !empty($avatars)
                ? $avatars[array_rand($avatars)]
                : 'avatars/default.jpg',
        ];
    }
}
