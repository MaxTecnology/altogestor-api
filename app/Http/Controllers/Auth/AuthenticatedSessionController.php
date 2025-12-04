<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Login (Sanctum token)
     *
     * @unauthenticated
     * @group Auth
     * @header X-Tenant-ID string required Public ID do tenant. Exemplo: 019aea83-a7be-7d5a-8106-710b80fc9a49
     * @bodyParam email string required Example: socio@demo.local
     * @bodyParam password string required Example: password
     */
    public function store(LoginRequest $request): Response
    {
        $tenantId = app(TenantManager::class)->getTenantId();
        if ($tenantId === null) {
            throw new HttpException(400, 'tenant_required');
        }

        $credentials = $request->only(['email', 'password']);

        $user = $request->user()
            ?? Auth::getProvider()->retrieveByCredentials([
                ...$credentials,
                'tenant_id' => $tenantId,
            ]);

        if (! $user || (int) $user->tenant_id !== $tenantId || ! Hash::check($credentials['password'], $user->getAuthPassword())) {
            abort(401, 'invalid_credentials');
        }

        app(TenantManager::class)->setTenantId((int) $user->tenant_id);

        $token = $user->createToken('api', ['tenant_id' => $tenantId])->plainTextToken;

        return response($token, 200);
    }

    /**
     * Logout
     *
     * @group Auth
     */
    public function destroy(Request $request): Response
    {
        $request->user()->currentAccessToken()?->delete();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
