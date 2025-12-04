<?php

namespace App\Policies;

use App\Models\Documento;
use App\Models\User;

class DocumentoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Documento $documento): bool
    {
        return (int) $user->tenant_id === (int) $documento->tenant_id
            && $user->empresas()->where('empresas.id', $documento->solicitacao->empresa_id)->exists();
    }

    public function create(User $user, Documento $documento = null): bool
    {
        return true;
    }

    public function update(User $user, Documento $documento): bool
    {
        return $this->view($user, $documento);
    }

    public function delete(User $user, Documento $documento): bool
    {
        return $this->view($user, $documento);
    }
}
