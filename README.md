# CRM Панель Задача

Простой API для управления задачами и напоминаниями. Сделано на Laravel 10 (PHP 8.2).

## Что внутри
- Регистрация и вход (Sanctum)
- Задачи: создание, обновление, удаление (Soft Delete)
- Статусы задач (Pending -> In Progress -> Done/Cancelled)
- Повторяющиеся задачи (при Done создается новая копия)
- Напоминания: Email (Notification) и SMS (лог в файл)
- Очереди и Планировщик

## Установка

1. **Клонировать репозиторий**
   ```bash
   git clone https://github.com/AlibekIsomov/crm-panel.git
   cd crm-panel
   ```

2. **Установить зависимости**
   ```bash
   composer install
   ```

3. **Настроить окружение**
   Скопируйте `.env.example` в `.env`:
   ```bash
   cp .env.example .env
   ```
   Откройте `.env` и пропишите настройки базы данных (MySQL или PostgreSQL):
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=crm_panel
   DB_USERNAME=postgres
   DB_PASSWORD=r00t
   ```

4. **Генерация ключа**
   ```bash
   php artisan key:generate
   ```

5. **Миграции и Сиды** (создаст таблицы и тестовые данные)
   ```bash
   php artisan migrate --seed
   ```
   *Создадутся пользователи:*
   - Manager: `manager@crm.com` / `password`
   - Admin: `admin@crm.com` / `password`

## Запуск

1. **Сервер API**
   ```bash
   php artisan serve
   ```
   API будет доступно по адресу: `http://localhost:8000/api`

2. **Очереди** (для отправки напоминаний)
   В отдельном терминале:
   ```bash
   php artisan queue:work
   ```

3. **Планировщик** (для проверки напоминаний каждую минуту)
   В отдельном терминале:
   ```bash
   php artisan schedule:work
   ```

## Тестирование
Для запуска тестов (Feature и Unit):
```bash
php artisan test
```

## API Документация (Postman)
Файл коллекции `crm_panel.postman_collection.json` лежит в корне проекта. Импортируйте его в Postman.

### Основные эндпоинты:
- `POST /api/auth/register` - Регистрация
- `POST /api/auth/login` - Вход (получить токен)
- `GET /api/tasks` - Список задач
- `POST /api/tasks` - Создать задачу
- `GET /api/tasks/today` - Задачи на сегодня
- `GET /api/tasks/overdue` - Просроченные
- `PATCH /api/tasks/{id}/status` - Сменить статус
