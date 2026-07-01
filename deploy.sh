#!/bin/bash
# =============================================================================
# Script de Deploy para cPanel / WHM
# =============================================================================
# Uso: bash deploy.sh
# Execute na raiz do projeto após fazer upload dos arquivos via Git ou FTP.
# =============================================================================

set -e

echo "========================================"
echo "  Iniciando deploy da Loja Rataplam..."
echo "========================================"

# 1. Verificar PHP
if ! command -v php &> /dev/null; then
    echo "[ERRO] PHP não encontrado. Verifique se o PHP está instalado."
    exit 1
fi

echo "[OK] PHP $(php -r 'echo PHP_VERSION;')"

# 2. Verificar composer
if ! command -v composer &> /dev/null; then
    echo "[AVISO] Composer não encontrado. Instale via cPanel ou manualmente."
    echo "  curl -sS https://getcomposer.org/installer | php"
    echo "  mv composer.phar ~/bin/composer"
fi

# 3. Instalar dependências (sem dev)
if command -v composer &> /dev/null; then
    echo "[INFO] Instalando dependências (production)..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# 4. Verificar/criar .env
if [ ! -f .env ]; then
    echo "[INFO] .env não encontrado. Copiando .env.example..."
    cp .env.example .env
    echo "[ATENÇÃO] Edite o .env com as configurações do seu banco MySQL!"
    echo "  DB_CONNECTION=mysql"
    echo "  DB_DATABASE=..."
    echo "  DB_USERNAME=..."
    echo "  DB_PASSWORD=..."
    echo "  APP_URL=https://seudominio.com.br"
    echo "  APP_ENV=production"
    echo "  APP_DEBUG=false"
    exit 1
fi

# 5. Gerar APP_KEY se vazio
APP_KEY=$(grep ^APP_KEY= .env | cut -d= -f2)
if [ -z "$APP_KEY" ]; then
    echo "[INFO] Gerando APP_KEY..."
    php artisan key:generate --force
fi

# 6. Permissões (storage + bootstrap/cache)
echo "[INFO] Ajustando permissões..."
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/assets public/frontend 2>/dev/null || true

# 7. Cache
echo "[INFO] Limpando caches..."
php artisan optimize:clear 2>/dev/null || true

# 8. Migrations
echo "[INFO] Rodando migrations..."
php artisan migrate --force

# 9. Seeders (apenas se tabelas vazias)
USERS=$(php -r "echo \\Modules\\User\\Models\\User::count();" 2>/dev/null || echo "0")
if [ "$USERS" = "0" ]; then
    echo "[INFO] Banco vazio — rodando seeders..."
    php artisan db:seed --force
fi

# 10. Cache de produção
echo "[INFO] Compilando assets de produção..."
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# 11. Link simbólico storage (se não existir)
if [ ! -L public/storage ]; then
    echo "[INFO] Criando link simbólico storage..."
    php artisan storage:link 2>/dev/null || true
fi

# 12. MercadoPago config check
MP_TOKEN=$(grep ^MERCADO_PAGO_ACCESS_TOKEN= .env | cut -d= -f2)
if [ -z "$MP_TOKEN" ]; then
    echo "[AVISO] MERCADO_PAGO_ACCESS_TOKEN está vazio."
    echo "  Configure no .env ou pelo admin em Configurações > Pagamentos > MercadoPago."
fi

echo "========================================"
echo "  Deploy concluído!"
echo "========================================"
echo ""
echo "Próximos passos:"
echo "  1. Acesse https://seudominio.com.br/admin para configurar a loja"
echo "  2. Configure o webhook do MercadoPago:"
echo "     URL: https://seudominio.com.br/mercadopago/webhook"
echo "     Eventos: payment"
echo "  3. Edite .env se necessário:"
echo "     APP_URL, MAIL_*, MERCADO_PAGO_*, etc."
