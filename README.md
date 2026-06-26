# Breeze Element Plus

Starter kit Laravel inspirado no Laravel Breeze, com autenticacao via Inertia, Vue e Element Plus.

O pacote instala uma base opinativa para aplicacoes Laravel que usam Vue no frontend, mantendo a simplicidade do Breeze e reaproveitando padroes usados em projetos Kallbuloso.

## Recursos

- Stack `vue` com Inertia, Vue 3, Element Plus, Pinia, Ziggy e Vite.
- Stack `api` para autenticacao API com Sanctum.
- Stubs compartilhados de autenticacao, testes, controllers, requests e middleware.
- Layout autenticado com stores, composables, utilitarios e integracao com Iconify.
- Facade `Toast` para notificacoes flash enviadas pelo backend.
- Tooling com ESLint, Prettier, TypeScript e `vue-tsc`.
- Suporte opcional a SSR para Inertia.
- Migration de usuario com `is_owner`, `profile_photo_path` e `softDeletes`.
- Seeder inicial com usuario de teste.

## Requisitos

- PHP 8.2 ou superior.
- Laravel 11, 12 ou 13.
- Composer.
- Node.js e npm, pnpm, yarn ou bun.

## Instalacao Local

Para testar este pacote em uma aplicacao Laravel antes de publicar no Packagist, adicione um repositorio `path` no `composer.json` da aplicacao de teste:

```json
"repositories": [
  {
    "type": "path",
    "url": "D:/packages/larakits/breeze-element-plus",
    "options": {
      "symlink": true,
      "versions": {
        "kallbuloso/breeze-element-plus": "1.0.0"
      }
    }
  }
]
```

Depois instale o pacote:

```bash
composer require kallbuloso/breeze-element-plus:^1.0 --dev -W
```

Tambem e possivel testar diretamente a branch principal:

```bash
composer require kallbuloso/breeze-element-plus:dev-main --dev -W
```

## Instalacao Publicada

Quando o pacote estiver publicado, instale com:

```bash
composer require kallbuloso/breeze-element-plus --dev
```

## Stack Vue

Para instalar a stack Inertia, Vue e Element Plus:

```bash
php artisan breeze-element-plus:install vue
```

Com SSR:

```bash
php artisan breeze-element-plus:install vue --ssr
```

Com Pest:

```bash
php artisan breeze-element-plus:install vue --pest
```

O instalador copia os stubs, instala dependencias Composer e npm, executa `migrate:fresh --seed` e prepara os arquivos de frontend.

Depois da instalacao, voce pode rodar:

```bash
npm run lint
npm run format
npm run build
```

Para desenvolvimento:

```bash
npm run start
```

ou:

```bash
npm run dev
```

## Stack API

Para instalar apenas a base API:

```bash
php artisan breeze-element-plus:install api
```

A stack API instala Sanctum, rotas, controllers, requests, testes e a estrutura de autenticacao compartilhada.

## Usuario de Teste

O seeder cria um usuario inicial:

```txt
E-mail: test@example.com
Senha: password
```

Esse usuario e criado com `is_owner` habilitado.

## Estrutura Instalada pela Stack Vue

A stack `vue` adiciona ou atualiza, entre outros:

- `resources/js/app.js`
- `resources/js/ssr.js`, quando `--ssr` for usado
- `resources/js/bootstrap.js`
- `resources/js/Components`
- `resources/js/Layouts`
- `resources/js/Pages`
- `resources/js/Stores`
- `resources/js/composables`
- `resources/js/utils/iconify.js`
- `resources/css/app.css`
- `vite.config.js`
- `eslint.config.js`
- `.prettierrc`
- `.prettierignore`
- `.editorconfig`

## Toast

O pacote mantem uma facade `Toast` para notificacoes vindas do backend. Exemplo:

```php
use Kallbuloso\BreezeElementPlus\Facades\Toast;

Toast::success('Perfil atualizado com sucesso.');
```

Os toasts sao compartilhados com o frontend pelo middleware Inertia e exibidos no layout da aplicacao.

## Atualizando no App de Teste

Quando alterar este pacote localmente, rode na aplicacao de teste:

```bash
composer update kallbuloso/breeze-element-plus -W
php artisan breeze-element-plus:install vue --ssr
```

Depois:

```bash
npm install
npm run lint
npm run format
npm run build
```

## Licenca

Este pacote e open source sob a licenca MIT.
