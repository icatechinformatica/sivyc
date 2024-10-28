<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Rf001StoreRequest extends FormRequest
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
            'consecutivo' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'consecutivo' => 'memorandum'
        ];
    }

    public function messages()
    {
        return [
            'consecutivo.required' => 'El :attribute es obligatorio'
        ];
    }
}
