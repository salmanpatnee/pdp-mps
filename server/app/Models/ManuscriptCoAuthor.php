<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManuscriptCoAuthor extends Model
{
    use HasFactory;

    protected $fillable = [
        'manuscript_id',
        'user_id',
        'name',
        'email',
        'affiliation',
        'country',
        'is_principal',
        'order',
    ];

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(Manuscript::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
