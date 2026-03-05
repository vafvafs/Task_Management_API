<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'profile_photo_url' => $this->profile_photo_path
                ? url("/api/teams/{$this->id}/profile-photo")
                : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'users'      => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
