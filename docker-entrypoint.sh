#!/bin/bash
set -e

echo "=> Iniciando processo de configuração do contêiner..."

# Garantindo que os diretórios necessários existam
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public/photos
mkdir -p /var/www/html/database

# Se o banco SQLite não existir no volume mapeado, ele será copiado ou gerado vazio
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "=> Criando arquivo SQLite vazio em /var/www/html/database/database.sqlite..."
    touch /var/www/html/database/database.sqlite
fi

# Ajustando as permissões para o usuário de servidor web (www-data)
echo "=> Ajustando permissões do storage e database..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Rodando migrations no banco de dados SQLite (como é produção, force mode)
echo "=> Verificando e rodando migrações..."
php artisan migrate --force

# Recriando o storage link se ele não existir
if [ ! -d /var/www/html/public/storage ]; then
    echo "=> Criando link simbólico para o storage público..."
    php artisan storage:link
fi

# Limpando e recriando cache do Laravel para estabilidade
echo "=> Otimizando views e configs..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=> Ambiente configurado. Passando o controle para o CMD original..."

# Passa a execução de volta para o commando CMD ["apache2-foreground"] declarado no Dockerfile
exec "$@"
