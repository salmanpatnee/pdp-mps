<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    /** @use HasFactory<\Database\Factories\JournalFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'issn',
        'eissn',
        'abbreviation',
        'description',
        'publisher',
        'email',
        'website_url',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function manuscripts()
    {
        return $this->hasMany(Manuscript::class);
    }
}
