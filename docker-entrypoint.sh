#!/bin/sh
set -e

export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_HOME=/tmp/.composer

if [ "$APP_ENV" = "dev" ] && [ ! -d "vendor" ]; then
    echo "Папка vendor не найдена. Запускаю composer install..."
    composer install --no-interaction --prefer-dist
fi

# echo "Проверка миграций..."
# php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

exec "$@"
