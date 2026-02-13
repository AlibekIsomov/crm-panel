# CRM Panel — Каталог товаров + Склад

Микросервис каталога интернет-магазина с двумя модулями: **Catalog** и **Inventory**.

## Стек

- PHP 8.1+
- Laravel 10
- PostgreSQL 15
- Docker Compose

## Запуск

### 1. Клонировать репозиторий

```bash
git clone https://github.com/AlibekIsomov/crm-panel.git
cd crm-panel
```

### 2. Установить зависимости

```bash
composer install
```

### 3. Настроить окружение

```bash
cp .env.example .env
php artisan key:generate
```

Убедитесь, что в `.env` указаны корректные параметры БД:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=crm_panel
DB_USERNAME=postgres
DB_PASSWORD=r00t
```

### 4. Запустить Docker

```bash
docker compose up -d
```

Это поднимет три контейнера:
- **app** — PHP-FPM приложение
- **web** — Nginx (порт 8000)
- **db** — PostgreSQL 15 (порт 5432)

### 5. Выполнить миграции

```bash
php artisan migrate
```

### 6. Запустить сервер (для локальной разработки)

```bash
php artisan serve
```

API доступен по адресу: `http://127.0.0.1:8000/api/`

## Тесты

```bash
php artisan test tests/Feature/Catalog tests/Feature/Inventory tests/Unit/Catalog tests/Unit/Inventory
```

### Feature-тесты (3):
1. **Создание товара с атрибутами** — в транзакции, проверка БД
2. **Фильтрация по категории и цене** — scopes `inCategory()`, `priceRange()`
3. **Изменение остатка + StockMovement** — проверка записи движения и валидации

### Unit-тесты (2):
1. **Slug-генерация** — обычный случай + конфликт (инкремент)
2. **Сервис изменения остатков** — приход, продажа, запрет отрицательного остатка

## Архитектура модулей

```
app/Modules/
├── Catalog/
│   ├── Database/Migrations/
│   ├── DTOs/
│   │   ├── CreateProductDTO.php
│   │   └── CreateCategoryDTO.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CategoryController.php
│   │   │   └── ProductController.php
│   │   ├── Requests/
│   │   │   ├── StoreCategoryRequest.php
│   │   │   └── StoreProductRequest.php
│   │   └── Resources/
│   │       ├── CategoryResource.php
│   │       ├── ProductResource.php
│   │       └── ProductAttributeResource.php
│   ├── Models/
│   │   ├── Category.php
│   │   ├── Product.php
│   │   └── ProductAttribute.php
│   ├── Services/
│   └── routes.php
│
└── Inventory/
    ├── Database/Migrations/
    ├── DTOs/
    │   └── AdjustStockDTO.php
    ├── Enums/
    │   └── StockMovementReason.php
    ├── Http/
    │   ├── Controllers/
    │   │   └── InventoryController.php
    │   ├── Requests/
    │   │   └── AdjustStockRequest.php
    │   └── Resources/
    │       └── StockMovementResource.php
    ├── Models/
    │   ├── Stock.php
    │   └── StockMovement.php
    ├── Services/
    │   └── InventoryService.php
    └── routes.php
```

## API Endpoints

| Метод | Эндпоинт | Описание |
|---|---|---|
| GET | `/api/categories` | Дерево категорий (до 3 уровней) |
| POST | `/api/categories` | Создание категории |
| GET | `/api/products` | Список товаров (пагинация, фильтры) |
| POST | `/api/products` | Создание товара с атрибутами |
| GET | `/api/products/{slug}` | Карточка товара |
| PUT | `/api/products/{id}` | Обновление товара |
| POST | `/api/inventory/{product_id}/adjust` | Изменение остатка |
| GET | `/api/inventory/{product_id}/history` | История движений |

## Модуль Delivery (Служба доставки)

### Особенности

- **Геокодинг**: Mock-сервис для получения координат.
- **Маршрутизация**: Mock-сервис для расчета расстояния и времени (Haversine).
- **Оплата**: Mock-сервис с поддержкой Webhook и проверкой HMAC-SHA256 подписи.
- **Уведомления**: Асинхронная отправка SMS и Email через Laravel Queue.
- **Логирование**: Все внешние запросы сохраняются в `order_logs`.

### Настройка драйверов (.env)

```env
DELIVERY_GEOCODER_DRIVER=mock
DELIVERY_ROUTING_DRIVER=mock
DELIVERY_PAYMENT_DRIVER=mock
DELIVERY_NOTIFICATION_DRIVER=mock
```

### Запуск очереди

Для отправки уведомлений необходимо запустить воркер:

```bash
php artisan queue:work
```

### API Endpoints

| Метод | Эндпоинт | Описание |
|---|---|---|
| POST | `/api/orders` | Создание заказа |
| POST | `/api/orders/calculate` | Предварительный расчет стоимости |
| GET | `/api/orders/{id}` | Инфо о заказе |
| PATCH | `/api/orders/{id}/status` | Смена статуса (admin) |
| POST | `/api/orders/{id}/pay` | Получение ссылки на оплату |
| POST | `/api/webhooks/payment` | Webhook оплаты (X-Signature) |

### Тестирование

```bash
php artisan test tests/Feature/Modules/Delivery tests/Unit/Modules/Delivery
```
