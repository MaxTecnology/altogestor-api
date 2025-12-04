<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guia_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('guia_id')->constrained('guias')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'guia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guia_historicos');
    }
};
