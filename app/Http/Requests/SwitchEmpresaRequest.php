<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SwitchEmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empresa_id' => ['required', 'integer', 'min:1'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'empresa_id' => [
                'description' => 'ID interno da empresa (ou public_id, se o front já tiver traduzido).',
                'example' => 1,
            ],
        ];
    }
}
