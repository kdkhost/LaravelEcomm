<?php

declare(strict_types=1);

namespace Modules\Front\Services;

use Illuminate\Support\Str;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

class SeoService
{
    /**
     * Generate dynamic meta title
     */
    public function generateTitle(string $title, string $type = 'page', ?string $siteName = null): string
    {
        $siteName = $siteName ?? config('app.name');

        return match ($type) {
            'product' => "{$title} - Compre Online | {$siteName}",
            'category' => "{$title} - Produtos | {$siteName}",
            'brand' => "{$title} - Produtos | {$siteName}",
            'blog' => "{$title} - Blog | {$siteName}",
            'home' => "{$siteName} - Loja de Roupas Infantis",
            default => "{$title} | {$siteName}",
        };
    }

    /**
     * Generate dynamic meta description
     */
    public function generateDescription(string $content, string $type = 'page', int $length = 160): string
    {
        $description = strip_tags($content);
        $description = Str::limit($description, $length);

        return match ($type) {
            'product' => "Compre {$description} online. Entrega rápida, pagamento seguro e ótimos preços. Peça já!",
            'category' => "Confira nossa coleção de {$description}. Produtos de qualidade com preços especiais. Compre agora!",
            'brand' => "Descubra os produtos {$description}. Loja oficial com itens originais e entrega rápida.",
            'blog' => "Leia sobre {$description}. Últimas novidades, dicas e insights do nosso blog.",
            default => $description,
        };
    }

    /**
     * Generate keywords for a page
     */
    public function generateKeywords(array $baseKeywords, string $type = 'page', ?object $model = null): string
    {
        $keywords = $baseKeywords;

        if ($model) {
            $keywords = array_merge($keywords, $this->extractModelKeywords($model));
        }

        // Add type-specific keywords
        $keywords = array_merge($keywords, $this->getTypeKeywords($type));

        return implode(', ', array_unique($keywords));
    }

    /**
     * Generate canonical URL
     */
    public function generateCanonicalUrl(string $route, array $parameters = []): string
    {
        return route($route, $parameters);
    }

    /**
     * Generate Open Graph data
     */
    public function generateOpenGraphData(string $type, ?object $model = null): array
    {
        $baseData = [
            'og:type' => $type,
            'og:site_name' => config('app.name'),
            'og:locale' => 'pt_BR',
        ];

        if ($model) {
            $baseData = array_merge($baseData, $this->getModelOpenGraphData($model));
        }

        return $baseData;
    }

    /**
     * Generate Twitter Card data
     */
    public function generateTwitterCardData(string $type, ?object $model = null): array
    {
        $baseData = [
            'twitter:card' => 'summary_large_image',
            'twitter:site' => '@'.config('app.name'),
        ];

        if ($model) {
            $baseData = array_merge($baseData, $this->getModelTwitterData($model));
        }

        return $baseData;
    }

