# API Reference – Autenticação & Usuários (v1)

Este módulo define:

- Autenticação (login/logout/refresh)
- Recuperação e troca de senha
- Gestão de usuários do **escritório**
- Gestão de usuários do **cliente** (portal)
- Conceito de perfis / papéis (roles) básicos

Todos os outros módulos dependem diretamente desta API.

Prefixo padrão:

```http
/ api / v1

1. Conceitos
1.1. Tipos de usuário

A plataforma trabalha com dois tipos principais:

Usuário do Escritório (usuarios_escritorio)

acessa o painel interno (backoffice),

apura obrigações,

valida documentos,

atende pedidos,

envia notificações.

Usuário do Cliente (usuarios_cliente)

acessa o portal do cliente,

envia documentos e comprovantes,

abre pedidos,

visualiza guias.

1.2. Perfis/Papéis (roles)

Sugestão mínima (detalhada depois na matriz de permissões):

Escritório

socio_admin

gestor

analista_fiscal

analista_contabil

analista_dp

colaborador_visualizacao

Cliente

cliente_admin (admin da empresa)

cliente_financeiro

cliente_basico (acesso limitado)

Roles podem ser armazenados em:

campo perfil / role na tabela,

ou tabela de perfis separada (futuro).

2. Autenticação
2.1. Login
POST /auth/login

Realiza login e retorna token de acesso.

Body

{
  "tipo_usuario": "escritorio",
  "email": "carlos@contabilidade.com",
  "senha": "minha-senha"
}


tipo_usuario aceitos:

"escritorio"

"cliente"

Resposta 200

{
  "access_token": "jwt-aqui",
  "refresh_token": "refresh-jwt-aqui",
  "token_type": "Bearer",
  "expires_in": 3600,
  "usuario": {
    "id": 8,
    "tipo_usuario": "escritorio",
    "nome": "Carlos Silva",
    "email": "carlos@contabilidade.com",
    "perfil": "analista_fiscal"
  }
}


Erros comuns

401 – credenciais inválidas

423 (opcional) – usuário bloqueado/inativo

2.2. Refresh Token
POST /auth/refresh

Atualiza o access_token utilizando o refresh_token.

Body

{
  "refresh_token": "refresh-jwt-aqui"
}


Resposta 200

{
  "access_token": "novo-jwt-aqui",
  "expires_in": 3600
}


Erros

401 – refresh token inválido, expirado ou revogado.

2.3. Logout
POST /auth/logout

Invalida o token atual (e opcionalmente o refresh).

Headers

Authorization: Bearer <access_token>


Resposta 204

Sem corpo.

2.4. Solicitar recuperação de senha
POST /auth/solicitar-recuperacao-senha

Envia um e-mail (ou outro canal) com link/código para redefinição.

Body

{
  "email": "usuario@dominio.com",
  "tipo_usuario": "cliente"
}


Resposta 200

{
  "mensagem": "Se este e-mail existir na base, será enviado um link de recuperação."
}


Obs: Sempre retornar resposta genérica para não vazar se o e-mail existe ou não (segurança).

2.5. Resetar senha
POST /auth/resetar-senha

Usa o token de recuperação enviado por e-mail.

Body

{
  "token_recuperacao": "token-que-veio-no-email",
  "nova_senha": "senha-nova-aqui"
}


Resposta 200

{
  "mensagem": "Senha redefinida com sucesso."
}

2.6. Alterar senha (usuário autenticado)
POST /auth/alterar-senha

Headers

Authorization: Bearer <access_token>


Body

{
  "senha_atual": "minha-senha-atual",
  "nova_senha": "nova-senha-forte"
}


Resposta 200

{
  "mensagem": "Senha alterada com sucesso."
}


Erros

400 – senha atual não confere

422 – nova senha não atende critérios

3. Usuários do Escritório
3.1. Listar usuários do escritório
GET /usuarios/escritorio

Query Params (opcional)

Param	Descrição
ativo	true / false
perfil	filtrar por perfil
busca	nome ou e-mail

Resposta

[
  {
    "id": 8,
    "nome": "Carlos Silva",
    "email": "carlos@contabilidade.com",
    "telefone": "(82) 99999-0000",
    "cargo": "Analista Fiscal",
    "perfil": "analista_fiscal",
    "ativo": true
  }
]

3.2. Criar usuário do escritório
POST /usuarios/escritorio

Pode ser feito por um usuário socio_admin ou gestor.

Body

{
  "nome": "Ana Paula",
  "email": "ana@contabilidade.com",
  "telefone": "(82) 98888-0000",
  "cargo": "Analista Contábil",
  "perfil": "analista_contabil"
}


Resposta 201

{
  "id": 15,
  "nome": "Ana Paula",
  "perfil": "analista_contabil",
  "ativo": true
}


A senha inicial pode ser:

gerada automaticamente e enviada por e-mail,

ou exigir um fluxo de “convite/primeiro acesso”.

3.3. Atualizar usuário do escritório
PATCH /usuarios/escritorio/{id}

Body (exemplo)

{
  "telefone": "(82) 97777-0000",
  "cargo": "Coordenador Fiscal",
  "perfil": "gestor"
}


Resposta 200

{
  "id": 8,
  "nome": "Carlos Silva",
  "perfil": "gestor"
}

3.4. Ativar/Desativar usuário do escritório
PATCH /usuarios/escritorio/{id}/status

Body

{
  "ativo": false
}

4. Usuários do Cliente (Portal)
4.1. Listar usuários do cliente de uma empresa
GET /empresas/{empresa_id}/usuarios-cliente

Resposta

[
  {
    "id": 200,
    "nome": "João – Financeiro",
    "email": "financeiro@padariaalfa.com",
    "telefone": "(82) 93333-0000",
    "cargo": "Financeiro",
    "perfil": "cliente_financeiro",
    "ativo": true
  }
]

4.2. Criar usuário cliente vinculado a uma empresa
POST /empresas/{empresa_id}/usuarios-cliente

Criado normalmente por:

usuário do escritório responsável pela empresa,

ou cliente_admin da empresa.

Body

{
  "nome": "João – Financeiro",
  "email": "financeiro@padariaalfa.com",
  "telefone": "(82) 93333-0000",
  "cargo": "Financeiro",
  "perfil": "cliente_financeiro",
  "receber_email_lembrete_impostos": true
}


Resposta 201

{
  "id": 200,
  "empresa_id": 55,
  "perfil": "cliente_financeiro",
  "ativo": true
}


Por baixo dos panos, isso alimenta:

usuarios_cliente

empresa_usuario_cliente (vínculo).

4.3. Atualizar usuário cliente
PATCH /usuarios-cliente/{id}

Body

{
  "telefone": "(82) 94444-0000",
  "perfil": "cliente_admin",
  "receber_email_lembrete_impostos": false
}

4.4. Ativar/Desativar usuário cliente
PATCH /usuarios-cliente/{id}/status

Body

{
  "ativo": false
}

5. Perfil do Usuário Logado
5.1. Obter dados do usuário autenticado
GET /me

Retorna informações do usuário logado.

Headers

Authorization: Bearer <access_token>


Resposta

{
  "id": 8,
  "tipo_usuario": "escritorio",
  "nome": "Carlos Silva",
  "email": "carlos@contabilidade.com",
  "perfil": "analista_fiscal",
  "permissoes": [
    "ver_guias",
    "editar_tarefas",
    "validar_documentos"
  ]
}


A lista de permissoes será detalhada depois na Matriz de Permissões.

6. Códigos de Erro
Código	Significado
400	Request inválido (payload malformado)
401	Não autenticado (token ausente/ inválido)
403	Sem permissão para a operação
404	Usuário não encontrado
409	Conflito (email já em uso, por exemplo)
422	Erro de validação (campos obrigatórios, formato de e-mail, etc.)
500	Erro interno
7. Integração com outros módulos

Documentos

usuario_cliente_id → quem enviou os documentos.

usuario_escritorio_id → quem validou.

Obrigações & Guias

responsável pela empresa (usuarios_escritorio),

destinatários padrão de notificações (usuarios_cliente).

Tarefas & Agenda

responsável por tarefas (usuarios_escritorio).

Pedidos & Formulários

quem abriu (usuarios_cliente),

quem atende (usuarios_escritorio).

Notificações

origem (usuario_escritorio_id),

destino (usuario_cliente_id).