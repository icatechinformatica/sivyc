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
use App\Services\WhatsAppService;

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

    /**
     * Obtiene las credenciales necesarias para la autenticación.
     * Incluye la validación del campo 'activo' = 1
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return [
            'email' => $request->email,
            'password' => $request->password,
            'activo' => 1  // Solo permite login si el usuario está activo
        ];
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        // if(isset($user->id)) {
        //     $roles = DB::Table('role_user')->Where('user_id', $user->id)->Get();
        //     foreach($roles as $rol) {
        //         if(in_array($rol->role_id, [11, 44, 5, 55])) {
        //             throw ValidationException::withMessages([
        //                 'email' => [new HtmlString('S I V y C &nbsp;&nbsp;&nbsp;&nbsp; E N &nbsp;&nbsp;&nbsp;&nbsp; M A N T E N I M I E N T O.')],
        //             ]);
        //         }
        //     }
        // }

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No encontramos un usuario registrado con ese correo.'],
            ]);
        }

        // Verificar si el usuario está inactivo
        if (!$user->activo) {
            throw ValidationException::withMessages([
                'email' => ['Tu cuenta está inactiva. Por favor contacta al administrador.'],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['La contraseña proporcionada es incorrecta.'],
        ]);
    }


    public function resetPasswordModal(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'exists:users,email,activo,1'
            ],
            'resetTelefono' => 'required'
        ], [
            'email.exists' => 'El correo proporcionado para restablecer la contraseña es erróneo o el usuario no está activo.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate a new random password
        $newPassword = \Str::random(6);
        $user->password = \Hash::make($newPassword);
        $user->telefono = $request->resetTelefono;

        //mensaje via whatsapp
        $infowhats = [
            'nombre' => $user->name,
            'correo' => $user->email,
            'pwd' => $newPassword,
            'telefono' => $user->telefono,
        ];

        $response = $this->whatsapp_restablecer_usuario_msg($infowhats, app(WhatsAppService::class));

        // Check for WhatsApp sending errors in the response
        if (isset($response['status']) && $response['status'] === false) {
            return back()->with('error', 'Error al enviar mensaje de WhatsApp: ' . ($response['message'] ?? 'Error desconocido'));
        }

        $user->save();

        return back()->with('success', 'Tu contraseña ha sido restablecida. Se ha enviado un mensaje de WhatsApp con tu nueva contraseña.');
    }

    private function whatsapp_restablecer_usuario_msg($instructor, WhatsAppService $whatsapp)
    {
        $plantilla = DB::Table('tbl_wsp_plantillas')->Where('nombre', 'restablecer_pwd_sivyc')->First();

        // Reemplazar variables en plantilla
        $mensaje = str_replace(
            ['{{nombre}}', '{{correo}}', '{{pwd}}', '\n'],
            [$instructor['nombre'], $instructor['correo'], $instructor['pwd'], "\n"],
            $plantilla->plantilla
        );

        $callback = $whatsapp->cola($instructor['telefono'], $mensaje, $plantilla->prueba);

        return $callback;
    }

    public function getTelefonoByEmail(Request $request)
    {
        $user = \App\Models\User::where('email', $request->email)->first();
        return response()->json(['telefono' => $user ? $user->telefono : '']);
    }
}
