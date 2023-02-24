<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class passwordController extends Controller
{
    public function index()
    {
        return view('password');
    }

    public function updatePassword(Request $request)
    {
        //dd(Auth::user()->email);
        $pass1= $request->nuevaContraseña;
        $pass2= $request->confi_nuv_contraseña;
        if($pass2==$pass1)
        {
            if(Hash::check($request->contraseña, Auth::user()->password))
            {
                echo 'hola';
                $user= DB::table('users')->where('email','=', Auth::user()->email)->update(['password'=>bcrypt($request->nuevaContraseña)]);
                return redirect()->route('password.view')->with('success', sprintf('¡CONTRASEÑA ACTUALIZADA CON EXITO!'));
            }else{

                return redirect()->route('password.view')
                ->withErrors(sprintf('¡LA CONTRASEÑA INGRESADA NO ES CORRECTA!'));
            }
        }else{

            return redirect()->route('password.view')
                ->withErrors(sprintf('¡LA CONFIRMACION DE LA CONTRASEÑA NO ES LA MISMA!'));
        }

    }
}
