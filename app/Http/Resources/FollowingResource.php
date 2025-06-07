<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "full_name" => $this->full_name,
            "username" => $this->username,
            "bio" => $this->bio,
            "is_private" => $this->is_private,
            "created_at" => $this->created_at,
            "is_requested" => $this->pivot->is_accepted ? false : true
        ];
    }
}
