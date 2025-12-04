<?php

namespace App\Policies;

use App\Models\ModeloDocumento;
use App\Models\User;

class ModeloDocumentoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ModeloDocumento $modelo): bool
    {
        return (int) $user->tenant_id === (int) $modelo->tenant_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ModeloDocumento $modelo): bool
    {
        return (int) $user->tenant_id === (int) $modelo->tenant_id;
    }

    public function delete(User $user, ModeloDocumento $modelo): bool
    {
        return (int) $user->tenant_id === (int) $modelo->tenant_id;
    }
}
