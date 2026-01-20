<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = ['nickname', 'avatar'];

    public function getAvatarUrlAttribute(): string
    {
        if (str_starts_with($this->avatar, 'avatars/default')) {
            return asset($this->avatar);
        }

        return asset('storage/' . $this->avatar);
    }
}
