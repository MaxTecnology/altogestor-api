# Qualidade & CI/CD

## Ferramentas

- Pint (formatador)
- PHPStan (nível 5 ou 6)
- PHPUnit

## Pipeline

1. Lint
2. Static Analysis
3. Unit Tests
4. Feature Tests
5. API Contract Tests
6. Build
7. Deploy

## Deploy Seguro

- Zero-downtime
- Migrations seguras:
  - evitar alterar colunas gigantes
  - usar migrações em 2 etapas
- Feature flags para funcionalidades novas
