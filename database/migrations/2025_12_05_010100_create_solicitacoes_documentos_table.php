<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes_documentos', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('modelo_documento_id')->constrained('modelos_documentos')->cascadeOnDelete();
            $table->string('competencia')->nullable(); // ex.: 2025-10
            $table->string('status')->default('PENDENTE'); // PENDENTE, PARCIAL, EM_VALIDACAO, COMPLETO, INCOMPLETO, RECUSADO
            $table->boolean('gerada_automaticamente')->default(false);
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'public_id']);
            $table->index(['tenant_id', 'empresa_id']);
            $table->index(['tenant_id', 'modelo_documento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_documentos');
    }
};
