<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuscriptReviewer extends Model
{
    protected $fillable = ['manuscript_id', 'user_id'];

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
