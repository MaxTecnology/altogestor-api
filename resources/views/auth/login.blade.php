<x-layouts.base title="Login">
    <div class="container stack" style="margin-top: 24px;">
        <div class="card" style="display:flex; align-items:center; gap:20px;">
            <img src="/assets/logo.svg" alt="AltoGestor" style="height:120px; width:auto; border-radius:16px; background:rgba(0,0,0,0.05); padding:8px; box-shadow:0 18px 40px rgba(0,0,0,0.25);">
            <div>
                <p class="text-muted" style="margin:0 0 4px;">Bem-vindo</p>
                <h1 class="title-xl" style="margin:0;">Acesso ao painel</h1>
                <p class="text-muted" style="margin:6px 0 0;">Tecnologia que impulsiona grandes negócios.</p>
            </div>
        </div>

        <div class="card stack">
            <div>
                <p class="text-muted">Login</p>
                <h2 class="title-xl" style="font-size:20px;">Use seu e-mail e senha para obter o token e entrar.</h2>
            </div>
            <form id="loginForm" class="stack">
                <div>
                    <p class="title-sm">E-mail</p>
                    <input name="email" id="email" type="email" class="form-control" placeholder="socio@demo.local" required>
                </div>
                <div>
                    <p class="title-sm">Senha</p>
                    <input name="password" id="password" type="password" class="form-control" placeholder="password" required>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <button type="submit" class="btn-primary">Entrar</button>
                    <p id="loginStatus" class="text-muted text-sm"></p>
                </div>
            </form>
        </div>

        <div class="card-muted">
            <p class="title-sm">Dicas rápidas</p>
            <ul class="text-muted" style="margin:0; padding-left: 16px;">
                <li>Login seed: socio@demo.local / password (tenant demo).</li>
                <li>Após login, redirecionamos para /app com token salvo.</li>
            </ul>
        </div>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const statusEl = document.getElementById('loginStatus');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            statusEl.textContent = 'Autenticando...';

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await res.json();
                if (!res.ok) {
                    let message = 'Erro ao autenticar';
                    const apiMessage = data?.message ?? '';
                    message = apiMessage === 'invalid_credentials'
                        ? 'E-mail ou senha incorretos.'
                        : (apiMessage === 'tenant_required'
                            ? 'Informe o tenant (X-Tenant-ID) porque este e-mail existe em mais de um tenant.'
                            : (apiMessage || message));
                    statusEl.textContent = message;
                    console.error('Login error', data);
                    return;
                }

                const token = data.token;
                const tenantPublicId = data.tenant_public_id ?? null;
                localStorage.setItem('ag_token', token);
                if (tenantPublicId) {
                    localStorage.setItem('ag_tenant_public_id', tenantPublicId);
                }
                statusEl.textContent = 'Login ok! Redirecionando...';
                window.location.href = '/app';
            } catch (err) {
                console.error(err);
                statusEl.textContent = 'Falha de rede';
            }
        });
    </script>
</x-layouts.base>
