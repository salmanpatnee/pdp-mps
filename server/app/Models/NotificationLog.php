<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'manuscript_id',
        'co_author_email',
        'status',
        'sent_at',
        'failed_at',
        'error_message',
    ];
}
