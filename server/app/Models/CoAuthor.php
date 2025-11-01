<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoAuthor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'affiliation',
        'country',
    ];

    public function manuscripts()
    {
        return $this->belongsToMany(Manuscript::class, 'manuscript_co_author')
            ->withPivot('is_principal', 'order')
            ->withTimestamps();
    }
}
