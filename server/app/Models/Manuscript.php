<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Manuscript extends Model
{

    protected static function booted()
    {
        static::creating(function ($manuscript) {
            if (empty($manuscript->reference_no)) {
                $manuscript->reference_no = self::generateReferenceNo($manuscript->journal_id);
            }
        });

        static::deleting(function ($manuscript) {
            // Delete associated files from storage then remove DB records
            foreach ($manuscript->files as $file) {
                if (!empty($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }
            }
            $manuscript->files()->delete();

            // Detach many-to-many relations
            $manuscript->reviewers()->detach();

            // Delete one-to-one related records
            $manuscript->copyright()->delete();
        });
    }


    protected $fillable = [
        'reference_no',
        'journal_id',
        'user_id',
        'article_type_id',
        'submission_type',
        'title',
        'abstract',
        'keywords',
        'status'
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coAuthors()
    {
        return $this->hasMany(ManuscriptCoAuthor::class);
    }

    public function reviewers()
    {
        return $this->belongsToMany(User::class, 'manuscript_reviewer')
            ->withTimestamps();
    }

    public function articleType()
    {
        return $this->belongsTo(ArticleType::class);
    }

    public function files()
    {
        return $this->hasMany(ManuscriptFile::class);
    }

    public function copyright()
    {
        return $this->hasOne(Copyright::class);
    }

    public static function generateReferenceNo($journalId)
    {
        $journal = Journal::find($journalId);
        $abbr = strtoupper($journal->abbreviation ?? 'GEN');

        $year = date('Y');

        // Get the last manuscript for this year and journal
        $last = self::where('journal_id', $journalId)
            ->latest('id')
            ->first();

        $nextSequence = $last ? intval(substr($last->reference_no, -4)) + 1 : 1;

        // Format: PDP-ABBR-2025-0001
        return sprintf('PDP-%s-%s-%04d', $abbr, $year, $nextSequence);
    }
}
