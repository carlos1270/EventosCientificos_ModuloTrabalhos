<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificadoRequest extends FormRequest
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
            'local'              => 'required|string|min:3|max:40',
            'nome'              => 'required|string|min:5|max:290',
            'texto'              => 'required|string|min:5|max:500',
            'tipo'              => 'required',
            'fotoCertificado'  => 'required|file|mimes:png,jpeg,jpg|max:2048',
            'assinaturas' => 'required',
            'data' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'assinaturas.required'     => "Selecione ao menos uma assiinatura para o certificado",
            'fotoCertificado.required'     => "A imagem do certificado é obrigatória",
            'fotoCertificado.max'          => "A imagem do certificado deve ter no máximo 2MB",
            'fotoCertificado.mimes'        => "A imagem do certificado deve ser em um dos formatos permitidos",
        ];
    }
}
