FROM php:8.2-apache

# 1. Instalar dependências essenciais do sistema e extensões do PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql gd zip opcache

# 2. Ativar módulo mod_rewrite do Apache para as rotas do Laravel funcionarem
RUN a2enmod rewrite

# 3. Configurar DocumentRoot do Apache para a pasta 'public' do Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Instalar o Composer (gerenciador de dependências PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Definir o diretório de trabalho
WORKDIR /var/www/html

# 6. Copiar os arquivos do projeto para o container
# Atenção: node_modules e arquivos não vitais são ignorados via .dockerignore
COPY . /var/www/html/

# 7. Instalar dependências PHP ignorando as de desenvolvimento (melhor para produção)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 8. Copiar o script de inicialização e dar permissão de execução
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# 9. Ajustar dono dos arquivos de forma genérica (o script de entrypoint fará o ajuste fino)
RUN chown -R www-data:www-data /var/www/html

ENTRYPOINT ["docker-entrypoint.sh"]

# 10. Iniciar o servidor web Apache em foreground
CMD ["apache2-foreground"]
