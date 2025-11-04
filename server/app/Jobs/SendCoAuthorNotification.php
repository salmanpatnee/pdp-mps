<?php

namespace App\Jobs;

use App\Mail\CoAuthorNotification;
use App\Models\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCoAuthorNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $manuscript;
    public $coAuthor;

    /**
     * Create a new job instance.
     */
    public function __construct($manuscript, $coAuthor)
    {
        $this->manuscript = $manuscript;
        $this->coAuthor = $coAuthor;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $log = NotificationLog::create([
            'manuscript_id' => $this->manuscript->id,
            'co_author_email' => $this->coAuthor->email,
            'status' => 'pending',
        ]);

        try {
            Mail::to($this->coAuthor->email)->send(new CoAuthorNotification($this->manuscript, $this->coAuthor));
            $log->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'failed_at' => now(), 'error_message' => $e->getMessage()]);
        }
    }
}
