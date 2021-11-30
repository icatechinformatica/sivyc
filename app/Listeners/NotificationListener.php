<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use App\Events\NotificationEvent;
use App\Notifications\BasicNotification;
use App\Models\user;

class NotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event) {

        $this->tokens_movil = [];
        foreach($event->users as $user) {

            if($user->token_movil != null) {
                array_push($this->tokens_movil, $user->token_movil);
            }

            Notification::send($user, new BasicNotification($event->letter));
        }

        sendNotification($this->tokens_movil, $event->letter['titulo'], $event->letter['cuerpo']);
    }
}
