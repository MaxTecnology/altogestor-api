<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('public_id');
            $table->string('name');
            $table->string('guard_name');
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'name', 'guard_name']);
            $table->unique(['tenant_id', 'public_id']);
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('public_id');
            $table->string('name');
            $table->string('guard_name');
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'name', 'guard_name']);
            $table->unique(['tenant_id', 'public_id']);
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->primary(['permission_id', 'role_id', 'tenant_id']);
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->morphs('model');
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->primary(['permission_id', 'model_id', 'model_type', 'tenant_id'], 'model_has_permissions_primary');
            $table->index(['model_id', 'model_type', 'tenant_id'], 'model_has_permissions_model_id_model_type_tenant_id_index');
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->morphs('model');
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->primary(['role_id', 'model_id', 'model_type', 'tenant_id'], 'model_has_roles_primary');
            $table->index(['model_id', 'model_type', 'tenant_id'], 'model_has_roles_model_id_model_type_tenant_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
