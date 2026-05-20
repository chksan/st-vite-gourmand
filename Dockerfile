FROM dunglas/frankenphp:php8.3-bookworm

RUN install-php-extensions ctype curl dom fileinfo filter hash mbstring openssl pcre pdo pdo_mysql session tokenizer xml mongodb

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --optimize-autoloader --no-scripts --no-interaction

RUN npm install && npm run build

RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache && chmod -R a+rw storage

EXPOSE 8080

CMD ["/start-container.sh"]