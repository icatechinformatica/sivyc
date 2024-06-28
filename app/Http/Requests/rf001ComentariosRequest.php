<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class rf001ComentariosRequest extends FormRequest
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
            'observacion' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'observacion' => 'comentario'
        ];
    }

    public function messages()
    {
        return [
            'observacion.required' => 'El :attribute es obligatorio'
        ];
    }
}
