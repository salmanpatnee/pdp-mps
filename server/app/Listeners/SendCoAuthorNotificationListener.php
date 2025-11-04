<?php

namespace App\Listeners;

use App\Events\ManuscriptSubmitted;
use App\Jobs\SendCoAuthorNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;

class SendCoAuthorNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ManuscriptSubmitted $event): void
    {
        foreach ($event->manuscript->coAuthors as $coAuthor) {
            SendCoAuthorNotification::dispatch($event->manuscript, $coAuthor);
        }
    }
}
