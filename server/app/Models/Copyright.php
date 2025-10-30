<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Copyright extends Model
{
    protected $fillable = [
        'manuscript_id',
        'is_corporate_interest',
        'has_human_subjects',
        'human_subjects_approval_path',
        'has_animal_subjects',
        'animal_subjects_approval_path',
        'is_conflict_interest',
        'conflict_of_interest_details',
        'has_us_government_author',
        'us_government_permission_path',
        'used_ai_technology',
        'ai_usage_details',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_corporate_interest' => 'boolean',
            'has_human_subjects' => 'boolean',
            'has_animal_subjects' => 'boolean',
            'is_conflict_interest' => 'boolean',
            'has_us_government_author' => 'boolean',
            'used_ai_technology' => 'boolean',
        ];
    }
}
