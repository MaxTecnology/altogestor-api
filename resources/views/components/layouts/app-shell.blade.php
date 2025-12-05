@props([
    'title' => 'AltoGestor',
    'active' => null,
])

<x-layouts.base :title="$title">
    <div class="app-shell-root">
        <div class="app-shell--layout">
            <aside class="app-sidebar">
                <div class="app-sidebar__header">
                    <a href="/app" class="app-brand">
                        <img src="/assets/logo.png" alt="AltoGestor" class="app-brand__logo">
                        <div>
                            <span class="app-brand__title">AltoGestor</span>
                            <span class="app-brand__subtitle">Painel</span>
                        </div>
                    </a>
                    <div class="app-sidebar__tenant">
                        <p class="text-muted">Contabilidade</p>
                        <p id="tenantLabel" class="tenant-name">—</p>
                    </div>
                </div>
                <nav class="app-sidebar__nav">
                    <a href="/app" class="nav-item {{ $active === 'dashboard' ? 'is-active' : '' }}">Dashboard</a>
                    <div class="nav-group">
                        <p>Documentos</p>
                        <a href="/app/documentos/modelos" class="{{ $active === 'modelos' ? 'is-active' : '' }}">Modelos</a>
                        <a href="/app/documentos/solicitacoes" class="{{ $active === 'solicitacoes' ? 'is-active' : '' }}">Solicitações</a>
                    </div>
                </nav>
                <div class="app-sidebar__footer">
                    <button type="button" id="logoutNav" class="app-nav__logout">Sair</button>
                </div>
            </aside>
            <section class="app-content">
                {{ $slot }}
            </section>
        </div>
    </div>

    <script>
        (async function() {
            const token = localStorage.getItem('ag_token');
            const tenantPublicId = localStorage.getItem('ag_tenant_public_id');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const tenantLabel = document.getElementById('tenantLabel');
            const logout = document.getElementById('logoutNav');

            if (logout) {
                logout.addEventListener('click', () => {
                    localStorage.removeItem('ag_token');
                    localStorage.removeItem('ag_tenant_public_id');
                    window.location.href = '/login';
                });
            }

            // Busca nome da contabilidade (tenant) para exibir no menu
            try {
                const res = await fetch('/api/me', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        ...(tenantPublicId ? { 'X-Tenant-ID': tenantPublicId } : {}),
                    },
                });
                if (res.ok) {
                    const data = await res.json();
                    const nomeTenant = data?.user?.tenant?.nome || tenantPublicId || 'Contabilidade';
                    if (tenantLabel) tenantLabel.textContent = nomeTenant;
                } else {
                    if (tenantLabel) tenantLabel.textContent = tenantPublicId || 'Contabilidade';
                }
            } catch (_) {
                if (tenantLabel) tenantLabel.textContent = tenantPublicId || 'Contabilidade';
            }
        })();
    </script>
</x-layouts.base>
