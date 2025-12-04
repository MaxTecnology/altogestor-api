<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('solicitacao_documento_id')->constrained('solicitacoes_documentos')->cascadeOnDelete();
            $table->foreignId('documento_id')->nullable()->constrained('documentos')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'solicitacao_documento_id']);
            $table->index(['tenant_id', 'documento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_historicos');
    }
};
