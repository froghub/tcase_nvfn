<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/.env.test')) {
    new Dotenv()->bootEnv(dirname(__DIR__).'/.env.test');
} elseif (file_exists(dirname(__DIR__).'/.env')) {
    new Dotenv()->bootEnv(dirname(__DIR__).'/.env');
}
echo "Подготовка тестовой базы данных...\n";

passthru('php bin/console doctrine:database:drop --env=test --force --if-exists');

passthru('php bin/console doctrine:database:create --env=test');

passthru('php bin/console doctrine:migrations:migrate --env=test --no-interaction');

echo "База данных готова! Запуск PHPUnit...\n\n";
