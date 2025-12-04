
---

# **07-frontend.md**

```md
# Frontend

## Stack

- Blade
- Livewire
- Vite
- Tailwind CSS

## Filosofia

- Entregar rápido, consistente, simples.
- Migrar para Inertia/SPA no futuro se necessário.

## Design System

Criar componentes:
- `<x-layout>`
- `<x-card>`
- `<x-table>`
- `<x-button>`
- `<x-modal>`

## Contexto de Empresa/Tenant

Header deve exibir:
- Tenant atual
- Empresa ativa
- Dropdown de troca de empresa

## Visibilidade via RBAC

Controller → Policy → Component Livewire

Regras devem usar:
- `tenant_id`
- `empresa_id`
- permissões da empresa ativa
