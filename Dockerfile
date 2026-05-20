FROM dunglas/frankenphp:php8.3-bookworm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions ctype curl dom fileinfo filter hash mbstring openssl pcre pdo pdo_mysql session tokenizer xml

RUN apt-get update && apt-get install -y libssl-dev pkg-config && \
    pecl install mongodb-1.17.3 && \
    docker-php-ext-enable mongodb

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --optimize-autoloader --no-scripts --no-interaction

RUN npm install && npm run build

RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache && chmod -R a+rw storage

EXPOSE 8080

CMD ["/start-container.sh"]