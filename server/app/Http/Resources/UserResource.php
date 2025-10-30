<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role_id' => $this->role_id,
            'journal_id' => $this->journal_id,
            'role' => new RoleResource($this->role),
            $this->mergeWhen($request->routeIs('users.show'), [
                'journal' => new JournalResource($this->journal),
            ]),
            'name' => $this->name,
            'email' => $this->email,
            'country' => $this->country,
            'affiliation' => $this->affiliation,
            'profile_link' => $this->profile_link,
            'created_at' => $this->created_at
        ];
    }
}
