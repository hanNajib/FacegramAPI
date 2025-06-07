<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            "caption" => $this->caption,
            "created_at" => $this->created_at,
            "deleted_at" => $this->deleted_at,
            "user" => $this->user,
            "attachments" => $this->attachments
        ];
    }
}
