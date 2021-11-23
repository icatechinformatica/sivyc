<?php

namespace App\Http\Controllers\ApiController\ApisMovil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeMovil extends Controller {
    
    public function getNotificaciones(Request $request) {
        $id = $request->idUser;
        $notificaciones = DB::table('notifications')
            ->where('notifications.notifiable_id', $id)
            ->select('notifications.*')
            // ->orderBy('created_at')
            ->orderByDesc('notifications.created_at')
            ->paginate(15, ['notifications.*']);
        // ->get();

        return response()->json($notificaciones, 200);
    }

    public function updateRead(Request $request) {
        $id = $request->id;
        $read = $request->read;
        DB::table('notifications')->where('id', $id)->update(['read_movil' => $read]);
        return response()->json('success', 200);
    }

}
