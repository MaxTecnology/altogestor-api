<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitacaoDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empresa_id' => ['required'],
            'modelo_documento_id' => ['required'],
            'competencia' => ['nullable', 'string', 'max:20'],
            'observacao' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'empresa_id' => ['description' => 'ID interno da empresa (ou public_id).', 'example' => 1],
            'modelo_documento_id' => ['description' => 'ID interno do modelo de documento (ou public_id).', 'example' => 1],
            'competencia' => ['description' => 'Competência no formato YYYY-MM.', 'example' => '2025-10'],
            'observacao' => ['description' => 'Observações adicionais.', 'example' => 'Favor enviar até dia 10.'],
        ];
    }
}
