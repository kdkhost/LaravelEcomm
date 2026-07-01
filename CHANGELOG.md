# Changelog

Todas as mudancas relevantes deste projeto devem ser documentadas aqui em portugues brasileiro.

## [1.1.6] - 2026-07-01

### Corrigido
- Menu lateral do admin deixou de apontar para a rota inexistente `avaliacoes.index` e passou a usar a rota real `reviews.index`.
- Fluxo administrativo de avaliacoes foi alinhado ao resource `reviews`, corrigindo listagem, edicao, exclusao e redirects do modulo.
- Views de avaliacoes com nomes corrompidos por encoding antigo foram reescritas em ASCII limpo para evitar novas falhas de rota e renderizacao.

### Validacoes
- `php -l Modules/Product/Http/Controllers/ProductReviewController.php`
- `git diff --check`
- Validacao remota de `/admin` apos limpeza de cache do Laravel.
- Validacao remota de `https://loja.km.site.nom.br/en/provador-virtual/status` apos configuracao do `REPLICATE_API_TOKEN`.

## [1.1.5] - 2026-07-01

### Corrigido
- Provador virtual deixou de tratar a composicao em canvas como resultado final e agora possui geracao real por IA via Replicate FLUX Kontext Pro.
- A referencia visual da peca passa a ser enviada junto da foto da crianca em uma imagem composta temporaria em memoria, evitando depender apenas de texto para vestir a roupa escolhida.
- Endpoint `/provador-virtual/status` foi criado antes da rota com slug para nao conflitar com produtos.

### Adicionado
- Servico `VirtualTryOnAiService` com validacao de imagem, limite de tamanho, prompt direcionado para preservar rosto/pose/corpo e trocar apenas a roupa.
- Endpoint AJAX `/provador-virtual/processar` para gerar a imagem com IA mediante consentimento de uso da foto.
- Status publico de configuracao do provador indicando provedor, modelo, limite de upload e disponibilidade da extensao GD.
- Bloco visual "Resultado IA real" no provador com estilo da imagem, carregamento, erros claros, abertura e download do resultado.
- Variaveis `REPLICATE_*` nos exemplos `.env.example`, `.env.cpanel.example` e `.env.prod.example`.

### Seguranca e privacidade
- A foto da crianca nao e persistida no storage da loja; o processamento usa dados temporarios em memoria e envio ao provedor somente apos consentimento.
- Quando a imagem da peca esta disponivel localmente pela Media Library, o servidor usa o arquivo local em vez de baixar uma URL publica.

### Validacoes
- `php -l Modules/Front/Services/VirtualTryOnAiService.php`
- `php -l Modules/Front/Http/Controllers/VirtualTryOnController.php`
- `php -l config/services.php`
- `node --check public/frontend/js/rataplam-virtual-try-on.js`
- `git diff --check`
- Verificacao sem UTF-8 BOM nos arquivos alterados.
- `graphify update .`
- `graphify update`
- Deploy remoto no cPanel com `optimize:clear`, `config:clear`, `route:clear` e `view:cache` executados com sucesso.
- Rota `https://loja.km.site.nom.br/en/provador-virtual/status` validada com HTTP `200`, provedor Replicate, modelo `black-forest-labs/flux-kontext-pro`, GD disponivel e `configurado=false` ate configurar `REPLICATE_API_TOKEN`.
- Pagina `https://loja.km.site.nom.br/en/provador-virtual/bermuda-andes` validada com HTTP `200`, bloco "Resultado IA real" e endpoint `/provador-virtual/processar`.
- Assets `frontend/js/rataplam-virtual-try-on.js?v=20260701ia` e `frontend/css/rataplam-virtual-try-on.css?v=20260701ia` validados em producao com HTTP `200`.

### Observacoes
- A geracao de imagem real depende de `REPLICATE_API_TOKEN` configurado no `.env` do servidor. Sem essa chave, a tela mostra o status pendente e nao dispara custo de IA.

## [1.1.4] - 2026-07-01

### Adicionado
- Provador virtual 360 com upload local de foto de corpo inteiro da crianca, composicao em canvas e aplicacao visual da roupa selecionada.
- Controles de ajuste fino para largura, altura, posicao vertical, opacidade e angulo 360 da roupa no corpo.
- Geracao de quadros de visualizacao em 360 graus nos angulos 0, 45, 90, 135, 180, 225, 270 e 315 graus.
- Botao para baixar a imagem final gerada pelo provador no navegador.
- Selecionador de produto dentro do provador para trocar a peca sem sair da experiencia.
- Atalhos "Provador virtual" nas vitrines de produtos dos temas default e modern, incluindo home, grade e lista.
- CSS e JavaScript dedicados em `frontend/css/rataplam-virtual-try-on.css` e `frontend/js/rataplam-virtual-try-on.js`.

