<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Tenancy\TenantManager;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RegisteredUserController extends Controller
{
    /**
     * Registrar usuário
     *
     * @unauthenticated
     * @group Auth
     * @header X-Tenant-ID string required Public ID do tenant. Exemplo: 019aea83-a7be-7d5a-8106-710b80fc9a49
     * @bodyParam name string required Exemplo: Sócio Admin
     * @bodyParam email string required Exemplo: socio@demo.local
     * @bodyParam password string required Exemplo: password
     * @bodyParam password_confirmation string required Exemplo: password
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $tenantId = app(TenantManager::class)->getTenantId();
        if ($tenantId === null) {
            throw new HttpException(400, 'tenant_required');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->where('tenant_id', $tenantId),
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return response()->noContent();
    }
}
