<?php

namespace App\Policies;

use App\Models\SolicitacaoDocumento;
use App\Models\User;

class SolicitacaoDocumentoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SolicitacaoDocumento $solicitacao): bool
    {
        return (int) $user->tenant_id === (int) $solicitacao->tenant_id
            && $user->empresas()->where('empresas.id', $solicitacao->empresa_id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, SolicitacaoDocumento $solicitacao): bool
    {
        return $this->view($user, $solicitacao);
    }

    public function delete(User $user, SolicitacaoDocumento $solicitacao): bool
    {
        return $this->view($user, $solicitacao);
    }
}
