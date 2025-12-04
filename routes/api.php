<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeController;
use App\Http\Controllers\SwitchEmpresaController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\Documentos\ModeloDocumentoController;
use App\Http\Controllers\Documentos\SolicitacaoDocumentoController;
use App\Http\Controllers\Documentos\UploadDocumentoController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->get('/me', [MeController::class, 'show']);
Route::middleware(['auth:sanctum'])->get('/companies', [CompaniesController::class, 'index']);
Route::middleware(['auth:sanctum'])->post('/switch-empresa', SwitchEmpresaController::class);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/documentos/modelos', [ModeloDocumentoController::class, 'index']);
    Route::post('/documentos/modelos', [ModeloDocumentoController::class, 'store']);

    Route::get('/documentos/solicitacoes', [SolicitacaoDocumentoController::class, 'index']);
    Route::post('/documentos/solicitacoes', [SolicitacaoDocumentoController::class, 'store']);
    Route::get('/documentos/solicitacoes/{id}', [SolicitacaoDocumentoController::class, 'show']);
    Route::post('/documentos/solicitacoes/{id}/upload', UploadDocumentoController::class);
});

require __DIR__.'/auth.php';
