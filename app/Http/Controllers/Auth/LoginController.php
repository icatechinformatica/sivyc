<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

     /**
     * Método que se ejecuta después de una autenticación exitosa
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticated(Request $request, $user)
    {
        //solicitudes.vb.grupos
        if ($user->id === 1) {
            \Log::info($user->id);
            return redirect()->route('solicitudes.vb.grupos');
        }

        // Comportamiento normal para otros usuarios
        return redirect()->intended($this->redirectPath());
    }
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if(isset($user->id)) {
            $roles = DB::Table('tblz_usuario_rol')->Where('usuario_id', $user->id)->Get();
            foreach($roles as $rol) {
                if(in_array($rol->rol_id, [11, 44, 5, 55])) {
                    throw ValidationException::withMessages([
                        'email' => [new HtmlString('S I V y C &nbsp;&nbsp;&nbsp;&nbsp; E N &nbsp;&nbsp;&nbsp;&nbsp; M A N T E N I M I E N T O.')],
                    ]);
                }
            }
        }

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No encontramos un usuario registrado con ese correo.'],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['La contraseña proporcionada es incorrecta.'],
        ]);
    }
}
