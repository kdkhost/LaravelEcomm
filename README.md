# Plataforma Avançada de E-commerce em Laravel 12

---

## 📑 Índice

- [🚀 Início Rápido](#-início-rápido)
- [✨ Visão Geral dos Recursos](#-visão-geral-dos-recursos)
- [📸 Capturas de Tela](#-capturas-de-tela)
- [📚 Documentação](#-documentação)
- [⚡ Blaze Template Engine](#-blaze-template-engine-fork-personalizado)
- [🤝 Contribuindo](#-contribuindo)
- [📄 Licença](#-licença)

---

## 🚀 Início Rápido

### Opção 1: Docker (Recomendado)

```bash
# 1. Clone e inicie
git clone https://github.com/kdkhost/LaravelEcomm.git
cd LaravelEcomm
docker-compose up -d

# 2. Instale as dependências
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

# 6. Crie o link de storage
docker exec e_comm_app php artisan storage:link

# 7. Acesse a aplicação
# Frontend: http://localhost:90
# Admin:    http://localhost:90/admin
# API:      http://localhost:90/api/v1
```

### Opção 2: Desenvolvimento Local

```bash
# 1. Clone e instale
git clone https://github.com/kdkhost/LaravelEcomm.git
cd LaravelEcomm
composer install
cp .env.example .env

# 2. Configure o ambiente
php artisan key:generate
# Edite .env com suas credenciais de banco de dados

# 3. Configure o banco de dados
php artisan migrate:fresh --seed

# 4. Instale assets do frontend
npm install && npm run build

# 5. Crie o link de storage
php artisan storage:link

# 6. Inicie o servidor
php artisan serve
# Acesse: http://localhost:8000
```

### Credenciais Padrão

| Perfil | URL | E-mail | Senha |
|--------|-----|--------|-------|
| **Admin** | `/admin` | `superadmin@mail.com` | `password` |
| **Cliente** | `/login` | `client@mail.com` | `password` |

---

## ✨ Visão Geral dos Recursos

### 🎨 Recursos do Frontend

#### Sistema Multi-Tema
- **Dois Temas Completos**: Tema padrão (e-commerce clássico) e Tema moderno (design contemporâneo)
- **Troca Fácil de Tema**: Altere o tema ativo pelas configurações do admin (sem alterar código)
- **Assets por Tema**: CSS, JS e imagens organizados por tema (`public/frontend/themes/{tema}/`)
- **Fallback de Views**: Fallback automático para o tema padrão se a view não existir no tema ativo
- **32+ Views de Tema**: Cobertura completa de páginas (homepage, produtos, carrinho, checkout, perfil)

#### Internacionalização (i18n)
- **Estratégia por Prefixo na URL**: `/en/`, `/pt/`, `/de/` para troca de idioma
- **Detecção Automática de Idioma**: Detecta o idioma do navegador e redireciona automaticamente
- **Idiomas pelo Banco de Dados**: Adicione/remova idiomas pelo admin sem alterar código
- **Gerenciamento de Traduções**: Interface admin para gerenciar traduções
- **Traduções de Modelos**: Produtos, Categorias, Páginas e Posts suportam traduções via trait `HasTranslations`
- **Fallback Automático**: Usa o idioma padrão se a tradução não existir
- **Suporte RTL**: Suporte a idiomas da direita para a esquerda

#### GeoLocalização e Moeda
- **Detecção GeoIP**: Detecta automaticamente o país do usuário pelo IP
- **Moeda Automática**: Detecta e define a moeda baseada no país
- **Taxas de Câmbio em Tempo Real**: 20+ moedas com taxas atualizadas
- **API de Conversão de Moedas**: Converta preços entre moedas em tempo real
- **Detecção UE**: Helpers de conformidade com LGPD para países da União Europeia
- **Detecção de Fuso Horário**: Define o fuso horário baseado na localização

#### Catálogo de Produtos
- **Tipos de Produto**: Simples, Configurável, Bundle, Downloadável
- **Atributos Avançados**: Sistema de atributos estilo Bagisto (cor, tamanho, material)
- **Swatches Visuais**: Swatches de cor, botão e imagem
- **Produtos Configuráveis**: Geração automática de variantes a partir de combinações de atributos
- **Navegação em Camadas**: Filtros via AJAX com contagem de produtos em tempo real
- **Variantes de Produto**: Gerencie estoque, preço e imagens por variante
- **Avaliações de Produtos**: Avaliações com estrelas, texto e votação de utilidade
- **Lista de Desejos**: Salve produtos para depois, compartilhe a lista
- **Vistos Recentemente**: Rastreie e exiba histórico de navegação
- **Comparação de Produtos**: Compare até 4 produtos lado a lado
- **Gestão de Estoque**: Acompanhe inventário, alertas de estoque baixo
- **Downloads Digitais**: Suporte a produtos digitais com links seguros

#### Experiência de Compra
- **Carrinho de Compras**: AJAX para adicionar/remover, atualizar quantidades, mini-carrinho
- **Carrinhos Salvos**: Salve o carrinho para depois, restaure-o
- **Checkout sem Cadastro**: Finalize a compra sem registro
- **Múltiplos Endereços**: Salve vários endereços de entrega/cobrança
- **Catálogo de Endereços**: Endereços padrão, gerenciamento de endereços
- **Rastreamento de Pedidos**: Acompanhe status do pedido e informações de entrega
- **Histórico de Pedidos**: Veja todos os pedidos, reordene pedidos anteriores
- **Sistema de Cupons**: Aplique cupons no carrinho, veja desconto detalhado
- **Estimativa de Frete**: Calcule custos de frete antes do checkout

#### Busca e Descoberta
- **Integração Elasticsearch**: Busca em texto completo, correspondência difusa, sugestões
- **Filtros Avançados**: Filtre por preço, marca, atributos, avaliações
- **Auto-Completar**: Sugestões de busca enquanto você digita
- **Analytics de Busca**: Acompanhe buscas populares, consultas sem resultados
- **Navegação por Categorias**: Categorias em múltiplos níveis, árvore de categorias
- **Breadcrumbs**: Trilha de navegação para fácil retorno
- **Produtos Relacionados**: Produtos relacionados por IA ou manuais
- **Up-Sells e Cross-Sells**: Recomendações de produtos

#### Gerenciamento de Conteúdo (Frontend)
- **Sistema de Blog**: Categorias, tags, imagens em destaque, meta SEO
- **Páginas CMS**: Crie páginas personalizadas (Sobre, Contato, FAQ) pelo admin
- **Banners**: Banners da homepage, banners promocionais com rastreamento de cliques
- **Menus**: Gerenciamento dinâmico de menus, menus aninhados
- **Newsletter**: Formulário de inscrição, confirmação double opt-in

#### Recursos da Conta do Usuário
- **Painel do Usuário**: Visão geral de pedidos, endereços, informações da conta
- **Gerenciamento de Perfil**: Atualize nome, e-mail, senha, avatar
- **Gerenciamento de Pedidos**: Veja pedidos, baixe faturas, rastreie entregas
- **Catálogo de Endereços**: Múltiplos endereços, endereço de entrega/cobrança padrão
- **Gerenciamento de Lista de Desejos**: Adicione/remova, mova para o carrinho
- **Gerenciamento de Avaliações**: Edite/exclua suas avaliações
- **Gerenciamento de Comentários**: Gerencie comentários do blog
- **Login Social**: Faça login com Facebook, Google, Twitter, GitHub

#### Pagamento e Checkout
- **Gateways de Pagamento**:
  - **MercadoPago**: Pagamentos com cartão de crédito, PIX e boleto
  - **Stripe**: Pagamentos com cartão de crédito (testado com Stripe Elements)
  - **PayPal**: Checkout expresso, suporte a sandbox
  - **Cash on Delivery (COD)**: Pagamento na entrega
- **Checkout Seguro**: Suporte SSL, helpers de conformidade PCI
- **Checkout Multi-Etapas**: Frete, pagamento, revisão
- **Confirmação de Pedido**: Confirmação por e-mail, fatura em PDF
- **Tratamento de Pagamentos com Falha**: Repita o pagamento, cancele o pedido

#### Marketing e Engajamento
- **Compartilhamento de Produtos**: Compartilhe em redes sociais (Facebook, Twitter, Pinterest)
- **Login Social**: Registro/login com um clique
- **Inscrição em Newsletter**: Inscrição no rodapé, opção de popup
- **Recuperação de Carrinho Abandonado**: Lembretes automáticos por e-mail
- **Recomendações de Produtos**: Sugestões baseadas em comportamento
- **Banners Promocionais**: Banners direcionados por segmento de usuário

#### Recursos de SEO
- **Meta Tags Dinâmicas**: Título e descrição gerados automaticamente por página
- **Open Graph**: Otimização para compartilhamento no Facebook
- **Twitter Cards**: Otimização para compartilhamento no Twitter
- **Dados Estruturados**: Marcação Schema.org (Produto, Organização, BreadcrumbList)
- **Sitemaps XML**: Gerados automaticamente para produtos, categorias, posts
- **URLs Amigáveis para SEO**: URLs baseadas em slug (`/produto/nome-do-produto`)
- **URLs Canônicas**: Evite problemas de conteúdo duplicado
- **Robots.txt**: Gerado automaticamente com referência ao sitemap
- **Alt Tags**: SEO de imagens com texto alternativo automático

---

### ⚙️ Painel Admin

#### Dashboard e Analytics
- **Visão Geral**: Vendas hoje, pedidos, usuários, gráficos de receita
- **Gráficos Interativos**: Integração com Chart.js (linha, barra, pizza)
- **Relatórios de Vendas**: Dados diários, semanais, mensais, anuais
- **Acompanhamento de Receita**: Receita total, valor médio do pedido
- **Analytics de Usuários**: Novos usuários, usuários ativos, crescimento
- **Analytics de Produtos**: Mais vendidos, estoque baixo, visualizações/cliques
- **Analytics de Pedidos**: Status de pedidos, métodos de pagamento, métodos de frete
- **Exportação de Relatórios**: Baixe relatórios em CSV, Excel, PDF
- **Atualizações em Tempo Real**: Dados atualizados ao vivo para métricas principais

#### Gestão de Produtos
- **Grade de Produtos**: Filtros avançados, ordenação, ações em lote
- **Criação de Produtos**: Assistente passo a passo
- **Gerenciamento de Atributos**: Crie atributos, opções, famílias
- **Gerenciamento de Variantes**: Gerencie variantes (estoque, preço, imagens)
- **Gerenciador de Mídia**: Upload de imagens, vídeos, documentos (Unisharp File Manager)
- **Atribuição de Categorias**: Produtos em múltiplas categorias, categoria principal
- **Gerenciamento SEO**: Meta título, descrição, palavras-chave por produto
- **Gestão de Estoque**: Quantidade, limite de estoque baixo, reservas
- **Precificação**: Preço base, preço promocional, preço de custo, preço por nível
- **Avaliações de Produtos**: Aprovar/rejeitar avaliações, responder avaliações
- **Importação/Exportação de Produtos**: Importação em lote via CSV

#### Gestão de Pedidos
- **Grade de Pedidos**: Filtre por status, data, cliente, pagamento
- **Ciclo de Vida do Pedido**:
  - Status: Pendente, Processando, Aguardando, Enviado, Entregue, Cancelado, Reembolsado, Falhou
  - Status de Pagamento: Pendente, Pago, Falhou, Reembolsado
- **Detalhes do Pedido**: Produtos, informações do cliente, frete, pagamento
- **Geração de Fatura**: Faturas em PDF com template personalizável
- **Rastreamento de Envio**: Adicione códigos de rastreio, transportadoras
- **Processamento de Reembolsos**: Reembolsos parciais/totais, crédito na loja
- **Notas do Pedido**: Notas internas, notas visíveis ao cliente
- **Impressão do Pedido**: Página de pedido amigável para impressão
- **Reenviar E-mail**: Reenvie confirmação de pedido, fatura

#### Gestão de Clientes
- **Grade de Clientes**: Pesquise, filtre, exporte clientes
- **Perfil do Cliente**: Pedidos, endereços, histórico de atividade
- **Grupos de Clientes**: Crie grupos (VIP, Atacado, etc.)
- **Segmentação de Clientes**: Baseada em histórico de compras, localização
- **Personificação**: Faça login como cliente para ajudar com problemas
- **Gerenciamento de Endereços**: Veja/edite endereços do cliente

#### Gestão de Conteúdo
- **Posts de Blog**: Crie, edite, agende posts
- **Categorias**: Categorias hierárquicas, configurações de SEO
- **Tags**: Gerenciamento de tags, nuvem de tags
- **Páginas**: Páginas CMS (Sobre, Contato, Termos, etc.)
- **Banners**: Sliders da homepage, banners promocionais
  - Rastreamento de cliques
  - Rastreamento de impressões
  - Datas de início/fim
  - URLs de destino
- **Biblioteca de Mídia**: Gerenciamento central de arquivos, otimização de imagens
- **Construtor de Menus**: Criação de menus por arrastar e soltar

#### Ferramentas de Marketing
- **Campanhas de E-mail**: Crie e envie campanhas de newsletter
- **Templates de E-mail**: Templates personalizáveis para todos os e-mails
- **Gerenciamento de Newsletter**: Assinantes, segmentos, histórico de envios
- **E-mails de Carrinho Abandonado**: Automação de sequência de 3 e-mails
  - E-mail 1: 1 hora após o abandono
  - E-mail 2: 24 horas após o abandono
  - E-mail 3: 72 horas após o abandono
- **Gerenciamento de Cupons**:
  - Tipos: Porcentagem, Valor fixo, Frete grátis
  - Restrições: Compra mínima, restrições de categoria, restrições de usuário
  - Limites de uso: Por cupom, por usuário
  - Datas de validade
- **Promoções**: Regras de preço no catálogo, regras de preço no carrinho

#### Automação de Marketing
- **Analytics de Campanhas**: Taxas de abertura, cliques, rejeição, cancelamentos
- **Templates de E-mail**: Templates HTML com variáveis dinâmicas
- **E-mails Automatizados**: Sequência de boas-vindas, e-mails de aniversário, reengajamento
- **Agendamento de E-mails**: Agende campanhas para datas futuras
- **Teste A/B**: Teste diferentes linhas de assunto, conteúdo
- **Segmentação**: Segmente grupos específicos de clientes

#### Gestão de Usuários e Perfis
- **Usuários Admin**: Crie/edite contas de administrador
- **Perfis**: Defina perfis (Super Admin, Admin, Editor, etc.)
- **Permissões**: Permissões granulares por perfil
- **Matriz de Permissões**: Atribuição visual de permissões
- **Registro de Atividades**: Acompanhe ações do admin, histórico de login

#### Configuração do Sistema
- **Configurações Gerais**: Nome da loja, logo, endereço, contato
- **Configurações de Moeda**: Moeda padrão, taxas de câmbio, formatação
- **Configurações de Idioma**: Idiomas ativos, idioma padrão
- **Configurações de E-mail**: Configuração SMTP, templates de e-mail
- **Configurações de Pagamento**: Ativar/desativar gateways, modo sandbox
- **Configurações de Frete**: Métodos, zonas, taxas
- **Configurações de Imposto**: Alíquotas, classes de imposto, opções de exibição
- **Configurações de SEO**: Meta tags padrão, configurações de sitemap
- **Configurações Sociais**: Links de redes sociais, chaves de API
- **Modo de Manutenção**: Ativar/desativar com mensagem personalizada

#### Módulo de Relatórios
- **8 Tipos de Relatório**: Vendas, Produtos, Clientes, Inventário, Pedidos, Cupons, Receita, Imposto
- **Relatórios Agendados**: Geração automática e envio por e-mail
- **Períodos Personalizados**: Períodos de relatório flexíveis
- **Formatos de Exportação**: CSV, Excel, PDF
- **Histórico de Relatórios**: Acompanhe relatórios gerados
- **Gráficos Visuais**: Representação gráfica dos dados

---

### 🔐 Segurança e Performance

#### Recursos de Segurança
- **Autenticação de Dois Fatores (2FA)**: Integração com Google Authenticator
- **Controle de Acesso Baseado em Perfis (RBAC)**: Permissões granulares
- **Bloqueio de IP**: Bloqueie endereços IP ou faixas específicas
- **Limitação de Tentativas de Login**: Previna ataques de força bruta
- **Políticas de Senha Segura**: Exija senhas fortes
- **Registro de Atividades**: Acompanhe todas as ações do admin
- **Trilhas de Auditoria**: Histórico completo de alterações de dados
- **Proteção CSRF**: Tokens CSRF nativos do Laravel
- **Proteção XSS**: Escape de saída, Política de Segurança de Conteúdo
- **Proteção contra Injeção SQL**: Consultas parametrizadas

#### Otimização de Performance
- **Cache Redis**: Cache de aplicação, armazenamento de sessão
- **Otimização de Consultas**: Carregamento antecipado, cache de consultas
- **Otimização de Imagens**: Compressão automática, suporte a WebP
- **Carregamento Preguiçoso**: Imagens carregam conforme o usuário rola
- **Suporte CDN**: Sirva assets estáticos de CDN
- **Compressão Gzip**: Comprima respostas
- **Cache de Navegador**: Cabeçalhos de cache para assets estáticos
- **Indexação de Banco de Dados**: Índices otimizados para consultas rápidas
- **Cache de Página Inteira**: Cache de páginas renderizadas para visitantes
- **Isolamento de Cache por Tenant**: Prefixo de cache por tenant
- **Contador de Geração de Busca**: Invalidação instantânea de cache
- **Cache de Helpers**: Métodos de frete, tags de posts, categorias de posts

---

### ⚡ Blaze Template Engine (Fork Personalizado)

Este projeto usa um **fork personalizado** do pacote de otimização Blaze Template Engine:

#### Recursos
- **Suporte a View Components**: Renderização adequada de componentes do Laravel
- **Suporte a View::share()**: Injeção automática de variáveis compartilhadas
- **Suporte a View Composers**: Acione composers via diretiva `@blaze(name: 'view.name')`
- **Otimização por Tema**: Estratégias por tema (compile, memo, fold)
- **Suporte Multi-Tema**: Configurações diferentes por tema
- **Debug Overlay**: Perfilador de performance integrado
- **Cache Warming**: Pré-compila views na implantação

#### Configuração (`config/blaze.php`)
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

#### Variáveis de Ambiente
```env
BLAZE_ENABLED=true          # Ativar/desativar otimização Blaze
BLAZE_DEBUG=true            # Mostrar debug overlay com tempos de renderização
BLAZE_CACHE_WARM=true       # Pré-compilar views no cache warm
```

> **Nota**: Este projeto usa um fork em `kdkhost/blaze` (dev-main) que inclui:
> - Injeção automática de View::share()
> - Suporte a View::composer() via diretiva `@blaze(name: ...)`
> - Suporte a @extends
> - Todas as features e otimizações do Blaze original

---

### 🤖 IA e Automação

#### Integração OpenAI
- **Gerador de Descrições de Produtos**: Descrições geradas por IA
- **Criação de Conteúdo**: Ideias para posts de blog, sugestões de conteúdo
- **Otimização SEO**: Geração de meta descrições
- **Assistência de Tradução**: Sugestões de tradução por IA

#### Automação de E-mail
- **Recuperação de Carrinho Abandonado**: Sequência de 3 e-mails
- **Sequência de Boas-vindas**: E-mails de integração para novos usuários
- **Acompanhamento Pós-Compra**: Solicite avaliações, cross-sell
- **Campanhas de Reengajamento**: Recupere clientes inativos
- **E-mails de Aniversário**: Felicitações automáticas com cupom

#### Recomendações Inteligentes
- **Sugestões por IA**: Recomendações baseadas em comportamento
- **Produtos Relacionados**: Correspondência inteligente de itens relacionados
- **Comprados Juntos**: Recomendações estilo Amazon
- **Vistos Recentemente**: Histórico de navegação personalizado
- **Produtos em Alta**: Itens populares na categoria do usuário

---

## 📸 Capturas de Tela

<details>
<summary>Clique para ver as capturas de tela</summary>

![Admin Dashboard](https://user-images.githubusercontent.com/29488275/90719413-13b82200-e2d4-11ea-8ca0-f0e5551c4c9d.png)
![Gerenciamento de Categorias](https://user-images.githubusercontent.com/29488275/90719470-3813fe80-e2d4-11ea-8f63-e6001855a945.png)
![Gerenciamento de Produtos](https://user-images.githubusercontent.com/29488275/90719534-61348f00-e2d4-11ea-8a81-409daee0ad94.png)
![Detalhes do Pedido](https://user-images.githubusercontent.com/29488275/90719557-71e50500-e2d4-11ea-97cf-befb1d525643.png)
![Perfil do Usuário](https://user-images.githubusercontent.com/29488275/90719563-7a3d4000-e2d4-11ea-9e6a-56caac13b146.png)
![Gerenciamento de Blog](https://user-images.githubusercontent.com/29488275/90719572-81644e00-e2d4-11ea-9fe5-3325ab427f88.png)
![Frontend](https://user-images.githubusercontent.com/29488275/90719631-a1940d00-e2d4-11ea-89a3-eb36960d687d.png)

</details>

---

## 🧪 Testes

O projeto possui cobertura abrangente de testes com **220+ arquivos de teste unitário**.

### Cobertura de Testes

| Módulo | Classes Action | Arquivos de Teste |
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
| **Outros** | 4 | 4 |
| **TOTAL** | **220** | **220** |

### Executando Testes

```bash
# Execute todos os testes
./vendor/bin/phpunit

# Execute apenas testes unitários
./vendor/bin/phpunit tests/Unit

# Execute testes de um módulo específico
./vendor/bin/phpunit tests/Unit/Actions/Product
./vendor/bin/phpunit tests/Unit/Actions/User
./vendor/bin/phpunit tests/Unit/Actions/Order

# Execute com relatório de cobertura
./vendor/bin/phpunit --coverage-html coverage
```

### Estrutura de Testes

Os testes seguem o padrão Action:

```
tests/Unit/Actions/
├── ActionTestCase.php              # Classe base com RefreshDatabase
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
└── [Módulo]/
    └── [Action]Test.php
```

### Escrevendo Novos Testes

Todas as tests de Action devem:
1. Estender `ActionTestCase` (fornece `RefreshDatabase` e seed de idiomas)
2. Usar factories para criação de modelos
3. Testar caminhos felizes e casos de borda
4. Verificar mudanças no estado do banco de dados
5. Mockar serviços externos (OpenAI, gateways de pagamento)

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
            title: 'Produto de Teste',
            // ... outros campos
        );

        $action = app(StoreProductAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('products', ['title' => 'Produto de Teste']);
    }
}
```

---

## 📚 Documentação

### Índice

1. [Guias de Instalação](#guias-de-instalação)
2. [Documentação da API](#documentação-da-api)
3. [Documentação dos Módulos](#documentação-dos-módulos)
4. [Referência de Comandos](#referência-de-comandos)
5. [Testes](#testes)
6. [Melhorias Recentes](#melhorias-recentes)

---

### Guias de Instalação

#### Instalação Detalhada com Docker

**Pré-requisitos:** Docker & Docker Compose

**Passo a passo:**

```bash
# Inicie todos os containers
docker-compose up -d

# Acesso aos containers
docker exec -it e_comm_app sh      # Container da aplicação
docker exec -it e_comm_mysql mysql -u homestead -p  # Banco de dados
docker exec -it e_comm_redis redis-cli               # Redis

# Comandos úteis
docker exec e_comm_app php artisan cache:clear
docker exec e_comm_app php artisan view:clear
docker exec e_comm_app php artisan migrate

# Portas dos containers:
# - Web (FrankenPHP):  90 → 80
# - MySQL:            3311 → 3306
# - Redis:            6381 → 6379
# - Elasticsearch:    9200 → 9200
```

#### Configuração de E-mail

```bash
# Configure no .env
MAIL_MAILER=smtp
MAIL_HOST=seu-smtp-host
MAIL_PORT=587
MAIL_USERNAME=seu-email
MAIL_PASSWORD=sua-senha
MAIL_ENCRYPTION=tls

# Processar e-mails de carrinho abandonado
php artisan cart:process-abandoned-emails
```

#### Configuração Elasticsearch

```bash
# Indexar produtos
docker-compose exec app php artisan product:index

# Reconstruir do zero
docker-compose exec app php artisan product:index --fresh
```

---

### Documentação da API

**Coleção Postman:** `LaravelEcomm.postman_collection.json`

**URL Base:** `http://localhost:90/api/v1`

#### Autenticação

```bash
# Login
POST /api/v1/auth/login
{
    "email": "client@mail.com",
    "password": "password"
}
```

#### API Multi-Idioma

```bash
# Listar idiomas
GET /api/languages

# Obter locale atual
GET /api/languages/current
X-Locale: pt
```

#### API de Relatórios

```bash
# Listar tipos de relatório
GET /api/admin/report-types

# Criar relatório
POST /api/admin/reports
{
    "name": "Vendas Mensais",
    "type": "sales",
    "format": "excel",
    "filters": {
        "date_from": "2026-01-01",
        "date_to": "2026-01-31"
    }
}

# Gerar e Exportar
POST /api/admin/reports/{id}/generate
POST /api/admin/reports/{id}/export
{ "format": "csv" }
```

#### API de GeoLocalização

```bash
# Obter localização pelo IP
GET /api/geolocation

# Converter moeda
POST /api/currency/convert
{
    "amount": 100,
    "from": "USD",
    "to": "BRL"
}
```

---

### Documentação dos Módulos

#### Sistema de Atributos

```php
// Criar atributo com opções
$color = Attribute::factory()->create([
    'code' => 'cor',
    'type' => 'select',
    'display' => 'color',
    'is_filterable' => true,
]);

$color->options()->create([
    'value' => 'vermelho',
    'label' => 'Vermelho',
    'color_hex' => '#FF0000',
]);

// Criar produto configurável
$product = Product::factory()->create([
    'type' => Product::TYPE_CONFIGURABLE,
]);
$product->configurableAttributes()->attach($color);

// Gerar variantes
app(ConfigurableProductService::class)->generateVariants($product);
```

#### Configuração SEO

```bash
# Gerar sitemaps
php artisan seo:generate-sitemap

# Configuração em config/seo.php
```

---

### Referência de Comandos

#### Gerenciamento de Cache

```bash
php artisan cache:clear          # Limpar cache da aplicação
php artisan config:clear         # Limpar cache de configuração
php artisan view:clear           # Limpar views compiladas
php artisan route:clear          # Limpar cache de rotas
```

#### Banco de Dados e Seeders

```bash
php artisan migrate:fresh --seed     # Banco novo com seeders
php artisan db:seed --class=DatabaseSeeder  # Executar seeder específico
```

#### Gerenciamento de Produtos

```bash
php artisan product:index          # Indexar produtos no Elasticsearch
php artisan product:index --fresh  # Reconstruir índice
```

#### E-mail e Marketing

```bash
php artisan cart:process-abandoned-emails  # Processar carrinhos abandonados
php artisan newsletter:send-campaigns      # Enviar campanhas de newsletter
```

#### Analytics e Relatórios

```bash
php artisan analytics:aggregate    # Agregar dados de analytics
php artisan reports:generate       # Gerar relatórios agendados
```

---

### Testes

#### Executando Testes

```bash
# Execute todos os testes
php artisan test

# Execute suíte específica
php artisan test --filter=OrderTest

# Execute com cobertura
php artisan test --coverage

# Execute testes E2E (requer Playwright)
npx playwright test
```

#### Contas de Teste

```
Admin:    superadmin@mail.com / password
Cliente:  client@mail.com / password
```

---

### 🏢 Multi-Tenancy (Opcional)

O multi-tenancy está **desabilitado por padrão** — a aplicação funciona como e-commerce de loja única sem configuração extra.

Para ativar o modo banco-de-dados-por-tenant, configure no `.env`:

```env
MULTI_TENANT_ENABLED=true
TENANT_MAIN_DOMAIN=seudominio.com
```

Quando ativo, cada tenant é identificado por subdomínio e recebe seu próprio banco MySQL isolado. O cache é automaticamente prefixado por tenant (`laravel_t1_`, `laravel_t2_`, ...).

| Configuração | Padrão | Descrição |
|-------------|--------|-----------|
| `MULTI_TENANT_ENABLED` | `false` | Ativar isolamento banco-por-tenant |
| `TENANT_MAIN_DOMAIN` | `localhost` | Domínio raiz para detecção de subdomínio |
| `TENANT_DB_PREFIX` | `tenant_` | Prefixo do nome do banco para tenants |
| `TENANT_ISOLATE_USERS` | `true` | Manter contas de usuário por tenant |

---

### Melhorias Recentes

<details>
<summary>Clique para expandir as atualizações recentes</summary>

#### Últimas: Cache e Performance
- ✅ Isolamento de cache por tenant (sem mais `Cache::flush()` na troca)
- ✅ Invalidação de cache de busca via contador de geração (instantânea)
- ✅ Multi-tenancy desligado por padrão — funciona como e-commerce normal
- ✅ Resolução de tema via `Cache::remember()` — seguro para FrankenPHP/Octane
- ✅ Substituídos todos `Artisan::call()` em requisições web por operações diretas de arquivo
- ✅ Helpers de frete, tags de posts e categorias de posts agora com cache de 1 hora

#### Anteriores: Carrinho/Checkout e Pagamentos
- ✅ Views do tema moderno para carrinho, checkout, meus-pedidos
- ✅ Fluxos de pagamento corrigidos (Stripe, PayPal, COD)
- ✅ Acesso de clientes a pedidos corrigido
- ✅ Testes E2E com Playwright

#### Refatoração de API e Arquitetura
- Arquitetura baseada em Actions para todos os controllers
- Cobertura completa de API para todos os módulos
- 540+ testes passando
- Conformidade com PHPStan

#### Multi-Idioma, Relatórios e GeoLocalização
- Estratégia de prefixo na URL (`/en/`, `/pt/`, `/de/`)
- 8 tipos de relatório com agendamento
- Detecção GeoIP com conversão de moeda
- Taxas de câmbio em tempo real

#### Implementação do Tema Moderno
- 32+ views para o tema moderno
- Design responsivo com cobertura abrangente
- Troca fácil de tema pelas configurações

#### Sistema de Atributos (estilo Bagisto)
- Atributos polimórficos para Produtos, Bundles, Categorias
- Swatches visuais (cor, imagem, botão)
- Produtos configuráveis com geração automática de variantes
- Navegação em camadas com filtros AJAX

#### Integração MercadoPago
- Checkout com cartão de crédito, PIX e boleto
- Webhook automático para notificação de status
- Reembolso total e parcial
- Suporte a 3 temas (default, modern, sport)

#### Catálogo Rataplam
- 84 produtos importados
- 8 categorias
- Produtos em destaque e ofertas

</details>

---

## 🤝 Contribuindo

---

## 📄 Licença

Copyright © 2026 kdkhost. Todos os direitos reservados.

---

## 🔗 Links Rápidos

| Recurso | URL |
|---------|-----|
| **Demonstração** | https://rataplam.com.br |
| **Admin** | http://localhost:90/admin |
| **Documentação da API** | `LaravelEcomm.postman_collection.json` |
| **Frontend** | http://localhost:90 |

---

<p align="center">Copyright © 2026 kdkhost. Todos os direitos reservados. Construído com ❤️ usando Laravel 12</p>
