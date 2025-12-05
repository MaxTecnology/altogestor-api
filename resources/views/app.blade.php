<x-layouts.app-shell title="Dashboard" active="dashboard">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 stack">
            <div class="card">
                <p class="text-muted">Usuário</p>
                <h1 class="title-xl" id="meName">-</h1>
                <p class="text-muted" id="meEmail">-</p>
                <p class="text-muted">Roles: <span id="meRoles">-</span></p>
                <p class="text-muted">Tenant: <span id="meTenant">-</span></p>
            </div>
            <div class="card">
                <p class="text-muted">Empresas vinculadas</p>
                <ul id="companyList" class="stack" style="margin: 12px 0 0;"></ul>
            </div>
        </div>
        <div class="stack">
            <div class="card">
                <p class="title-sm">Atalhos</p>
                <div class="stack" style="margin-top: 8px;">
                    <a class="link" href="/app/documentos/modelos">Modelos de documentos</a>
                    <a class="link" href="/app/documentos/solicitacoes">Solicitações de documentos</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('ag_token');
        const tenantPublicId = localStorage.getItem('ag_tenant_public_id');
        const headers = {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
            ...(tenantPublicId ? { 'X-Tenant-ID': tenantPublicId } : {}),
        };

        async function fetchMe() {
            const res = await fetch('/api/me', { headers });
            if (!res.ok) throw new Error('Erro ao carregar /me');
            return res.json();
        }

        async function fetchCompanies() {
            const res = await fetch('/api/companies', { headers });
            if (!res.ok) throw new Error('Erro ao carregar empresas');
            return res.json();
        }

        function renderMe(data) {
            document.getElementById('meName').textContent = data.user.name;
            document.getElementById('meEmail').textContent = data.user.email;
            document.getElementById('meRoles').textContent = data.user.roles.join(', ') || '-';
            document.getElementById('meTenant').textContent = data.user.tenant?.nome || '-';
        }

        function renderCompanies(resp) {
            const ul = document.getElementById('companyList');
            ul.innerHTML = '';
            resp.data.forEach((empresa) => {
                const li = document.createElement('li');
                li.className = 'p-3 rounded-xl bg-slate-800/70 text-slate-200 flex items-center justify-between';
                li.innerHTML = `
                    <div>
                        <p class="font-semibold">${empresa.razao_social}</p>
                        <p class="text-sm text-slate-400">${empresa.cnpj} · Perfil: ${empresa.perfil ?? '-'}</p>
                    </div>
                    <button class="text-sm text-sky-400 hover:text-sky-200" data-id="${empresa.id}">Selecionar</button>
                `;
                li.querySelector('button').addEventListener('click', () => switchEmpresa(empresa.id));
                ul.appendChild(li);
            });
        }

        async function switchEmpresa(empresaId) {
            const res = await fetch('/api/switch-empresa', {
                method: 'POST',
                headers: {
                    ...headers,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ empresa_id: empresaId }),
            });
            if (!res.ok) {
                alert('Não foi possível selecionar empresa.');
                return;
            }
            alert('Empresa selecionada!');
        }

        async function init() {
            if (!token) {
                window.location.href = '/login';
                return;
            }
            try {
                const [me, companies] = await Promise.all([fetchMe(), fetchCompanies()]);
                renderMe(me);
                renderCompanies(companies);
            } catch (err) {
                console.error(err);
                window.location.href = '/login';
            }
        }

        init();
    </script>
</x-layouts.app-shell>
