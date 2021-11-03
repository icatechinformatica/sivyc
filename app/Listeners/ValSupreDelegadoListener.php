<?php

namespace App\Listeners;

use App\Events\ValsupreDelegadoEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use App\Notifications\ValSupreDelegadoNotification;
use App\Models\user;

class ValSupreDelegadoListener
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
     * @param  ValsupreDelegadoEvent  $event
     * @return void
     */
    public function handle($event)
    {
        // dd($event);
            User::select('users.*')->WHERE('role_user.role_id', '=', '2')->WHEREraw("tbl_unidades.id = users.unidad")
                // ->JOIN('tbl_supre', function($join, $event)
                // {
                //     $join->on('tabla_supre.id', '=', $event->valsupre->id);
                // })
                ->JOIN('tabla_supre','tabla_supre.id', '=', db::raw($event->valsupre->id))
                ->JOIN('tbl_unidades','tbl_unidades.unidad', '=', 'tabla_supre.unidad_capacitacion')
                ->JOIN('role_user', 'role_user.user_id', '=', 'users.id')
                ->EACH(function(User $user) use ($event){
                    Notification::send($user, new ValSupreDelegadoNotification($event->valsupre));
                });
    }
}
