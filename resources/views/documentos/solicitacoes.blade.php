<x-layouts.app-shell title="Solicitações de Documentos" active="solicitacoes">
    <div class="stack">
        <div class="card">
            <p class="text-muted">Nova solicitação</p>
            <form id="solicitacaoForm" class="stack" style="margin-top: 12px;">
                <div class="grid" style="display:grid; gap:12px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                    <div>
                        <p class="title-sm">Empresa *</p>
                        <select name="empresa_id" id="empresaSelect" class="form-control" required></select>
                    </div>
                    <div>
                        <p class="title-sm">Modelo *</p>
                        <select name="modelo_documento_id" id="modeloSelect" class="form-control" required></select>
                    </div>
                    <div>
                        <p class="title-sm">Competência</p>
                        <input type="month" name="competencia" class="form-control">
                    </div>
                </div>
                <div>
                    <p class="title-sm">Observação</p>
                    <textarea name="observacao" class="form-control" rows="2" placeholder="Ex: enviar até dia 10."></textarea>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <span id="solicitacaoFormStatus" class="text-muted"></span>
                    <button type="submit" class="btn-primary" style="margin-left:auto;">Criar solicitação</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                <div>
                    <p class="title-sm">Solicitações</p>
                    <p class="text-muted" id="solicitacoesCount">-</p>
                </div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <select id="statusFilter" class="form-control" style="width:auto;">
                        <option value="">Todos status</option>
                        <option value="PENDENTE">Pendente</option>
                        <option value="PARCIAL">Parcial</option>
                        <option value="INCOMPLETO">Incompleto</option>
                        <option value="RECUSADO">Recusado</option>
                        <option value="APROVADO">Aprovado</option>
                    </select>
                    <button id="reloadSolicitacoes" class="btn-primary" type="button" style="padding:10px 14px;">Recarregar</button>
                </div>
            </div>
            <div id="solicitacoesList" class="stack" style="margin-top: 14px;"></div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('ag_token');
        const tenant = localStorage.getItem('ag_tenant_public_id');
        if (!token || !tenant) {
            window.location.href = '/login';
        }
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            'X-Tenant-ID': tenant
        };

        const empresaSelect = document.getElementById('empresaSelect');
        const modeloSelect = document.getElementById('modeloSelect');
        const solicitacaoForm = document.getElementById('solicitacaoForm');
        const solicitacaoFormStatus = document.getElementById('solicitacaoFormStatus');
        const solicitacoesList = document.getElementById('solicitacoesList');
        const solicitacoesCount = document.getElementById('solicitacoesCount');
        const statusFilter = document.getElementById('statusFilter');
        const reloadSolicitacoesBtn = document.getElementById('reloadSolicitacoes');

        async function loadEmpresas() {
            const res = await fetch('/api/companies', { headers });
            if (!res.ok) {
                empresaSelect.innerHTML = '<option>Erro ao carregar</option>';
                return [];
            }
            const data = await res.json();
            const items = data.data || [];
            empresaSelect.innerHTML = items.map((e) => `<option value="${e.id}">${e.nome_fantasia || e.razao_social} (${e.cnpj})</option>`).join('');
            return items;
        }

        async function loadModelos() {
            const res = await fetch('/api/documentos/modelos', { headers });
            if (!res.ok) {
                modeloSelect.innerHTML = '<option>Erro ao carregar</option>';
                return [];
            }
            const data = await res.json();
            const items = data.data || [];
            modeloSelect.innerHTML = items.map((m) => `<option value="${m.id}">${m.nome}</option>`).join('');
            return items;
        }

        async function loadSolicitacoes() {
            solicitacoesList.innerHTML = '<p class="text-muted">Carregando...</p>';
            const query = statusFilter.value ? `?status=${encodeURIComponent(statusFilter.value)}` : '';
            const res = await fetch(`/api/documentos/solicitacoes${query}`, { headers });
            if (!res.ok) {
                solicitacoesList.innerHTML = '<p class="text-muted">Erro ao carregar solicitações.</p>';
                return;
            }
            const data = await res.json();
            const itens = data.data ? (Array.isArray(data.data) ? data.data : data.data.data || []) : [];
            solicitacoesCount.textContent = `${itens.length} solicitação(ões)`;
            renderSolicitacoes(itens);
        }

        function renderSolicitacoes(list) {
            if (!list.length) {
                solicitacoesList.innerHTML = '<p class="text-muted">Nenhuma solicitação encontrada.</p>';
                return;
            }
            solicitacoesList.innerHTML = '';
            list.forEach((s) => {
                const card = document.createElement('div');
                card.className = 'card-muted';
                const empresa = s.empresa ? (s.empresa.nome_fantasia || s.empresa.razao_social) : '-';
                const modelo = s.modelo ? s.modelo.nome : '-';
                card.innerHTML = `
                    <div style="display:flex; justify-content:space-between; gap:12px;">
                        <div>
                            <p class="title-sm" style="margin:0;">${modelo}</p>
                            <p class="text-muted" style="margin:4px 0 0;">Empresa: ${empresa}</p>
                            <p class="text-muted" style="margin:4px 0 0;">Competência: ${s.competencia || '-'}</p>
                            <p class="text-muted" style="margin:4px 0 0; font-size:12px;">${s.observacao || ''}</p>
                        </div>
                        <span class="status-pill" data-status="${s.status || ''}">${s.status || '—'}</span>
                    </div>
                `;
                solicitacoesList.appendChild(card);
            });
        }

        solicitacaoForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            solicitacaoFormStatus.textContent = 'Enviando...';
            const formData = new FormData(solicitacaoForm);
            const competencia = formData.get('competencia');
            const payload = {
                empresa_id: formData.get('empresa_id'),
                modelo_documento_id: formData.get('modelo_documento_id'),
                competencia: competencia || null,
                observacao: formData.get('observacao'),
            };
            try {
                const res = await fetch('/api/documentos/solicitacoes', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!res.ok) {
                    solicitacaoFormStatus.textContent = data.message || 'Erro ao criar solicitação';
                    return;
                }
                solicitacaoForm.reset();
                solicitacaoFormStatus.textContent = 'Criada com sucesso!';
                await loadSolicitacoes();
            } catch (err) {
                console.error(err);
                solicitacaoFormStatus.textContent = 'Falha de rede';
            }
        });

        reloadSolicitacoesBtn.addEventListener('click', loadSolicitacoes);
        statusFilter.addEventListener('change', loadSolicitacoes);

        (async function init() {
            await Promise.all([loadEmpresas(), loadModelos()]);
            await loadSolicitacoes();
        })();
    </script>
</x-layouts.app-shell>
