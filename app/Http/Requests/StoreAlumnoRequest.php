<?php
// app/Http/Requests/StoreAlumnoRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlumnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ajusta según tu sistema de permisos
        return true;
    }

    public function rules(): array
    {
        // Puedes obtener el id del aspirante de varias formas:
        // 1) Si lo mandas como input hidden: name="aspirante_id"
        $aspiranteId = $this->input('aspirante_id');

        return [
            'curp'   => ['required', 'string', 'size:18'],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'nacionalidad' => ['required', 'string', 'min:2'],
            'fecha' => ['required'],
            'sexo' => ['required'],
            'estado'    => ['required'],
            'municipio' => ['required'],
            'localidad' => ['required'],
            'estado_civil' => ['required'],
            'ultimo_grado_estudios' => ['required'],
            'medio_entero' => ['required'],
            'motivos_eleccion_sistema_capacitacion' => ['required'],
            'correo' => [
                'nullable',     // Permite que el campo venga vacío
                'email',        // Valida formato de correo
                Rule::unique('alumnos_pre', 'correo')->ignore($aspiranteId, 'id'),
                // unique: tabla alumnos_pre, columna correo, ignorando al registro actual
            ],

            // Archivos
            'customFile' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'fotografia' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],

            // Ejemplo para algunos checkboxes
            'chk_bolsa' => ['nullable', 'boolean'],
            'trabajo'   => ['nullable', 'boolean'],

            // Agrega el resto de campos que uses en el método...
        ];
    }

    public function messages(): array
    {
        return [
            // -----------------------------
            // Correo electrónico
            // -----------------------------
            'correo.email'  => 'Ingrese un correo electrónico válido.',
            'correo.unique' => 'El correo ya está siendo utilizado por otro aspirante.',

            // -----------------------------
            // Datos personales
            // -----------------------------
            'curp.required' => 'La CURP es obligatoria.',
            'curp.size'     => 'La CURP debe contener exactamente 18 caracteres.',

            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max'      => 'El nombre no puede exceder 100 caracteres.',

            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.max'      => 'El apellido paterno no puede exceder 100 caracteres.',

            'apellido_materno.max' => 'El apellido materno no puede exceder 100 caracteres.',

            'nacionalidad.required' => 'La nacionalidad es obligatoria.',
            'nacionalidad.min'      => 'La nacionalidad debe contener al menos 2 caracteres.',

            'fecha.required' => 'La fecha de nacimiento es obligatoria.',
            'sexo.required'  => 'El sexo es obligatorio.',

            // -----------------------------
            // Datos de ubicación
            // -----------------------------
            'estado.required'    => 'El estado es obligatorio.',
            'municipio.required' => 'El municipio es obligatorio.',
            'localidad.required' => 'La localidad es obligatoria.',

            // -----------------------------
            // Datos sociodemográficos
            // -----------------------------
            'estado_civil.required' => 'El estado civil es obligatorio.',
            'ultimo_grado_estudios.required' =>
                'El último grado de estudios es obligatorio.',

            'medio_entero.required' =>
                'Debe seleccionar el medio por el cual se enteró.',

            'motivos_eleccion_sistema_capacitacion.required' =>
                'Debe indicar los motivos por los cuales eligió este sistema de capacitación.',

            // -----------------------------
            // Archivos
            // -----------------------------
            'customFile.file'  => 'El archivo subido no es válido.',
            'customFile.mimes' => 'Solo se permiten archivos PDF o imágenes (pdf, jpg, jpeg, png).',
            'customFile.max'   => 'El archivo no debe superar los 5 MB.',

            'fotografia.image' => 'El archivo debe ser una imagen.',
            'fotografia.mimes' => 'Solo se permiten imágenes en formato JPG o PNG.',
            'fotografia.max'   => 'La fotografía no debe superar los 4 MB.',

            // -----------------------------
            // Checkboxes
            // -----------------------------
            'chk_bolsa.boolean' => 'El valor de bolsa de trabajo es inválido.',
            'trabajo.boolean'   => 'El valor de trabajo es inválido.',
        ];
    }

}
