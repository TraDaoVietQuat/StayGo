FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    libonig-dev \
    libsodium-dev \
    git \
    unzip \
    curl \
    autoconf \
    g++ \
    make \
    && docker-php-ext-install \
        intl \
        pdo_mysql \
        zip \
        gd \
        bcmath \
        opcache \
        sodium \
        mbstring \
        xml \
        fileinfo \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs && apt-get clean

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

COPY package.json ./
RUN npm install

COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && npm run build

RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chmod -R 775 storage bootstrap/cache

CMD php artisan storage:link --force 2>/dev/null; \
    php artisan config:cache; \
    php artisan route:cache; \
    php artisan view:cache; \
    php artisan migrate --force; \
    php artisan db:seed --class=ReplaceHotelsSeeder --force --no-interaction 2>/dev/null; \
    php artisan db:seed --class=ReplaceBlogPostsSeeder --force --no-interaction 2>/dev/null; \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}