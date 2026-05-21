USER_ID = $(shell id -u)
GROUP_ID = $(shell id -g)

# Запуск команд через эту переменную автоматически прокидывает ID пользователя
DOCKER_DEV = USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose

.PHONY: up down build restart migrate cc-dev cc-prod prod-up prod-down test openapi-export

# ==========================================
# КОМАНДЫ ДЛЯ РАЗРАБОТКИ (DEV)
# ==========================================

# Поднять dev-окружение (с автоматической сборкой, если нужно)
up:
	$(DOCKER_DEV) up -d

# Остановить dev-окружение
down:
	$(DOCKER_DEV) down

# Пересобрать контейнеры dev с нуля
build:
	$(DOCKER_DEV) up --build -d

# Перезапустить контейнеры dev
restart: down up

# Накатить миграции в dev
migrate:
	$(DOCKER_DEV) exec -u root php php bin/console doctrine:migrations:migrate --no-interaction

# Очистить кэш Symfony в dev
cc-dev:
	$(DOCKER_DEV) exec php php bin/console cache:clear

# Зайти в консоль PHP-контейнера (sh)
bash:
	$(DOCKER_DEV) exec php sh

test:
	docker compose exec php ./vendor/bin/simple-phpunit
openapi-export:
	docker compose exec php bin/console api:openapi:export --output=openapi.yaml
# ==========================================
# КОМАНДЫ ДЛЯ ПРОДАКШЕНА (PROD)
# ==========================================

# Собрать и поднять прод-окружение на сервере
prod-up:
	docker-compose -f docker-compose.prod.yml up --build -d

# Остановить прод-окружение на сервере
prod-down:
	docker-compose -f docker-compose.prod.yml down

# Накатить миграции в прод
prod-migrate:
	docker-compose -f docker-compose.prod.yml exec php php bin/console doctrine:migrations:migrate --no-interaction
