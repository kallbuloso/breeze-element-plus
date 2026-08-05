# ADR 0001: Tenancy opcional por linhas no instalador

- **Status:** Aceito
- **Data:** 2026-08-01

## Contexto

O aplicativo SofiasLyder usa tenancy compartilhada por banco de dados: o usuário autenticado possui `tenant_id`; `TenantScope` restringe modelos opt-in; e `BelongsToTenant` preenche `tenant_id` na criação. A inspeção do seu `composer.json` não encontrou um pacote externo de tenancy: `spatie/laravel-permission` é usado para permissões, não para resolver tenancy.

A implementação de SofiasLyder também contém onboarding, perfil empresarial, endereços, telefones, políticas, permissões Spatie, seleção de contas no login e reset de senha por tenant. Esses recursos são específicos do produto e não pertencem ao starter kit.

O instalador do pacote executa `migrate:fresh --seed` ao fim das stacks Vue e API. Portanto, os stubs e migrações opcionais precisam ser copiados depois dos stubs de autenticação e antes da migração/seeding final.

## Decisão

`breeze-element-plus:install` terá a opção `--tenancy=single|multi` e, quando interativo, perguntará **“Application single or multitenancy?”**. O modo padrão é `single`, inclusive em execução não interativa.

No modo `multi`, o pacote copiará código próprio e mínimo:

- migration `tenants`;
- `App\Models\Tenant` e sua factory;
- `App\Scopes\TenantScope`;
- `App\Traits\BelongsToTenant`;
- variantes de User, factory, migration e seeder com `tenant_id`.

Nenhum pacote Composer de tenancy será instalado. A primeira versão não copiará onboarding, UI, middleware de perfil, policies, Spatie, configurações, variáveis de ambiente ou rotas específicas do SofiasLyder.

## Consequências

- O modo `single` continua compatível com a instalação atual e mantém `users.email` globalmente único.
- O modo `multi` usa `unique(['email', 'tenant_id'])` e exige uma futura definição de contexto de tenant para login e reset de senha caso e-mails repetidos entre tenants sejam permitidos em produção.
- Apenas modelos que declarem `use BelongsToTenant;` serão filtrados automaticamente.
- O suporte a `vue --tenancy=multi` e `api --tenancy=multi` só será declarado depois de testes em consumidores limpos; o instalador deve falhar antes de alterações se uma combinação não for suportada.
