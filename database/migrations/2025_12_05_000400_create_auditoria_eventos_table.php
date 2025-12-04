<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria_eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('entidade');
            $table->unsignedBigInteger('entidade_id');
            $table->string('evento');
            $table->json('dados_antes')->nullable();
            $table->json('dados_depois')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'empresa_id']);
            $table->index(['entidade', 'entidade_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_eventos');
    }
};
