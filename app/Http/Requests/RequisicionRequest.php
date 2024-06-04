<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequisicionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'articulo' => ['required'],
            'cantidad' => ['required', 'numeric'],
            'unidad' => ['required'],

        ];
    }

    public function messages()
    {
        return [
            'articulo.required' => 'El :attribute es obligatorio',
            'cantidad.required' => 'La :attribute es obligatoria',
            'unidad.required' => ':attribute es obligatorio',
            'cantidad.numeric' => 'La :attribute debe ser númerica',
        ];
    }

    public function attributes()
    {
        return [
            'articulo' => 'Artículo',
            'cantidad' => 'Cantidad',
            'unidad' => 'Unidad'
        ];
    }
}
