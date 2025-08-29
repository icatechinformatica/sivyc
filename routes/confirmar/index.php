
<?php

use Illuminate\Support\Facades\Route;

// Ruta dinámica con dos parámetros (ejemplo: /confirmar/instructor/123/abc123)
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Http\Request;

Route::get('/confirmar/instructor/{id}/{token}', function ($id, $token) {
    try {
        // decodificar Id
        $id = base64_decode($id);
        // 2. Separar el token recibido
        [$fechaStr, $horaStr] = explode('|', $token);
        // 3. Construir fecha completa
        $fechaToken = Carbon::createFromFormat('dmY H:i:s', $fechaStr . ' ' . $horaStr);

        // 4. Fecha de expiración (ejemplo: 5 horas)
        $fechaExpiracion = $fechaToken->copy()->addHours(5);

        if (now()->greaterThan($fechaExpiracion)) {
            return view('mensaje_instructor.index', [
                'cursos' => null,
                'token' => $token,
                'error' => 'El enlace ha expirado'
            ]);
        }

        // Consultar usuario en la BD
        $cursos = DB::table('tbl_cursos')->where('id', $id)->first();

        if (!$cursos) {
            return view('mensaje_instructor.index', [
                'cursos' => null,
                'token' => $token,
                'error' => 'Usuario no encontrado'
            ]);
        }

        // Retornar vista con datos válidos
        return view('mensaje_instructor.index', [
            'cursos' => $cursos,
            'token' => $token,
            'error' => null
        ]);

    } catch (\Exception $e) {
        // Manejo de errores (opcional)
        return view('mensaje_instructor.index', [
            'cursos' => null,
            'token' => $token,
            'error' => 'Token inválido'
        ]);
    }
})->name('confirmar.instructor');
Route::post('/confirmar/instructor/aceptar', function (Request $request) {
    $id = $request->input('curso_id');

    // Buscar el curso por id
    $curso = DB::table('tbl_cursos')->where('id', $id)->first();
    if (!$curso) {
        return response()->json(['error' => 'Curso no encontrado'], 404);
    }

    // Suponiendo que el campo jsonb se llama 'link_confirmacion' y es un array de registros
   $link_confirmacion = json_decode($curso->link_confirmacion, true);

    if (is_array($link_confirmacion) && !empty($link_confirmacion)) {
        // Modificar el objeto directamente
        $link_confirmacion['estatus'] = true;
        $link_confirmacion['fecha_aceptacion'] = date('d-m-Y H:i:s');
    }


    // Guardar como objeto
    DB::table('tbl_cursos')->where('id', $id)->update([
        'link_confirmacion' => json_encode($link_confirmacion)
    ]);

    return response()->json(['success' => true, 'link_confirmacion' => $link_confirmacion]);
})->name('instructor.aceptar.confirmacion');
