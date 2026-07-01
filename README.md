# Plataforma Avancada de E-commerce em Laravel 12

### Demo: https://e-comm.mk

---

## Sumario

- [Inicio rapido](#inicio-rapido)
- [Visao geral dos recursos](#visao-geral-dos-recursos)
- [Capturas de tela](#capturas-de-tela)
- [Documentacao](#documentacao)
- [Blaze Template Engine](#blaze-template-engine-fork-personalizado)
- [Contribuindo](#contribuindo)
- [Licenca](#licenca)

---

## Inicio rapido

### Opcao 1: Docker (recomendado)

```bash
# 1. Clone e inicie
git clone https://github.com/KalimeroMK/LaravelEcomm.git
cd LaravelEcomm
docker-compose up -d

# 2. Instale as dependencias
docker exec e_comm_app composer install

# 3. Configure o ambiente
cp .env.example .env
docker exec e_comm_app php artisan key:generate

# 4. Configure o banco de dados no .env
DB_HOST=db
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret

# 5. Execute migrations e seeders
docker exec e_comm_app php artisan migrate:fresh --seed

# 6. Crie o link do storage
docker exec e_comm_app php artisan storage:link

# 7. Acesse a aplicacao
# Frontend: http://localhost:90
# Admin:    http://localhost:90/admin
# API:      http://localhost:90/api/v1
```

### Opcao 2: Desenvolvimento local

```bash
# 1. Clone e instale
git clone https://github.com/KalimeroMK/LaravelEcomm.git
cd LaravelEcomm
composer install
cp .env.example .env

# 2. Configure o ambiente
php artisan key:generate
# Edite o .env com as credenciais do seu banco de dados

# 3. Configure o banco de dados
php artisan migrate:fresh --seed

# 4. Instale os assets do frontend
npm install && npm run build

# 5. Crie o link do storage
php artisan storage:link

# 6. Inicie o servidor
php artisan serve
# Visite: http://localhost:8000
```

### Credenciais padrao

| Papel | URL | E-mail | Senha |
|------|-----|-------|----------|
| **Admin** | `/admin` | `superadmin@mail.com` | `password` |
| **Cliente** | `/login` | `client@mail.com` | `password` |

---

## Visao geral dos recursos

### Recursos do frontend

#### Sistema multi-tema
- **Dois temas completos**: tema Padrao (e-commerce classico) e tema Moderno (design contemporaneo)
- **Troca facil de tema**: altere o tema ativo pelas configuracoes do admin, sem mudancas no codigo
- **Assets por tema**: CSS, JS e imagens organizados por tema (`public/frontend/themes/{theme}/`)
- **Fallback de views**: fallback automatico para o tema padrao quando uma view nao existir no tema ativo
- **32+ views por tema**: cobertura completa de paginas (inicio, produtos, carrinho, checkout, paginas do usuario)

#### Internacionalizacao (i18n)
- **Estrategia por prefixo de URL**: `/en/`, `/mk/`, `/de/`, `/sq/` para troca de idioma
- **Deteccao automatica de locale**: detecta o idioma do navegador e redireciona automaticamente
- **Idiomas controlados por banco**: adicione/remova idiomas pelo admin sem alterar codigo
- **Gerenciamento de traducoes**: interface administrativa para gerenciar traducoes
- **Traducoes de models**: Product, Category, Page e Post suportam traducoes via trait `HasTranslations`
- **Fallback automatico**: usa o idioma padrao quando uma traducao estiver ausente
- **Suporte RTL**: suporte embutido para idiomas da direita para a esquerda

#### Geolocalizacao e moeda
- **Deteccao por GeoIP**: detecta automaticamente o pais do usuario pelo endereco IP
- **Moeda automatica**: detecta e define a moeda com base no pais
- **Taxas de cambio em tempo real**: mais de 20 moedas com taxas ao vivo
- **API de conversao de moeda**: converte precos entre moedas dinamicamente
- **Deteccao da UE**: helpers de conformidade GDPR para paises da Uniao Europeia
- **Deteccao de fuso horario**: define automaticamente o fuso horario com base na localizacao

#### Catalogo de produtos
- **Tipos de produto**: simples, configuravel, pacote e baixavel
- **Atributos avancados**: sistema de atributos no estilo Bagisto (cor, tamanho, material, amostras)
- **Amostras visuais**: amostras de cor, botoes e imagens
- **Produtos configuraveis**: geracao automatica de variantes por combinacoes de atributos (ex.: camiseta vermelha em P, M, G)
- **Navegacao em camadas**: filtros AJAX com contagem de produtos em tempo real
- **Variantes de produto**: estoque, preco e imagens por variante
- **Avaliacoes de produto**: notas por estrelas, texto de avaliacao e votos de utilidade
- **Lista de desejos**: salve produtos para depois e compartilhe a lista
- **Vistos recentemente**: rastreia e exibe historico de navegacao
- **Comparacao de produtos**: compara ate 4 produtos lado a lado
- **Gerenciamento de estoque**: controle de inventario, alertas de estoque baixo e tratamento de indisponibilidade
- **Downloads digitais**: suporte para produtos baixaveis com links seguros

#### Experiencia de compra
- **Carrinho de compras**: adicionar/remover via AJAX, atualizar quantidades e mini-carrinho
- **Carrinhos salvos**: salvar carrinho para depois e restaurar carrinho
- **Checkout como visitante**: compra sem cadastro
- **Multiplos enderecos**: salvar varios enderecos de entrega/cobranca
- **Catalogo de enderecos**: enderecos padrao e gerenciamento de enderecos
- **Rastreamento de pedidos**: acompanhar status do pedido e informacoes de envio
- **Historico de pedidos**: visualizar todos os pedidos e refazer pedidos anteriores
- **Sistema de cupons**: aplicar cupons no carrinho e ver detalhamento do desconto
- **Estimativa de frete**: calcular custos de frete antes do checkout

#### Busca e descoberta
- **Integracao com Elasticsearch**: busca full-text, correspondencia aproximada e sugestoes
- **Filtros avancados**: filtrar por preco, marca, atributos e avaliacoes
- **Autocompletar**: sugestoes de busca enquanto digita
- **Analise de buscas**: rastreia buscas populares e consultas sem resultado
- **Navegacao por categorias**: categorias multinivel e arvore de categorias
- **Breadcrumbs**: trilha de navegacao para facilitar retorno
- **Produtos relacionados**: produtos relacionados por IA ou manualmente
- **Up-sells e cross-sells**: recomendacoes de produtos

#### Gerenciamento de conteudo (frontend)
- **Sistema de blog**: categorias, tags, imagens destacadas e meta SEO
- **Paginas CMS**: crie paginas personalizadas (Sobre, Contato, FAQ) pelo admin
- **Banners**: banners da pagina inicial e promocionais com rastreamento de cliques
- **Menus**: gerenciamento dinamico de menus e menus aninhados
- **Newsletter**: formulario de assinatura e confirmacao double opt-in

#### Recursos da conta do usuario
- **Painel do usuario**: visao geral de pedidos, enderecos e dados da conta
- **Gerenciamento de perfil**: atualizar nome, e-mail, senha e avatar
- **Gerenciamento de pedidos**: visualizar pedidos, baixar faturas e rastrear envios
- **Catalogo de enderecos**: multiplos enderecos e padrao de entrega/cobranca
- **Gerenciamento de lista de desejos**: adicionar/remover e mover para o carrinho
- **Gerenciamento de avaliacoes**: editar/excluir proprias avaliacoes
- **Gerenciamento de comentarios**: gerenciar comentarios do blog
- **Login social**: login com Facebook, Google, Twitter e GitHub

#### Pagamento e checkout
- **Gateways de pagamento**:
  - **Stripe**: pagamentos com cartao de credito (testado com Stripe Elements)
  - **PayPal**: checkout expresso e suporte a sandbox
  - **Pagamento na entrega (COD)**: opcao de pagar na entrega
- **Checkout seguro**: suporte a SSL e helpers de conformidade PCI
- **Checkout em etapas**: etapas de entrega, pagamento e revisao
- **Confirmacao de pedido**: confirmacao por e-mail e fatura em PDF
- **Tratamento de pagamento com falha**: tentar pagamento novamente ou cancelar pedido

#### Marketing e engajamento
- **Compartilhamento de produtos**: compartilhar em redes sociais (Facebook, Twitter, Pinterest)
- **Login social**: cadastro/login com um clique
- **Assinatura de newsletter**: formulario no rodape e opcao de popup
- **Recuperacao de carrinho abandonado**: lembretes automaticos por e-mail
- **Recomendacoes de produtos**: sugestoes por IA com base no comportamento
- **Banners promocionais**: banners segmentados por perfil de usuario

#### Recursos de SEO
- **Meta tags dinamicas**: titulo e descricao gerados por pagina
- **Open Graph**: otimizacao de compartilhamento no Facebook
- **Twitter Cards**: otimizacao de compartilhamento no Twitter
- **Dados estruturados**: marcacao Schema.org (Product, Organization, BreadcrumbList)
- **Sitemaps XML**: gerados automaticamente para produtos, categorias e posts
- **URLs amigaveis para SEO**: URLs por slug (`/product/nike-air-max`)
- **URLs canonicas**: evita problemas de conteudo duplicado
- **Robots.txt**: gerado automaticamente com referencia ao sitemap
- **Alt tags**: SEO de imagens com texto alternativo automatico

---

### Painel administrativo

#### Dashboard e analytics
- **Dashboard geral**: vendas de hoje, pedidos, usuarios e graficos de receita
- **Graficos interativos**: integracao Chart.js (linhas, barras e pizza)
- **Relatorios de vendas**: dados diarios, semanais, mensais e anuais
- **Rastreamento de receita**: receita total e ticket medio
- **Analise de usuarios**: novos usuarios, usuarios ativos e crescimento
- **Analise de produtos**: mais vendidos, estoque baixo, visualizacoes/cliques
- **Analise de pedidos**: status de pedidos, metodos de pagamento e metodos de frete
- **Exportacao de relatorios**: baixar relatorios em CSV, Excel e PDF
- **Atualizacoes em tempo real**: atualizacao ao vivo para metricas principais

#### Gerenciamento de produtos
- **Grade de produtos**: filtros avancados, ordenacao e acoes em massa
- **Criacao de produtos**: assistente passo a passo para criar produtos
- **Gerenciamento de atributos**: crie atributos, opcoes e familias
- **Gerenciamento de variantes**: estoque, preco e imagens de variantes
- **Gerenciador de midia**: upload de imagens, videos e documentos (Unisharp File Manager)
- **Atribuicao de categorias**: produtos em multiplas categorias e categoria principal
- **Gerenciamento de SEO**: meta title, descricao e palavras-chave por produto
- **Gerenciamento de estoque**: quantidade, limite de estoque baixo e encomendas
- **Precificacao**: preco base, preco promocional, preco de custo e precificacao por nivel
- **Avaliacoes de produto**: aprovar/rejeitar avaliacoes e responder avaliacoes
- **Importacao/exportacao de produtos**: importacao em massa via CSV

#### Gerenciamento de pedidos
- **Grade de pedidos**: filtrar por status, data, cliente e pagamento
- **Ciclo de vida do pedido**:
  - Status: Pending, Processing, On Hold, Shipped, Delivered, Cancelled, Refunded, Failed
  - Status de pagamento: Pending, Paid, Failed, Refunded
- **Detalhes do pedido**: produtos, dados do cliente, entrega e pagamento
- **Geracao de faturas**: faturas em PDF com template personalizavel
- **Rastreamento de envio**: adicionar codigos de rastreio e transportadoras
- **Processamento de reembolso**: reembolso parcial/total e credito na loja
- **Notas do pedido**: notas internas e notas visiveis para o cliente
- **Impressao do pedido**: pagina amigavel para impressao
- **Reenvio de e-mail**: reenviar confirmacao do pedido e fatura

#### Gerenciamento de clientes
- **Grade de clientes**: buscar, filtrar e exportar clientes
- **Perfil do cliente**: pedidos, enderecos e historico de atividade
- **Grupos de clientes**: crie grupos (VIP, Atacado etc.)
- **Segmentacao de clientes**: com base em historico de compra e localizacao
- **Impersonacao**: entrar como cliente para ajudar em diagnosticos
- **Gerenciamento de enderecos**: visualizar/editar enderecos de clientes

#### Gerenciamento de conteudo
- **Posts do blog**: criar, editar e agendar posts
- **Categorias**: categorias hierarquicas e configuracoes de SEO
- **Tags**: gerenciamento de tags e nuvem de tags
- **Paginas**: paginas CMS (Sobre, Contato, Termos etc.)
- **Banners**: sliders da pagina inicial e banners promocionais
  - Rastreamento de cliques
  - Rastreamento de impressoes
  - Datas de inicio/fim
  - URLs de destino
- **Biblioteca de midia**: gerenciamento central de arquivos e otimizacao de imagens
- **Construtor de menus**: criacao de menus por arrastar e soltar

#### Ferramentas de marketing
- **Campanhas de e-mail**: criar e enviar campanhas de newsletter
- **Templates de e-mail**: templates personalizaveis para todos os e-mails
- **Gerenciamento de newsletter**: assinantes, segmentos e historico de envio
- **E-mails de carrinho abandonado**: automacao em sequencia de 3 e-mails
  - E-mail 1: 1 hora apos abandono
  - E-mail 2: 24 horas apos abandono
  - E-mail 3: 72 horas apos abandono
- **Gerenciamento de cupons**:
  - Tipos: percentual, valor fixo e frete gratis
  - Restricoes: compra minima, restricoes por categoria e por usuario
  - Limites de uso: por cupom e por usuario
  - Datas de expiracao
- **Promocoes**: regras de preco de catalogo e regras de preco de carrinho

#### E-mail marketing e automacao
- **Analytics de campanhas**: taxas de abertura, clique, rejeicao e descadastro
- **Templates de e-mail**: templates HTML com variaveis dinamicas
- **E-mails automaticos**: serie de boas-vindas, aniversario e reengajamento
- **Agendamento de e-mail**: agendar campanhas para datas futuras
- **Teste A/B**: testar assuntos e conteudos diferentes
- **Segmentacao**: direcionar grupos especificos de clientes

#### Gerenciamento de usuarios e funcoes
- **Usuarios admin**: criar/editar contas administrativas
- **Funcoes**: definir funcoes (Super Admin, Admin, Editor etc.)
- **Permissoes**: permissoes granulares por funcao
- **Matriz de permissoes**: atribuicao visual de permissoes
- **Log de atividades**: rastrear acoes administrativas e historico de login

#### Configuracao do sistema
- **Configuracoes gerais**: nome da loja, logo, endereco e contato
- **Configuracoes de moeda**: moeda padrao, taxas de cambio e formatacao
- **Configuracoes de idioma**: idiomas ativos e idioma padrao
- **Configuracoes de e-mail**: SMTP e templates de e-mail
- **Configuracoes de pagamento**: ativar/desativar gateways e modo sandbox
- **Configuracoes de frete**: metodos, zonas e tarifas
- **Configuracoes de impostos**: aliquotas, classes e opcoes de exibicao
- **Configuracoes de SEO**: meta tags padrao e configuracoes de sitemap
- **Configuracoes sociais**: links de redes sociais e chaves de API
- **Modo de manutencao**: ativar/desativar com mensagem personalizada

#### Modulo de relatorios
- **8 tipos de relatorio**: vendas, produtos, clientes, estoque, pedidos, cupons, receita e impostos
- **Relatorios agendados**: gerar e enviar relatorios por e-mail automaticamente
- **Periodos personalizados**: intervalos flexiveis para relatorios
- **Formatos de exportacao**: CSV, Excel e PDF
- **Historico de relatorios**: rastrear relatorios gerados
- **Graficos visuais**: representacao grafica de dados

---

### Seguranca e desempenho

#### Recursos de seguranca
- **Autenticacao de dois fatores (2FA)**: integracao com Google Authenticator
- **Controle de acesso baseado em funcoes (RBAC)**: permissoes granulares
- **Bloqueio de IP**: bloquear enderecos IP especificos ou faixas
- **Limite de tentativas de login**: prevencao contra ataques de forca bruta
- **Politicas de senha segura**: exigir senhas fortes
- **Registro de atividades**: rastrear todas as acoes administrativas
- **Trilhas de auditoria**: historico completo de alteracoes de dados
- **Protecao CSRF**: tokens CSRF nativos do Laravel
- **Protecao XSS**: escape de saida e Content Security Policy
- **Protecao contra SQL Injection**: consultas parametrizadas

#### Otimizacao de desempenho
- **Cache Redis**: cache da aplicacao e armazenamento de sessao
- **Otimizacao de consultas**: eager loading e cache de consultas
- **Otimizacao de imagens**: compressao automatica e suporte WebP
- **Lazy loading**: imagens carregam conforme o usuario rola a pagina
- **Suporte a CDN**: servir assets estaticos por CDN
- **Compressao Gzip**: compressao de respostas
- **Cache do navegador**: headers de cache para assets estaticos
- **Indices de banco de dados**: indices otimizados para consultas rapidas
- **Cache de pagina inteira**: cache de paginas renderizadas para visitantes
- **Isolamento de cache por tenant**: prefixo por tenant evita contaminacao de cache entre tenants
- **Contador de geracao da busca**: invalidacao instantanea do cache de busca quando produtos mudam
- **Cache de helpers**: metodos de frete, tags de post e categorias de post em cache com TTL de 1 h

---

### Blaze Template Engine (fork personalizado)

Este projeto usa um **fork personalizado** do pacote de otimizacao Blaze template engine com recursos aprimorados para Laravel Blade:

#### Recursos
- **Suporte a View Components**: suporte completo a Laravel View Components com renderizacao correta
- **Suporte a View::share()**: injeta variaveis compartilhadas de view sem custo extra de desempenho
- **Suporte a View Composer**: dispara composers pela diretiva `@blaze(name: 'view.name')`
- **Otimizacao consciente de tema**: estrategias por tema (compile, memo, fold)
- **Suporte multi-tema**: configuracoes diferentes por tema (default vs modern)
- **Overlay de debug**: profiler embutido com tempos de renderizacao por view
- **Aquecimento de cache**: pre-compila views no deploy para primeira carga mais rapida

#### Configuracao (`config/blaze.php`)
```php
'themes' => [
    'enabled' => true,
    'paths' => [
        'default' => base_path('Modules/Front/Resources/views/themes/default'),
        'modern' => base_path('Modules/Front/Resources/views/themes/modern'),
    ],
    'strategies' => [
        'default' => ['compile' => true, 'memo' => true, 'fold' => false],
        'modern' => ['compile' => true, 'memo' => true, 'fold' => false],
    ],
],
```

#### Variaveis de ambiente
```env
BLAZE_ENABLED=true          # Ativar/desativar a otimizacao Blaze
BLAZE_DEBUG=true            # Exibir overlay de debug com tempos de renderizacao
BLAZE_CACHE_WARM=true       # Pre-compilar views no aquecimento de cache
```

> **Observacao**: este projeto usa um fork em `KalimeroMK/blaze` (dev-main) que inclui:
> - injecao automatica de View::share() (upstream: nao suportado)
> - suporte a View::composer() via diretiva `@blaze(name: ...)`
> - suporte a templates com @extends
> - todos os recursos upstream do Blaze e otimizacoes de desempenho

---

### IA e automacao

#### Integracao OpenAI
- **Gerador de descricao de produto**: descricoes de produto geradas por IA
- **Criacao de conteudo**: ideias de posts de blog e sugestoes de conteudo
- **Otimizacao de SEO**: geracao de meta descriptions
- **Assistencia de traducao**: sugestoes de traducao com IA

#### Automacao de e-mail
- **Recuperacao de carrinho abandonado**: sequencia de 3 e-mails
- **Serie de boas-vindas**: onboarding para novos usuarios
- **Acompanhamento pos-compra**: solicitar avaliacoes e oferecer cross-sell
- **Campanhas de reengajamento**: recuperar clientes inativos
- **E-mails de aniversario**: felicitacoes automaticas com cupom

#### Recomendacoes inteligentes
- **Sugestoes por IA**: recomendacoes de produtos baseadas em comportamento
- **Produtos relacionados**: correspondencia inteligente de itens relacionados
- **Frequentemente comprados juntos**: recomendacoes no estilo Amazon
- **Vistos recentemente**: historico personalizado de navegacao
- **Produtos em alta**: itens populares na categoria do usuario

---

## Capturas de tela

<details>
<summary>Clique para ver as capturas de tela</summary>

![Admin Dashboard](https://user-images.githubusercontent.com/29488275/90719413-13b82200-e2d4-11ea-8ca0-f0e5551c4c9d.png)
![Category Management](https://user-images.githubusercontent.com/29488275/90719470-3813fe80-e2d4-11ea-8f63-e6001855a945.png)
![Product Management](https://user-images.githubusercontent.com/29488275/90719534-61348f00-e2d4-11ea-8a81-409daee0ad94.png)
![Order Details](https://user-images.githubusercontent.com/29488275/90719557-71e50500-e2d4-11ea-97cf-befb1d525643.png)
![User Profile](https://user-images.githubusercontent.com/29488275/90719563-7a3d4000-e2d4-11ea-9e6a-56caac13b146.png)
![Blog Management](https://user-images.githubusercontent.com/29488275/90719572-81644e00-e2d4-11ea-9fe5-3325ab427f88.png)
![Frontend](https://user-images.githubusercontent.com/29488275/90719631-a1940d00-e2d4-11ea-89a3-eb36960d687d.png)

</details>

---

## Testes

O projeto tem cobertura abrangente de testes com **220+ arquivos de testes unitarios** cobrindo todas as classes Action.

### Cobertura de testes

| Modulo | Classes Action | Arquivos de teste |
|--------|---------------|-------------------|
| **User** | 14 | 9 |
| **Product** | 23 | 16 |
| **Order** | 11 | 7 |
| **Cart** | 6 | 4 |
| **Category** | 7 | 7 |
| **Brand** | 6 | 5 |
| **Banner** | 5 | 5 |
| **Message** | 8 | 7 |
| **Newsletter** | 5 | 5 |
| **Coupon** | 6 | 6 |
| **Shipping** | 6 | 6 |
| **Page** | 5 | 5 |
| **Post** | 10 | 10 |
| **Role** | 6 | 6 |
| **Settings** | 8 | 8 |
| **Billing** | 9 | 9 |
| **Attribute** | 10 | 10 |
| **Core** | 3 | 3 |
| **Google2FA** | 10 | 10 |
| **Bundle** | 7 | 7 |
| **ProductStats** | 5 | 5 |
| **Permission** | 5 | 5 |
| **Language** | 3 | 3 |
| **Reporting** | 5 | 5 |
| **OpenAI** | 3 | 3 |
| **Front** | 23 | 23 |
| **Tenant** | 5 | 5 |
| **Complaint** | 5 | 5 |
| **Other** | 4 | 4 |
| **TOTAL** | **220** | **220** |

### Executando testes

```bash
# Executar todos os testes
./vendor/bin/phpunit

# Executar apenas testes unitarios
./vendor/bin/phpunit tests/Unit

# Executar testes de um modulo especifico
./vendor/bin/phpunit tests/Unit/Actions/Product
./vendor/bin/phpunit tests/Unit/Actions/User
./vendor/bin/phpunit tests/Unit/Actions/Order

# Executar com relatorio de cobertura
./vendor/bin/phpunit --coverage-html coverage
```

### Estrutura de testes

Os testes sao organizados seguindo o padrao Action:

```
tests/Unit/Actions/
├── ActionTestCase.php              # Classe base de teste com RefreshDatabase
├── User/
│   ├── LoginUserActionTest.php
│   ├── RegisterUserActionTest.php
│   ├── UpdateUserActionTest.php
│   └── ...
├── Product/
│   ├── CreateProductActionTest.php
│   ├── UpdateProductActionTest.php
│   ├── DeleteProductActionTest.php
│   └── ...
└── [Module]/
    └── [Action]Test.php
```

### Escrevendo novos testes

Todos os testes de Action devem:
1. Estender `ActionTestCase` (fornece `RefreshDatabase` e seed de idiomas)
2. Usar factories para criacao de models
3. Testar fluxos felizes e casos de borda
4. Verificar alteracoes no estado do banco de dados
5. Mockar servicos externos (OpenAI, gateways de pagamento)

Exemplo:
```php
<?php
declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\StoreProductAction;
use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class CreateProductActionTest extends ActionTestCase
{
    public function testExecuteCreatesProductSuccessfully(): void
    {
        $dto = new ProductDTO(
            id: null,
            title: 'Test Product',
            // ... outros campos
        );

        $action = app(StoreProductAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('products', ['title' => 'Test Product']);
    }
}
```

---

## Documentacao

### Sumario

1. [Guias de instalacao](#guias-de-instalacao)
2. [Documentacao da API](#documentacao-da-api)
3. [Documentacao de modulos](#documentacao-de-modulos)
4. [Referencia de comandos](#referencia-de-comandos)
5. [Testes](#testes)
6. [Melhorias recentes](#melhorias-recentes)

---

### Guias de instalacao

#### Configuracao detalhada com Docker

**Pre-requisitos:** Docker e Docker Compose

**Passo a passo:**

```bash
# Iniciar todos os containers
docker-compose up -d

# Acesso aos containers
docker exec -it e_comm_app sh      # Container da aplicacao
docker exec -it e_comm_mysql mysql -u homestead -p  # Banco de dados
docker exec -it e_comm_redis redis-cli               # Redis

# Comandos uteis
docker exec e_comm_app php artisan cache:clear
docker exec e_comm_app php artisan view:clear
docker exec e_comm_app php artisan migrate

# Portas dos containers:
# - Web (FrankenPHP):  90 -> 80
# - MySQL:            3311 -> 3306
# - Redis:            6381 -> 6379
# - Elasticsearch:    9200 -> 9200
```

#### Configuracao de e-mail

```bash
# Configure no .env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls

# Processar e-mails de carrinho abandonado
php artisan cart:process-abandoned-emails
```

#### Configuracao do Elasticsearch

```bash
# Indexar produtos
docker-compose exec app php artisan product:index

# Reconstruir do zero
docker-compose exec app php artisan product:index --fresh
```

---

### Documentacao da API

**Colecao Postman:** `LaravelEcomm.postman_collection.json`

**URL base:** `http://localhost:90/api/v1`

#### Autenticacao

```bash
# Login
POST /api/v1/auth/login
{
    "email": "client@mail.com",
    "password": "password"
}
```

#### API multi-idioma

```bash
# Listar idiomas
GET /api/languages

# Obter locale atual
GET /api/languages/current
X-Locale: mk
```

#### API de relatorios

```bash
# Listar tipos de relatorio
GET /api/admin/report-types

# Criar relatorio
POST /api/admin/reports
{
    "name": "Monthly Sales",
    "type": "sales",
    "format": "excel",
    "filters": {
        "date_from": "2026-01-01",
        "date_to": "2026-01-31"
    }
}

# Gerar e exportar
POST /api/admin/reports/{id}/generate
POST /api/admin/reports/{id}/export
{ "format": "csv" }
```

#### API de geolocalizacao

```bash
# Obter localizacao por IP
GET /api/geolocation

# Converter moeda
POST /api/currency/convert
{
    "amount": 100,
    "from": "USD",
    "to": "EUR"
}
```

---

### Documentacao de modulos

#### Sistema de atributos

```php
// Criar atributo com opcoes
$color = Attribute::factory()->create([
    'code' => 'color',
    'type' => 'select',
    'display' => 'color',
    'is_filterable' => true,
]);

$color->options()->create([
    'value' => 'red',
    'label' => 'Red',
    'color_hex' => '#FF0000',
]);

// Criar produto configuravel
$product = Product::factory()->create([
    'type' => Product::TYPE_CONFIGURABLE,
]);
$product->configurableAttributes()->attach($color);

// Gerar variantes
app(ConfigurableProductService::class)->generateVariants($product);
```

#### Configuracao de SEO

```bash
# Gerar sitemaps
php artisan seo:generate-sitemap

# Configuracao em config/seo.php
```

---

### Referencia de comandos

#### Gerenciamento de cache

```bash
php artisan cache:clear          # Limpar cache da aplicacao
php artisan config:clear         # Limpar cache de configuracao
php artisan view:clear           # Limpar views compiladas
php artisan route:clear          # Limpar cache de rotas
```

#### Banco de dados e seed

```bash
php artisan migrate:fresh --seed     # Banco novo com seeders
php artisan db:seed --class=DatabaseSeeder  # Executar seeder especifico
```

#### Gerenciamento de produtos

```bash
php artisan product:index          # Indexar produtos no Elasticsearch
php artisan product:index --fresh  # Reconstruir indice
```

#### E-mail e marketing

```bash
php artisan cart:process-abandoned-emails  # Processar carrinhos abandonados
php artisan newsletter:send-campaigns      # Enviar campanhas de newsletter
```

#### Analytics e relatorios

```bash
php artisan analytics:aggregate    # Agregar dados de analytics
php artisan reports:generate       # Gerar relatorios agendados
```

---

### Testes

#### Executando testes

```bash
# Executar todos os testes
php artisan test

# Executar suite especifica
php artisan test --filter=OrderTest

# Executar com cobertura
php artisan test --coverage

# Executar testes E2E (requer Playwright)
npx playwright test
```

#### Contas de teste

```
Admin:    superadmin@mail.com / password
Cliente:  client@mail.com / password
```

---

### Multi-tenancy (opcional)

Multi-tenancy vem **desativado por padrao**. A aplicacao roda como uma plataforma padrao de e-commerce de loja unica, sem configuracao extra.

Para ativar o modo com um banco por tenant, defina no `.env`:

```env
MULTI_TENANT_ENABLED=true
TENANT_MAIN_DOMAIN=yourdomain.com
```

Quando ativado, cada tenant e identificado por subdominio e recebe seu proprio banco MySQL isolado. O cache recebe prefixo automatico por tenant (`laravel_t1_`, `laravel_t2_`, ...) para que tenants nunca compartilhem dados em cache.

| Configuracao | Padrao | Descricao |
|--------------|--------|-----------|
| `MULTI_TENANT_ENABLED` | `false` | Ativa isolamento de banco por tenant |
| `TENANT_MAIN_DOMAIN` | `localhost` | Dominio raiz para deteccao de subdominio |
| `TENANT_DB_PREFIX` | `tenant_` | Prefixo do nome dos bancos dos tenants |
| `TENANT_ISOLATE_USERS` | `true` | Mantem contas de usuario por tenant |

---

### Melhorias recentes

<details>
<summary>Clique para expandir atualizacoes recentes</summary>

#### Mais recente: correcoes de cache e desempenho
- Tenant cache isolation via prefixo por tenant (sem mais `Cache::flush()` na troca)
- Invalidacao de cache de busca por contador de geracao (instantanea, sem resultado obsoleto por 24 h)
- Multi-tenancy desligado por padrao, funcionando como e-commerce normal e com opt-in para multi-tenant
- Resolucao de tema via `Cache::remember()`, segura para workers FrankenPHP/Octane
- Substituicao de todos os `Artisan::call()` em requisicoes web por operacoes diretas no filesystem
- `Helper::shipping()`, `postTagList()` e `postCategoryList()` agora ficam em cache com TTL de 1 h

#### Anterior: correcoes de carrinho/checkout e pagamento
- Views do tema moderno para carrinho, checkout e meus pedidos
- Fluxos de pagamento corrigidos (Stripe, PayPal, COD)
- Acesso aos pedidos do cliente corrigido
- Testes E2E com Playwright

#### Refatoracao de API e arquitetura
- Arquitetura baseada em Actions para todos os controllers
- Cobertura completa de API para todos os modulos
- Mais de 540 testes passando
- Conformidade com PHPStan

#### Multi-idioma, relatorios e geolocalizacao
- Estrategia por prefixo de URL (`/en/`, `/mk/`, `/de/`)
- 8 tipos de relatorios com agendamento
- Deteccao GeoIP com conversao de moeda
- Taxas de cambio em tempo real

#### Implementacao do tema moderno
- Mais de 32 arquivos de view para o tema moderno
- Design responsivo com cobertura abrangente
- Troca facil de tema via configuracoes

#### Sistema de atributos (estilo Bagisto)
- Atributos polimorficos para Products, Bundles e Categories
- Amostras visuais (cor, imagem, botao)
- Produtos configuraveis com geracao automatica de variantes
- Navegacao em camadas com filtros AJAX

</details>

---

## Contribuindo

1. Faca um fork do repositorio
2. Crie sua branch de recurso (`git checkout -b feature/amazing-feature`)
3. Commit suas alteracoes (`git commit -m 'Add amazing feature'`)
4. Envie para a branch (`git push origin feature/amazing-feature`)
5. Abra um Pull Request

---

## Licenca

Este projeto e licenciado sob a Licenca MIT.

---

## Links rapidos

| Recurso | URL |
|---------|-----|
| **Demo** | https://e-comm.mk |
| **Admin** | http://localhost:90/admin |
| **Docs da API** | `LaravelEcomm.postman_collection.json` |
| **Frontend** | http://localhost:90 |

---

<p align="center">Construido com amor usando Laravel 12</p>
