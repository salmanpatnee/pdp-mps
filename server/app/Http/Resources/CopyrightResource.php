<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CopyrightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'is_corporate_interest' => $this->is_corporate_interest,
            'has_human_subjects' => $this->has_human_subjects,
            'human_subjects_approval_url' => $this->human_subjects_approval_path ? Storage::url($this->human_subjects_approval_path) : null,
            'has_animal_subjects' => $this->has_animal_subjects,
            'animal_subjects_approval_url' => $this->animal_subjects_approval_path ? Storage::url($this->animal_subjects_approval_path) : null,
            'is_conflict_interest' => $this->is_conflict_interest,
            'conflict_of_interest_details' => $this->conflict_of_interest_details,
            'has_us_government_author' => $this->has_us_government_author,
            'us_government_permission_url' => $this->us_government_permission_path ? Storage::url($this->us_government_permission_path) : null,
            'used_ai_technology' => $this->used_ai_technology,
            'ai_usage_details' => $this->ai_usage_details,
        ];
    }
}
