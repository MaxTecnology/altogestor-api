<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('config_obrigacoes_empresas', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('tipo_obrigacao_id')->constrained('tipos_obrigacoes')->cascadeOnDelete();
            $table->unsignedTinyInteger('vencimento_dia')->nullable(); // dia do mês
            $table->boolean('gera_guia_automatica')->default(true);
            $table->foreignId('responsavel_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'public_id']);
            $table->unique(['tenant_id', 'empresa_id', 'tipo_obrigacao_id']);
            $table->index(['tenant_id', 'empresa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config_obrigacoes_empresas');
    }
};
