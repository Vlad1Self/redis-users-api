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

### 4. Настройка приложения
Установите зависимости, сгенерируйте ключ и выполните миграции:
```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan storage:link
```

Теперь приложение доступно по адресу: **http://localhost:8080**

---

## Тестирование

Для запуска тестов используйте изолированную базу данных в памяти (SQLite :memory:):

```bash
docker compose exec -u www-data app sh -c "DB_DATABASE=:memory: php artisan test"
```

## Работа с очередями и планировщиком

Для работы фоновых задач и автоматической очистки данных запустите:

- **Обработка очередей:**
```bash
docker compose exec -u www-data app php artisan queue:work
```

- **Планировщик (для локальной отладки):**
```bash
docker compose exec -u www-data app php artisan schedule:work
```
*В `routes/console.php` настроена ежедневная задача `cleanup_old_users` (03:00).*

---

## Структура API

### Регистрация пользователя
**POST** `/api/register`
- **Параметры:** `nickname` (строка, unique), `avatar` (файл изображения).
- **Ответ:** `ApiResponse` с ключами `data`, `meta`, `status`.

### Список пользователей
**GET** `/users`
- Возвращает Blade-страницу со списком всех пользователей.
- Данные подгружаются через `UserService`, который использует кэширование Redis.
