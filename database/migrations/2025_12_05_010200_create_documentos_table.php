<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('solicitacao_documento_id')->constrained('solicitacoes_documentos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // quem enviou
            $table->string('nome_original');
            $table->string('caminho'); // path no storage
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('tamanho_bytes')->nullable();
            $table->string('origem')->default('portal'); // portal, importacao, onedrive, api
            $table->string('status')->default('ENVIADO'); // ENVIADO, EM_VALIDACAO, COMPLETO, INCOMPLETO, RECUSADO
            $table->timestamps();

            $table->unique(['tenant_id', 'public_id']);
            $table->index(['tenant_id', 'solicitacao_documento_id']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
