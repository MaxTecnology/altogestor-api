<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modelos_documentos', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('nome');
            $table->string('descricao')->nullable();
            $table->string('departamento')->nullable(); // fiscal, contábil, dp etc.
            $table->string('periodicidade')->nullable(); // mensal, anual, único
            $table->boolean('obrigatorio')->default(true);
            $table->boolean('exige_periodo')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'public_id']);
            $table->unique(['tenant_id', 'nome']);
            $table->index(['tenant_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modelos_documentos');
    }
};
