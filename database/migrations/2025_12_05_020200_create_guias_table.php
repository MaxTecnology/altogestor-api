<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guias', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('tipo_obrigacao_id')->constrained('tipos_obrigacoes')->cascadeOnDelete();
            $table->string('competencia')->nullable(); // YYYY-MM
            $table->date('vencimento')->nullable();
            $table->decimal('valor', 15, 2)->nullable();
            $table->string('status')->default('GERADA_INTERNA'); // GERADA_INTERNA, DISPONIVEL_PORTAL, ENVIADA_CLIENTE, VISUALIZADA_CLIENTE, PAGA, ATRASADA, CANCELADA
            $table->timestamp('enviada_em')->nullable();
            $table->timestamp('paga_em')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'public_id']);
            $table->index(['tenant_id', 'empresa_id']);
            $table->index(['tenant_id', 'tipo_obrigacao_id']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guias');
    }
};
