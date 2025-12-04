<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'arquivo' => ['required', 'file', 'max:51200'], // ~50MB
            'observacao' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'arquivo' => [
                'description' => 'Arquivo a ser enviado.',
                'example' => null,
            ],
            'observacao' => [
                'description' => 'Observação opcional.',
                'example' => 'Envio do mês de outubro.',
            ],
        ];
    }
}
