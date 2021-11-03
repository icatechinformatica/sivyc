<?php

namespace App\Http\Controllers\ApiController\ApisInstructores;

use App\Http\Controllers\Controller;
use App\Models\instructor;
use Illuminate\Http\Request;

class RegistroController extends Controller
{
    public function getInstructor($curp) {
        return response()->json(instructor::where('curp', '=', $curp)->where('status', '=', 'Validado')->where('estado', '=', true)->first(),200);
    }
}
