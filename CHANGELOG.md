# Changelog

Todas as mudancas relevantes deste projeto devem ser documentadas aqui em portugues brasileiro.

## [1.1.1] - 2026-07-01

### Corrigido
- Corrigido erro 500 em producao causado por `Call to undefined function format_currency()` no tema modern.
- Carregamento dos helpers globais de tema, locale e moeda tambem pelo `AppServiceProvider`, evitando dependencia exclusiva do autoload `files` do Composer em hospedagem cPanel.

### Validacoes
- `php -l app/Providers/AppServiceProvider.php`
- `git diff --check`
- `graphify update .`
- `graphify update`

## [1.1.0] - 2026-07-01

### Adicionado
- Compatibilidade operacional com hospedagem compartilhada cPanel/CloudLinux, incluindo `.htaccess` na raiz para ocultar `/public`, exemplos de `.env` para cPanel/producao e arquivo `.user.ini`.
- Documentacao de deploy cPanel em `docs/deploy-cpanel.md`.
- Worker centralizado de cron para rodar tarefas do Laravel sem depender do cron da hospedagem nem de trafego de visitantes.
- Configuracao padrao para fuso `America/Sao_Paulo`, locale `pt` e moeda BRL.
- Integracao Mercado Pago com checkout transparente, Pix, boleto, retorno automatico e webhook com segredo dinamico.
- Logs de webhook do Mercado Pago para rastreabilidade de eventos recebidos.
- Provador virtual para produtos nos temas default e modern.
- Integracao de frete Correios configuravel:
  - Cotacao AJAX na pagina do produto.
  - Cotacao AJAX no carrinho.
  - Suporte a API oficial de preco/prazo com token, contrato, codigo DR e servicos configuraveis.
  - Fallback para regras internas de frete da loja quando nao houver token, contrato ou resposta valida dos Correios.
  - Configuracoes `CORREIOS_*` nos exemplos de ambiente e no painel de frete.
- Arquivo ZIP de entrega do codigo-fonte em `builds/LaravelEcomm-cpanel-source-20260630.zip`.

### Alterado
- README traduzido integralmente para portugues brasileiro, mantendo a estrutura, comandos, URLs, credenciais e conteudo tecnico do documento original.
- Textos publicos e administrativos revisados para portugues brasileiro nas paginas alteradas.
- Configuracoes de pagamento revisadas para Mercado Pago robusto e padrao brasileiro.
- Tela administrativa de frete padronizada em portugues brasileiro e expandida com campos dos Correios.
- Exemplos `.env.example`, `.env.cpanel.example` e `.env.prod.example` atualizados com Mercado Pago, Correios, BRL e timezone Sao Paulo.

### Corrigido
- Ajustes de compatibilidade para cPanel/shared hosting em paths publicos, storage e configuracoes de cache.
- Protecoes para evitar dependencia de visitas publicas na execucao de tarefas recorrentes.
- Normalizacao de mensagens e labels nas views alteradas para reduzir textos mistos em ingles/portugues.

### Validacoes
- `composer validate --no-check-publish`
- `php -l` nos arquivos PHP alterados
- Verificacao de arquivos sem UTF-8 BOM
- `git diff --check`
- `graphify update .`
- `graphify update`

### Observacoes
- A base do projeto permanece em Laravel 12 conforme o README original e `composer.json`.
- A API oficial de preco e prazo dos Correios exige credenciais/token e liberacao conforme contrato ou permissao no CWS; sem essas credenciais, a loja usa as regras internas de frete configuradas no sistema.
