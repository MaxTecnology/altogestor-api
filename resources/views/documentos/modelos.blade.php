<x-layouts.app-shell title="Modelos de Documentos" active="modelos">
    <div class="stack">
        <div class="card">
            <p class="text-muted">Criar modelo</p>
            <form id="modeloForm" class="stack" style="margin-top: 12px;">
                <div class="grid" style="display:grid; gap:12px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                    <div>
                        <p class="title-sm">Nome *</p>
                        <input type="text" name="nome" class="form-control" placeholder="XML de saída" required>
                    </div>
                    <div>
                        <p class="title-sm">Departamento</p>
                        <input type="text" name="departamento" class="form-control" placeholder="fiscal / contábil / dp">
                    </div>
                    <div>
                        <p class="title-sm">Periodicidade</p>
                        <input type="text" name="periodicidade" class="form-control" placeholder="mensal / anual / único">
                    </div>
                </div>
                <div>
                    <p class="title-sm">Descrição</p>
                    <textarea name="descricao" class="form-control" rows="2" placeholder="Detalhes do que precisa ser enviado."></textarea>
                </div>
                <div style="display:flex; gap:16px; align-items:center;">
                    <label style="display:flex; gap:6px; align-items:center;">
                        <input type="checkbox" name="obrigatorio"> Obrigatório
                    </label>
                    <label style="display:flex; gap:6px; align-items:center;">
                        <input type="checkbox" name="exige_periodo"> Exige competência/período
                    </label>
                    <span id="modeloFormStatus" class="text-muted" style="margin-left:auto;"></span>
                    <button type="submit" class="btn-primary">Salvar modelo</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                <div>
                    <p class="title-sm">Modelos existentes</p>
                    <p class="text-muted" id="modelosCount">-</p>
                </div>
                <button type="button" id="reloadModelos" class="btn-primary" style="padding:10px 14px;">Recarregar</button>
            </div>
            <div id="modelosList" class="stack" style="margin-top: 14px;"></div>
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

        const modelosList = document.getElementById('modelosList');
        const modelosCount = document.getElementById('modelosCount');
        const form = document.getElementById('modeloForm');
        const formStatus = document.getElementById('modeloFormStatus');
        const reloadBtn = document.getElementById('reloadModelos');

        async function fetchModelos() {
            modelosList.innerHTML = '<p class="text-muted">Carregando...</p>';
            const res = await fetch('/api/documentos/modelos', { headers });
            if (!res.ok) {
                modelosList.innerHTML = '<p class="text-muted">Erro ao carregar modelos.</p>';
                return;
            }
            const data = await res.json();
            const itens = data.data ?? data;
            modelosCount.textContent = `${(itens || []).length} modelo(s)`;
            renderModelos(itens || []);
        }

        function renderModelos(itens) {
            if (!itens.length) {
                modelosList.innerHTML = '<p class="text-muted">Nenhum modelo cadastrado.</p>';
                return;
            }
            modelosList.innerHTML = '';
            itens.forEach((m) => {
                const div = document.createElement('div');
                div.className = 'card-muted';
                div.innerHTML = `
                    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                        <div>
                            <p class="title-sm" style="margin:0;">${m.nome}</p>
                            <p class="text-muted" style="margin:4px 0 0;">${m.descricao || '-'}</p>
                            <p class="text-muted" style="margin:6px 0 0; font-size:12px;">
                                Dept: ${m.departamento || '-'} · Periodicidade: ${m.periodicidade || '-'}
                            </p>
                        </div>
                        <div class="badge">${m.obrigatorio ? 'Obrigatório' : 'Opcional'}</div>
                    </div>
                `;
                modelosList.appendChild(div);
            });
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            formStatus.textContent = 'Salvando...';
            const formData = new FormData(form);
            const payload = {
                nome: formData.get('nome'),
                descricao: formData.get('descricao'),
                departamento: formData.get('departamento'),
                periodicidade: formData.get('periodicidade'),
                obrigatorio: formData.get('obrigatorio') === 'on',
                exige_periodo: formData.get('exige_periodo') === 'on',
            };
            try {
                const res = await fetch('/api/documentos/modelos', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!res.ok) {
                    formStatus.textContent = data.message || 'Erro ao salvar';
                    return;
                }
                form.reset();
                formStatus.textContent = 'Salvo!';
                await fetchModelos();
            } catch (err) {
                console.error(err);
                formStatus.textContent = 'Falha de rede';
            }
        });

        reloadBtn.addEventListener('click', fetchModelos);

        fetchModelos();
    </script>
</x-layouts.app-shell>
