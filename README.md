# redis-users-api

## Технологический стек
- **Backend:** Laravel 12.47.0
- **Database:** MySQL 8.0
- **Caching:** Redis 7.0 (phpredis)
- **Containerization:** Docker & Docker Compose
- **Web Server:** Nginx 1.25 (Alpine)

## Основные возможности
- Регистрация пользователей с загрузкой аватара.
- Список пользователей с отображением аватара.
- Кэширование списка пользователей в Redis (время жизни 10 минут, автоматический сброс при новой регистрации и при очистке старых пользователей через job).
- Ежедневная автоматическая очистка пользователей и их аватаров, созданных более месяца назад.
- Полное покрытие тестами (Feature & Unit).

---

## Установка и запуск

### 1. Склонируйте репозиторий
```bash
git clone https://github.com/Vlad1Self/redis-users-api.git
cd redis-users-api
```

### 2. Подготовьте окружение
Создайте файл `.env`:
```bash
cp .env.example .env
```

### 3. Сборка и запуск контейнеров
Запустите Docker Compose:
```bash
docker compose up -d --build
```

### 4. Настройка приложения (Выполняется разово от root)
Поскольку в Docker на Linux смонтированные папки принадлежат пользователю хоста, первый запуск и настройка прав выполняются от имени `root`:

```bash
# Установка всех зависимостей (включая dev для тестов)
docker compose exec -u root app composer install

# Настройка прав доступа (чтобы www-data мог писать логи и кэш)
docker compose exec -u root app chown -R www-data:www-data storage bootstrap/cache
docker compose exec -u root app chmod -R 775 storage bootstrap/cache

# Генерация ключа, миграции и создание симлинка (от root для записи в .env и public)
docker compose exec -u root app php artisan key:generate
docker compose exec -u root app php artisan migrate --seed
docker compose exec -u root app php artisan storage:link --relative
```

Теперь приложение доступно по адресу: **http://localhost:8080**

---

## Тестирование и разработка (Выполняется от www-data)

Все повседневные команды запускаются от имени пользователя `www-data` для соблюдения прав доступа:

### Запуск тестов
```bash
docker compose exec -u www-data app sh -c "DB_DATABASE=:memory: php artisan test"
```

### Работа с очередями и планировщиком
- **Обработка очередей:**
```bash
docker compose exec -u www-data app php artisan queue:work
```

- **Планировщик:**
```bash
docker compose exec -u www-data app php artisan schedule:work
```

---

## Структура API

### Регистрация пользователя
**POST** `/api/register`
- **Параметры:** `nickname` (строка, unique), `avatar` (файл изображения).
- **Ответ:** структура `ApiResponse` (data, meta, status).

### Список пользователей
**GET** `/users`
- Blade-страница со списком всех пользователей.
- Использует сервис `UserService` с кэшированием в Redis.
