<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModeloDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:500'],
            'departamento' => ['nullable', 'string', 'max:100'],
            'periodicidade' => ['nullable', 'string', 'max:50'],
            'obrigatorio' => ['boolean'],
            'exige_periodo' => ['boolean'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'nome' => ['description' => 'Nome do modelo de documento.', 'example' => 'XML de saída'],
            'descricao' => ['description' => 'Descrição do modelo.', 'example' => 'XML mensal de notas emitidas'],
            'departamento' => ['description' => 'Departamento responsável.', 'example' => 'fiscal'],
            'periodicidade' => ['description' => 'Periodicidade (mensal, anual, único).', 'example' => 'mensal'],
            'obrigatorio' => ['description' => 'Se é obrigatório.', 'example' => true],
            'exige_periodo' => ['description' => 'Se exige competência/período.', 'example' => true],
        ];
    }
}
