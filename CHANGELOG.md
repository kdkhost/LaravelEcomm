# Changelog

Todas as alterações notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/),
Copyright © 2026 kdkhost. Todos os direitos reservados.

Este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

## [Não publicado]

### Adicionado
- `.env.example` super completo em português com todas as variáveis documentadas
- `.env.prod.example` completo em português para ambiente de produção cPanel
- `.env` simplificado e robusto com `CACHE_STORE`, `LOG_LEVEL`, seções organizadas
- `APP_TIMEZONE=America/Sao_Paulo`, `APP_CURRENCY=BRL`, `APP_CURRENCY_SYMBOL=R$` no `.env`
- `.htaccess` na raiz do repositório (rewrite para `public/` + PHP 8.4 handler)
- Língua `pt` no `LanguageDatabaseSeeder` (Português como padrão)
- `currency_symbol` e `default_currency` em `config/app.php` (BRL, R$)
- Moeda BRL nos gateways: PayPal (`config/paypal.php`), Stripe (`config/stripe.php`)
- BRL no `CurrencyService` (símbolo R$, taxas, moeda base, moedas disponíveis)
- BRL no `GeoIpService` (moeda padrão para localização geográfica)
- BRL nas moedas válidas do `SettingsRequest`
- `resources/lang/pt/messages.php` — cupom com R$ em vez de $
- `public/frontend/js/jquery.mask.min.js` — plugin de máscaras jQuery Mask v1.14.16

### Alterado
- Workflow definido: atualizações sempre nos 3 locais (local → GitHub → servidor)
- CHANGELOG sempre atualizado a cada sessão
- **Checkout (3 temas)** totalmente em português brasileiro:
  - Labels traduzidas (Nome, Sobrenome, E-mail, Telefone, CPF/CNPJ, CEP, Endereço, etc.)
  - Moeda alterada de `$` para `R$` em todos os preços e totais
  - Campos brasileiros adicionados: CPF/CNPJ (com máscara automática), Estado/UF (dropdown completo), Bairro, Número
  - País padrão alterado de MK (Macedônia) para BR (Brasil)
  - Telefone alterado de `type="number"` para `type="text"` (com máscara)
  - Máscaras JavaScript para CPF/CNPJ, telefone `(00) 00000-0000` e CEP `00000-000`
  - jQuery Mask Plugin incluído nos scripts do checkout
- `config/app.php`: `'timezone'` agora lê de `env('APP_TIMEZONE', 'America/Sao_Paulo')`

### Corrigido
- Site `loja.km.site.nom.br` retornando 404 — `.htaccess` foi perdido durante `git reset --hard origin/main` no servidor (estava em commit local não enviado). Recriado e adicionado ao repositório para não se perder novamente.

### MercadoPago
- Já implementado nas sessões anteriores: Service, Controller (6 endpoints), DTO, Action, rotas, views admin, CSRF exclusion, `config/mercadopago.php`
- Moeda já configurada como BRL no Service e DTO
- Rotas funcionais: checkout, success, failure, pending, webhook, refund
- Opção de pagamento MercadoPago presente nos 3 temas de checkout

### Planejado
- Testar fluxo completo de checkout MercadoPago (sandbox e produção)
- Implementar notificações de e-mail transacionais em português
- Configurar cron jobs para processamento de carrinhos abandonados e fila
- Adicionar validação server-side de CPF/CNPJ no backend

## [1.0.0] - 2026-06-30

### Adicionado

#### 🇧🇷 Internacionalização (Português)
- Traduções completas em português brasileiro (pt-BR) para:
  - `frontend.php` — interface do cliente
  - `auth.php` — autenticação
  - `pagination.php` — paginação
  - `passwords.php` — redefinição de senha
  - `apiResponse.php` — respostas da API
  - `messages.php` — mensagens do sistema
  - `sidebar.php` — barra lateral do admin
  - `partials.php` — componentes parciais
  - `validation.php` — validação de formulários
- Adicionado `pt` ao array de locales em `config/app.php`
- Adicionada língua portuguesa à tabela `languages` no banco de dados
- Definido português como idioma padrão (`APP_LOCALE=pt`, `APP_FALLBACK_LOCALE=pt` no `.env`)

#### 🛒 Catálogo de Produtos (Rataplam)
- Criado `RataplamStoreSeeder` com:
  - Marca **Rataplam**
  - 8 categorias (Calças, Camisetas, Moletons, Jaquetas, Acessórios, Bermudas, Sapatos, Promoções)
  - 84 produtos com preços, descrições e imagens extraídos de rataplam.com.br
- 8 produtos definidos como destaque (`is_featured = true`)
- 4 produtos definidos como oferta (`d_deal = 1`)

#### 💳 Integração MercadoPago
- **Service**: `Modules/Billing/Services/MercadoPagoService.php` — API via cURL
- **Controller**: `Modules/Billing/Http/Controllers/MercadoPagoController.php` com endpoints:
  - `checkout` — inicia pagamento
  - `success` — retorno após pagamento aprovado
  - `failure` — retorno após pagamento recusado
  - `pending` — retorno após pagamento pendente
  - `webhook` — notificação automática de status
  - `refund` — reembolso total e parcial
- **DTO**: `Modules/Billing/DTOs/MercadoPagoDTO.php`
- **Action**: `Modules/Billing/Actions/MercadoPago/CreateMercadoPagoChargeAction.php`
- **Rotas**: 6 rotas em `Modules/Billing/Routes/web.php`
- **Views de checkout**: atualizadas nos 3 temas (default, modern, sport)
- **Configuração admin**: formulário de token do MercadoPago no painel
- **Migrations**: adicionado `mercadopago` aos enums de `payments` e `orders`
- **CSRF**: webhook excluído da verificação CSRF em `bootstrap/app.php`
- **Config**: `config/mercadopago.php` com token de acesso e modo sandbox
- **Singleton**: registrado em `BillingServiceProvider.php`
- **Checkout**: `ProcessCheckoutAction.php` atualizado com fluxo mercadopago

#### 🚀 Compatibilidade cPanel/WHM
- `.env.example` reescrito com opções MySQL e SQLite comentadas
- `deploy.sh` — script de deploy para cPanel
- Migrations com detecção de driver (`SQLite` vs `MySQL`) para compatibilidade
- `graphify-out/.gitignore` — exclui `cache/` do versionamento
- `graphify-out/cache/` removido do git tracking

#### 🐛 Correções
- Criada view `themes/default/pages/product-deal.blade.php` (estava faltando)
- 4 produtos definidos como oferta para popular a página `/pt/product/deal`
- `DatabaseSeeder.php`: corrigido `SET FOREIGN_KEY_CHECKS` para compatibilidade com SQLite
- README.md: corrigidas credenciais padrão (admin, manager, superadmin)

### Alterado
- `.env` configurado para SQLite (desenvolvimento local sem Docker)
- `.env` com `SCOUT_DRIVER=collection`, `MULTI_TENANT_ENABLED=false`,
  `TELESCOPE_ENABLED=false` para ambiente local
- `Modules/Front/Http/Controllers/FrontController.php` — rota `productDeal` agora
  funciona corretamente

### Removido
- Dependências de serviços externos desnecessários para dev local:
  - Redis (substituído por file cache)
  - Elasticsearch (substituído por collection driver)
  - Telescope (desabilitado)

### Segurança
- CSRF desabilitado apenas para webhook do MercadoPago
- Nenhuma chave secreta exposta no código fonte