### Alterado
- `VirtualTryOnController` agora entrega galeria da peca, lista de produtos ativos e mapa corporal para ajuste proporcional do caimento.
- Paginas do provador dos temas default e modern passam a usar um componente compartilhado em `front::partials.virtual-try-on-studio`.
- Calculo de medidas retorna tamanho recomendado, mensagem de caimento e parametros de corpo usados pelo canvas.

### Seguranca e privacidade
- A foto da crianca e processada no navegador no modo padrao e nao fica salva no servidor.
- O upload exige confirmacao de autorizacao de uso da foto antes de carregar a imagem no provador.

### Observacoes
- A visualizacao 360 atual e uma simulacao interativa em canvas a partir da foto enviada e da imagem da peca. Um modelo 3D/IA generativa real pode ser acoplado futuramente mediante provedor externo e credenciais proprias.

## [1.1.3] - 2026-07-01

### Adicionado
- Comando `rataplam:import-catalog` para importar/catalogar imagens e descricoes dos produtos da loja antiga Rataplam com suporte a lotes para cPanel.
- Descricoes curtas e completas atualizadas nos 84 produtos da loja nova, usando o titulo, categorias e detalhes estruturados da pagina antiga do Wix.
- CSS `frontend/css/rataplam-storefront.css` para padronizar altura dos cards, enquadramento das imagens, animacoes de entrada e hover dos produtos.
- Menu mobile em drawer lateral deslizante, com busca, links principais, carrinho, favoritos e acesso de conta.
- Layout unificado em portugues brasileiro para login, cadastro, recuperacao de senha, redefinicao, confirmacao de senha e login por link magico.

### Alterado
- Layouts dos temas default, modern e globais receberam suporte ao drawer mobile e ao CSS de padronizacao da vitrine.
- Cards de produto agora usam altura consistente, imagem com `object-fit: cover`, textos limitados por linha e area de preco/acao alinhada.
- O importador Rataplam remove duplicidade de URLs do Wix pelo identificador real da midia, evitando duplicar imagens quando o Wix entrega a mesma foto em tamanhos diferentes.

### Corrigido
- Removidas 152 midias duplicadas criadas durante a primeira execucao do importador, mantendo a primeira copia valida de cada arquivo por hash.
- Cache/opcache remoto limpo apos publicacao dos layouts para o cPanel refletir os arquivos atualizados.

### Validacoes
- 84 de 84 produtos com resumo curto maior que 80 caracteres e descricao completa maior que 250 caracteres.
- 84 de 84 produtos com bloco "Detalhes corretos do produto" na descricao.
- 152 imagens finais publicadas na Media Library, sem produtos sem imagem e sem duplicatas por hash.
- Lote de imagens da Calca Juno e Calca Nono validado com `0` novas duplicatas, `38` imagens existentes reconhecidas e `4` URLs antigas do Wix ainda bloqueadas por HTTP `403`.
- Home, grade de produtos, detalhes de produto, login, cadastro e recuperacao de senha validados em producao com HTTP `200`.
- CSS `frontend/css/rataplam-storefront.css?v=20260701` validado em producao com HTTP `200`.
- Scripts temporarios `_codex_*` removidos do servidor.

### Observacoes
- As 4 imagens pendentes continuam indisponiveis na origem antiga do Wix: 2 da Calca Nono e 2 da Calca Juno.
- O site ainda possui rotas de idioma antigas como `/en`; esta entrega padronizou as telas alteradas e o catalogo de produtos, sem remover idiomas cadastrados.

## [1.1.2] - 2026-07-01

### Adicionado
- Imagens dos produtos da loja antiga Wix Rataplam importadas para a Media Library da nova loja.
- Publicacao das imagens em `public/storage` no servidor cPanel para evitar bloqueio de symlink em hospedagem compartilhada.

### Alterado
- `.env` remoto ajustado para usar `FILESYSTEM_DISK=public`, `FILESYSTEM_PUBLIC_ROOT=/home/lojavirtual/public_html/public/storage` e `FILESYSTEM_PUBLIC_URL=https://loja.km.site.nom.br/storage`.

### Validacoes
- 84 de 84 produtos da nova loja foram mapeados com produtos da loja antiga.
- 152 imagens foram importadas e publicadas com retorno HTTP `200` em `/storage/...`.
- Home, pagina da Calca Nono e pagina da Calca Juno validadas em producao com retorno HTTP `200`.
- Scripts temporarios de importacao e correcao de storage removidos do servidor.
- `graphify update .`
- `graphify update`

### Observacoes
- 4 imagens antigas do Wix nao foram importadas porque todas as URLs publicas expostas pela propria loja antiga retornaram HTTP `403`: 2 da Calca Nono e 2 da Calca Juno.
- Mesmo com essas URLs bloqueadas na origem antiga, todos os 84 produtos publicados possuem pelo menos uma imagem ativa na nova loja.

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
