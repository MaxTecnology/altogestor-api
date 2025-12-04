<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('public_id')->after('id');
            $table->foreignId('tenant_id')->after('public_id')->constrained('tenants')->cascadeOnDelete();
            $table->softDeletes()->after('remember_token');

            // Ajustar unicidade de email para incluir tenant
            $table->dropUnique('users_email_unique');
            $table->unique(['tenant_id', 'email']);

            $table->index(['tenant_id', 'id']);
            $table->index(['tenant_id', 'public_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'id']);
            $table->dropIndex(['tenant_id', 'public_id']);
            $table->dropUnique(['tenant_id', 'email']);
            $table->unique('email');
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn(['public_id']);
            $table->dropSoftDeletes();
        });
    }
};
