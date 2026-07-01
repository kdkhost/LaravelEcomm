# Deploy em WHM/cPanel

Este projeto foi preparado para rodar em hospedagem compartilhada WHM/cPanel mantendo a versao atual do sistema.

## Requisitos

- PHP 8.4 ou superior habilitado pelo MultiPHP Manager.
- Extensoes comuns do Laravel: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd` ou `imagick`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.
- MariaDB/MySQL criado pelo painel cPanel.
- Composer disponivel no Terminal do cPanel ou dependencias enviadas ja instaladas.

## Estrutura no public_html

O projeto pode ser enviado inteiro para `public_html`. O arquivo `.htaccess` da raiz redireciona as requisicoes para `public/index.php` sem exibir `/public` na URL e bloqueia acesso direto a pastas internas como `app`, `config`, `database`, `storage` e `vendor`.

Arquivos principais para cPanel:

- `.htaccess`: entrada pela raiz e protecao de arquivos internos.
- `public/.htaccess`: front controller padrao do Laravel.
- `.user.ini` e `public/.user.ini`: limites de PHP para upload e execucao.
- `.env.cpanel.example`: modelo de ambiente para MariaDB/cPanel.

## Configuracao do .env

No servidor, copie `.env.cpanel.example` para `.env` e ajuste:

```env
APP_URL=https://seudominio.com.br
APP_TIMEZONE=America/Sao_Paulo
APP_CURRENCY_SYMBOL="R$"
APP_CURRENCY_CODE=BRL
DB_HOST=localhost
DB_DATABASE=cpanel_usuario_banco
DB_USERNAME=cpanel_usuario_user
DB_PASSWORD="senha-do-banco"
QUEUE_CONNECTION=database
CACHE_STORE=file
SESSION_DRIVER=file
FILESYSTEM_DISK=public
```

Se o servidor nao permitir `php artisan storage:link`, use o modo sem symlink:

```env
FILESYSTEM_PUBLIC_ROOT=/home/usuario/public_html/public/storage
FILESYSTEM_PUBLIC_URL=https://seudominio.com.br/storage
```

## Mercado Pago

Configure as credenciais em `Configuracoes > Pagamentos` ou diretamente no `.env`:

```env
MERCADOPAGO_ENABLED=true
MERCADOPAGO_ENVIRONMENT=production
MERCADOPAGO_ACCESS_TOKEN=APP_USR...
MERCADOPAGO_PUBLIC_KEY=APP_USR...
MERCADOPAGO_WEBHOOK_SECRET=gere-um-segredo-forte
MERCADOPAGO_STATEMENT_DESCRIPTOR=LOJA VIRTUAL
```

O painel administrativo exibe os enderecos dinamicos para copiar no Mercado Pago:

- Retorno automatico: `https://seudominio.com.br/mercadopago/retorno`
- Webhook: `https://seudominio.com.br/mercadopago/webhook?secret=SEU_SEGREDO`

No painel do Mercado Pago, cadastre o webhook para eventos de pagamento. O sistema valida o segredo pela URL e tambem aceita a assinatura oficial enviada nos headers `x-signature` e `x-request-id` quando o segredo do webhook estiver configurado.

## Frete Correios

Configure em `Configuracoes > Frete` ou diretamente no `.env`:

```env
CORREIOS_ENABLED=true
CORREIOS_CONTRACT_ENABLED=false
CORREIOS_ORIGIN_CEP=00000000
CORREIOS_ACCESS_TOKEN=
CORREIOS_CONTRACT_NUMBER=
CORREIOS_REGIONAL_CODE=
CORREIOS_SERVICE_CODES=03220:SEDEX,03298:PAC
CORREIOS_PRECO_BASE_URL=https://api.correios.com.br/preco/v1
CORREIOS_PRAZO_BASE_URL=https://api.correios.com.br/prazo/v1
CORREIOS_TIMEOUT=8
CORREIOS_DEFAULT_WEIGHT_GRAMS=300
CORREIOS_WEIGHT_UNIT=kg
CORREIOS_DEFAULT_LENGTH_CM=20
CORREIOS_DEFAULT_WIDTH_CM=15
CORREIOS_DEFAULT_HEIGHT_CM=5
```

A cotacao aparece na pagina do produto e no carrinho. Quando houver token e CEP de origem, o sistema tenta a API oficial dos Correios; sem token ou se a API falhar, usa as regras internas de frete configuradas na loja para nao quebrar a compra.

## Comandos de instalacao

Execute no Terminal do cPanel, dentro de `public_html`:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Se `storage:link` falhar por restricao do cPanel, mantenha `FILESYSTEM_PUBLIC_ROOT` apontando para `public/storage` e crie a pasta manualmente.

## Cron central sem trafego

O sistema nao depende de visita ao site para acionar tarefas. O comando centralizado e:

```bash
php artisan system:cron-work
```

Ele executa o agendador Laravel e processa a fila em loop com trava para evitar dois workers simultaneos.

Para iniciar pelo Terminal do cPanel quando o provedor permitir processo PHP persistente:

```bash
bash scripts/cpanel-cron-worker.sh start
```

Para consultar ou parar:

```bash
bash scripts/cpanel-cron-worker.sh status
bash scripts/cpanel-cron-worker.sh stop
```

Importante: sem cron da hospedagem, sem trafego e sem um processo persistente permitido pelo WHM/cPanel/CloudLinux, nenhum sistema PHP consegue executar tarefas futuras sozinho. Neste projeto, a dependencia fica centralizada no worker `system:cron-work`, nao em visitas ao site.

## Tarefas agendadas

As tarefas continuam declaradas em `app/Console/Kernel.php`, com timezone fixo em `America/Sao_Paulo`:

- `sitemap:generate` diariamente.
- `newsletter:product` semanalmente.
- `newsletter:post` semanalmente.
- `stock:notify` diariamente.
- `front:optimize --force` a cada 15 minutos.

## Validacao rapida

Depois do deploy, confira:

```bash
php artisan about
php artisan route:list
php artisan system:cron-work --once
```

Abra a home, o login e o admin no dominio final. Um `200 OK` isolado nao prova que assets, storage e rotas internas estao corretos.