    /**
     * Generate breadcrumb data
     */
    public function generateBreadcrumbs(array $items): array
    {
        $breadcrumbs = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => route('front.index'),
            ],
        ];

        $position = 2;
        foreach ($items as $item) {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs,
        ];
    }

    /**
     * Generate product schema
     */
    public function generateProductSchema(Product $product): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->title,
            'description' => $product->description,
            'image' => $product->imageUrl,
            'url' => route('front.product-detail', $product->slug),
            'sku' => $product->sku,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand?->title ?? 'Unknown',
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $product->price,
                'priceCurrency' => config('app.default_currency', 'BRL'),
                'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => config('app.name'),
                ],
            ],
        ];

        if ($product->categories->isNotEmpty()) {
            $schema['category'] = $product->categories->first()->title;
        }

        if ($product->discount && $product->discount > 0) {
            $schema['offers']['priceValidUntil'] = now()->addDays(30)->format('Y-m-d');
        }

        return $schema;
    }

    /**
     * Generate organization schema
     */
    public function generateOrganizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'logo' => config('app.url').'/assets/img/logo/logo.png',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => config('app.currency_symbol') === 'R$' ? '+55-11-99999-9999' : '+1-555-123-4567',
                'contactType' => 'customer service',
                'areaServed' => config('app.default_currency') === 'BRL' ? 'BR' : 'US',
                'availableLanguage' => config('app.default_currency') === 'BRL' ? 'Portuguese' : 'English',
            ],
            'sameAs' => [
                'https://www.facebook.com/yourpage',
                'https://www.twitter.com/yourpage',
                'https://www.instagram.com/yourpage',
            ],
        ];
    }

    /**
     * Generate website schema
     */
    public function generateWebsiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => config('app.url').'/product/search?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Extract keywords from model
     */
    private function extractModelKeywords(object $model): array
    {
        $keywords = [];

        if ($model instanceof Product) {
            $keywords[] = $model->title;
            $keywords[] = $model->brand?->title;
            $keywords[] = $model->categories->pluck('title')->toArray();
            $keywords[] = 'comprar online';
            $keywords[] = 'loja infantil';
        } elseif ($model instanceof Category) {
            $keywords[] = $model->title;
            $keywords[] = 'produtos';
            $keywords[] = 'comprar';
        } elseif ($model instanceof Brand) {
            $keywords[] = $model->title;
            $keywords[] = 'loja oficial';
            $keywords[] = 'original';
        } elseif ($model instanceof Post) {
            $keywords[] = $model->title;
            $keywords[] = $model->tags->pluck('title')->toArray();
            $keywords[] = 'blog';
            $keywords[] = 'artigo';
        }

        return array_filter(array_merge(...array_map(fn ($k): array => is_array($k) ? $k : [$k], $keywords)));
    }

    /**
     * Get type-specific keywords
     */
    private function getTypeKeywords(string $type): array
    {
        return match ($type) {
            'product' => ['comprar online', 'ecommerce', 'moda infantil', 'roupas infantis'],
            'category' => ['produtos', 'loja', 'categorias', 'moda'],
            'brand' => ['oficial', 'original', 'marca', 'autêntico'],
            'blog' => ['blog', 'artigo', 'notícias', 'dicas', 'guia'],
            'home' => ['moda infantil', 'roupas', 'loja de roupas', 'bebê', 'criança'],
            default => ['loja', 'moda', 'roupas'],
        };
    }

    /**
     * Get model-specific Open Graph data
     */
    private function getModelOpenGraphData(object $model): array
    {
        if ($model instanceof Product) {
            return [
                'og:title' => $this->generateTitle($model->title, 'product'),
                'og:description' => $this->generateDescription($model->summary ?? $model->description, 'product'),
                'og:image' => $model->imageUrl,
                'og:url' => route('front.product-detail', $model->slug),
                'product:price:amount' => $model->price,
                'product:price:currency' => config('app.default_currency', 'BRL'),
                'product:availability' => $model->stock > 0 ? 'in stock' : 'out of stock',
            ];
        }
        if ($model instanceof Post) {
            return [
                'og:title' => $this->generateTitle($model->title, 'blog'),
                'og:description' => $this->generateDescription($model->summary ?? $model->description, 'blog'),
                'og:image' => $model->imageUrl,
                'og:url' => route('front.blog-detail', $model->slug),
                'article:published_time' => $model->created_at->toISOString(),
                'article:author' => $model->user?->name ?? 'Admin',
            ];
        }
        if ($model instanceof Category) {
            return [
                'og:title' => $this->generateTitle($model->title, 'category'),
                'og:description' => $this->generateDescription($model->summary ?? $model->description, 'category'),
                'og:url' => route('front.product-cat', $model->slug),
            ];
        }

        return [];
    }

    /**
     * Get model-specific Twitter data
     */
    private function getModelTwitterData(object $model): array
    {
        if ($model instanceof Product) {
            return [
                'twitter:title' => $this->generateTitle($model->title, 'product'),
                'twitter:description' => $this->generateDescription($model->summary ?? $model->description, 'product'),
                'twitter:image' => $model->imageUrl,
            ];
        }
        if ($model instanceof Post) {
            return [
                'twitter:title' => $this->generateTitle($model->title, 'blog'),
                'twitter:description' => $this->generateDescription($model->summary ?? $model->description, 'blog'),
                'twitter:image' => $model->imageUrl,
            ];
        }

        return [];
    }
}
