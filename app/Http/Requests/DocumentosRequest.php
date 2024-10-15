<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentosRequest extends FormRequest
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
            'document.*' => 'required|file|mimes:pdf,doc,docx,png,jpeg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'document.*.required' => 'El archivo del documento es obligatorio.',
            'document.*.mimes' => 'El archivo debe ser un archivo de tipo: pdf, doc, docx, png, jpeg.',
            'document.*.max' => 'El tamaño del archivo no puede exceder los 2 MB.',
        ];
    }
}
