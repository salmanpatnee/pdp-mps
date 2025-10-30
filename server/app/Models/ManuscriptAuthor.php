<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuscriptAuthor extends Model
{
    protected $fillable = ['manuscript_id', 'user_id', 'is_principal'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_principal' => 'boolean',
        ];
    }

    public function manuscripts()
    {
        return $this->belongsToMany(Manuscript::class)
            ->withPivot('is_principal')
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
