## Запуск dev
1. Клонируем репозиторий: 
```bash
git clone https://github.com/froghub/tcase_nvfn.git
``` 
2. Запускаем:
```bash
USER_ID=$(id -u) GROUP_ID=$(id -g) docker compose up -d
```
Ждем сборку контейнеров, установки зависимостей.   
3. Выполняем миграции
```bash
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```
4. Остановка:  
```bash
docker compose down
```

___
## Запуск prod  

1. Клонируем репозиторий:
```bash
git clone https://github.com/froghub/tcase_nvfn.git
``` 
2.Копируем и заполняем конфиг файл:
```bash
cp .env.docker.env .env.docker.prod
```  

Устанавливаем APP_ENV=prod  
Остальные - меняем на актуальные для prod-окружения  


3.Запускаем:
```bash
docker compose -f docker-compose.prod.yml up --build -d
```
Ждем сборку контейнеров, установки зависимостей.  

3. Выполняем миграции
```bash
docker compose -f docker-compose.prod.yml exec php php bin/console doctrine:migrations:migrate --no-interaction
```
4. Остановка:
```bash
docker compose down
```

Альтернативный способ запуска/миграции/остановки:  
`make up` / `make migrate` / `make down`  
`make prod-up` / `make prod-migrate` / `make prod-down`  
