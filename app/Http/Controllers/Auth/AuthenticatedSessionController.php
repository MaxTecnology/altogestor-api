<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
     * @bodyParam email string required Example: socio@demo.local
     * @bodyParam password string required Example: password
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);
        $tenantManager = app(TenantManager::class);
        $tenantId = $tenantManager->getTenantId();
        $tenantPublicId = $tenantManager->getTenantPublicId();
        $user = null;

        if ($tenantId !== null) {
            $user = Auth::getProvider()->retrieveByCredentials([
                ...$credentials,
                'tenant_id' => $tenantId,
            ]);
        } else {
            $users = \App\Models\User::query()->where('email', $credentials['email'])->get();
            if ($users->isEmpty()) {
                abort(401, 'invalid_credentials');
            }
            if ($users->count() > 1) {
                throw new HttpException(400, 'tenant_required');
            }
            $user = $users->first();
            $tenantId = (int) $user->tenant_id;
            $tenantManager->setTenantId($tenantId);
            $tenantPublicId = optional($user->tenant)->public_id;
            if ($tenantPublicId) {
                $tenantManager->setTenantPublicId($tenantPublicId);
            }
        }

        if (! $user || ! Hash::check($credentials['password'], $user->getAuthPassword())) {
            abort(401, 'invalid_credentials');
        }

        $tenantManager->setTenantId($tenantId);
        if (! $tenantPublicId && $user->relationLoaded('tenant') === false) {
            $user->load('tenant');
            $tenantPublicId = optional($user->tenant)->public_id;
        }

        $token = $user->createToken('api', ['tenant_id' => $tenantId])->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'tenant_id' => $tenantId,
            'tenant_public_id' => $tenantPublicId,
        ], 200);
    }

    /**
     * Logout
     *
     * @group Auth
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
