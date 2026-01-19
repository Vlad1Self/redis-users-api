<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'nickname' => $this->nickname,
            'avatar'   => asset('storage/' . $this->avatar),
        ];
    }
}
