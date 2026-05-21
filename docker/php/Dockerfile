#FROM php:8.5-fpm-alpine
#
#
#RUN apk add --no-cache git unzip icu-dev postgresql-dev build-base autoconf \
#    && docker-php-ext-configure intl \
#    && docker-php-ext-install intl pdo pdo_pgsql \
#    && pecl install redis \
#    && docker-php-ext-enable redis \
#    && apk del build-base autoconf
#
#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#
#WORKDIR /var/www/project


# ==========================================
# STAGE 1: Базовый образ (Общий для dev и prod)
# ==========================================
FROM php:8.5-fpm-alpine AS app_base

RUN apk add --no-cache git unzip icu-dev postgresql-dev build-base autoconf \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del build-base autoconf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/project

# ==========================================
# STAGE 2: Образ для разработки (dev)
# ==========================================
FROM app_base AS app_dev

# Переменная окружения по умолчанию для этого слоя
ENV APP_ENV=dev

# Копируем и настраиваем точку входа для локальной разработки
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# ==========================================
# STAGE 3: Образ для продакшена (prod)
# ==========================================
FROM app_base AS app_prod

ENV APP_ENV=prod
ENV APP_DEBUG=0

# 1. Сначала копируем только файлы зависимостей (для кэширования слоев Docker)
COPY composer.json composer.lock ./

# 2. Ставим только prod-пакеты без запуска скриптов Symfony (их еще рано запускать)
RUN composer install --no-dev --no-interaction --no-plugins --no-scripts --prefer-dist

# 3. Копируем весь остальной код проекта внутрь образа
COPY . .

# 4. Оптимизируем автолоадер Composer и чистим кэш Symfony для prod
RUN composer dump-autoload --no-dev --classmap-authoritative \
    && php bin/console cache:clear --env=prod

CMD ["php-fpm"]
