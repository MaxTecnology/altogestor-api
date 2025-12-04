<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprovantes_pagamento', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('guia_id')->constrained('guias')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nome_original');
            $table->string('caminho');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('tamanho_bytes')->nullable();
            $table->string('status')->default('ENVIADO'); // ENVIADO, VALIDADO, RECUSADO
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'public_id']);
            $table->index(['tenant_id', 'guia_id']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprovantes_pagamento');
    }
};
