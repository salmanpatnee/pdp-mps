<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuscriptFile extends Model
{
    protected $fillable = [
        'manuscript_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_type',
        'file_extension'
    ];

    public function manuscript()
    {
        return $this->belongsTo(Manuscript::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
