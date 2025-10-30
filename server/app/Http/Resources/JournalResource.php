<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JournalResource extends JsonResource
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
            'name' => $this->name,
            'issn' => $this->issn,
            'eissn' => $this->eissn,
            'abbreviation' => $this->abbreviation,
            'description' => $this->description,
            'publisher' => $this->publisher,
            'email' => $this->email,
            'website_url' => $this->website_url,
            'is_active' => $this->is_active,
        ];
    }
}
