<?php

namespace App\Http\Controllers\Documentos;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreModeloDocumentoRequest;
use App\Models\ModeloDocumento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModeloDocumentoController extends Controller
{
    /**
     * Lista modelos de documento do tenant.
     *
     * @group Documentos
     * @header X-Tenant-ID string required Public ID do tenant.
     */
    public function index(Request $request): JsonResponse
    {
        $modelos = ModeloDocumento::query()
            ->orderBy('nome')
            ->get([
                'id',
                'public_id',
                'nome',
                'descricao',
                'departamento',
                'periodicidade',
                'obrigatorio',
                'exige_periodo',
            ]);

        return response()->json(['data' => $modelos]);
    }

    /**
     * Cria modelo de documento.
     *
     * @group Documentos
     * @header X-Tenant-ID string required Public ID do tenant.
     */
    public function store(StoreModeloDocumentoRequest $request): JsonResponse
    {
        $modelo = ModeloDocumento::query()->create($request->validated());

        return response()->json(['data' => $modelo], 201);
    }
}
