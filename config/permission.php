<?php

return [

    'models' => [
        'permission' => App\Models\Permission::class,
        'role' => App\Models\Role::class,
    ],

    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        'team_foreign_key' => 'tenant_id',
    ],

    'display_permission_in_exception' => false,

    'display_role_in_exception' => false,

    'enable_wildcard_permission' => false,

    'teams' => true,

    'cache' => [
        'expiration_time' => now()->addDay(),
        'key' => 'spatie.permission.cache',
        'store' => 'default',
    ],
];
