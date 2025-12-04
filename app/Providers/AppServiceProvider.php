<?php

namespace App\Providers;

use App\Support\Tenancy\TenantManager;
use App\Models\ModeloDocumento;
use App\Models\SolicitacaoDocumento;
use App\Models\Documento;
use App\Policies\ModeloDocumentoPolicy;
use App\Policies\SolicitacaoDocumentoPolicy;
use App\Policies\DocumentoPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantManager::class, fn () => new TenantManager());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Gate::policy(ModeloDocumento::class, ModeloDocumentoPolicy::class);
        Gate::policy(SolicitacaoDocumento::class, SolicitacaoDocumentoPolicy::class);
        Gate::policy(Documento::class, DocumentoPolicy::class);
    }
}
