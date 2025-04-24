<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            \Log::info($user);
            return redirect()->route('solicitudes.vb.grupos'); // Ruta única para el usuario 1
        }

        // Comportamiento normal para otros usuarios
        return redirect()->intended($this->redirectPath());
    }

    public function login(Request $request)
    {
        $this->validateLogin($request); // usa las reglas del trait

        // Si el usuario ha superado el límite de intentos
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials, $request->filled('remember'))) {
            \Log::warning('Login fallido', [
                'email' => $credentials['email'],
                'ip' => $request->ip(),
                'hora' => now()->toDateTimeString(),
            ]);

            $this->incrementLoginAttempts($request); // incrementa intentos fallidos
            return $this->sendFailedLoginResponse($request);
        }

        // Autenticación exitosa
        return $this->sendLoginResponse($request);
    }
}
