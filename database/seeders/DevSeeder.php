<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Tenancy\TenantManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->firstOrCreate(
            ['slug' => 'altogestor-demo'],
            ['nome' => 'AltoGestor Demo Contabilidade']
        );
        if (! $tenant->public_id) {
            $tenant->public_id = (string) \Symfony\Component\Uid\UuidV7::generate();
            $tenant->save();
        }

        app(TenantManager::class)->setTenantId($tenant->id);

        $empresas = [
            [
                'razao_social' => 'Empresa Demo 1 LTDA',
                'nome_fantasia' => 'Demo 1',
                'cnpj' => '00.000.000/0001-01',
            ],
            [
                'razao_social' => 'Empresa Demo 2 LTDA',
                'nome_fantasia' => 'Demo 2',
                'cnpj' => '00.000.000/0002-02',
            ],
        ];

        /** @var User $user */
        $user = User::query()->updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'email' => 'socio@demo.local',
            ],
            [
                'name' => 'Sócio Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        foreach ($empresas as $empresaData) {
            $empresa = Empresa::query()->firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'cnpj' => $empresaData['cnpj'],
                ],
                [
                    ...$empresaData,
                    'timezone' => 'America/Maceio',
                ]
            );

            DB::table('empresa_user')->updateOrInsert(
                [
                    'tenant_id' => $tenant->id,
                    'empresa_id' => $empresa->id,
                    'user_id' => $user->id,
                ],
                [
                    'perfil' => 'socio_admin',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // Roles e permissões iniciais (escopo por tenant)
        $this->seedRoles($tenant->id, $user);
    }

    protected function seedRoles(int $tenantId, User $user): void
    {
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId($tenantId);

        $roles = [
            'socio_admin',
            'gestor',
            'analista_fiscal',
            'analista_contabil',
            'analista_dp',
            'colaborador_visualizacao',
            'cliente_admin',
            'cliente_financeiro',
            'cliente_basico',
        ];

        foreach ($roles as $roleName) {
            Role::query()->firstOrCreate(
                [
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'tenant_id' => $tenantId,
                ]
            );
        }

        $user->assignRole('socio_admin');

        DB::table('empresa_user')
            ->where('tenant_id', $tenantId)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);
    }
}
