<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Models\user;
use App\Notifications\SupreNotification;

class SupreListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // dd($event);
        User::select('users.*')->WHERE('role_user.role_id', '=', '4')
                ->JOIN('role_user', 'role_user.user_id', '=', 'users.id')
                ->EACH(function(User $user) use ($event){
                    Notification::send($user, new SupreNotification($event->supre));
                });
    }
}
