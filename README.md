# Construction Ledger

Construction Ledger — Laravel-приложение для учёта строительства частного дома: покупок стройматериалов, складских движений, списаний, услуг подрядчиков и аналитики затрат.

## Стек

- PHP 8.3+
- Laravel 12/13 compatible skeleton
- Laravel Sanctum для token-based API
- Filament для web/admin панели
- SQLite для локального старта, MySQL/PostgreSQL для production

## Быстрый старт

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

Filament-панель открывается по адресу `/admin`.

Демо-пользователь:

- email: `demo@example.com`
- password: `password`

## Проверки

```bash
php artisan migrate:fresh --seed
php artisan test
```

## Реализованное ядро MVP

- Проекты строительства с владельцем (`user_id`).
- Справочники единиц измерения, материалов, исполнителей и тегов.
- Покупки материалов со строками и автоматическим приходом на склад.
- Списания материалов с запретом отрицательных остатков и средневзвешенной себестоимостью.
- Корректировки склада через движения типа `adjustment`.
- Услуги с типами расчёта `hourly`, `fixed`, `unit`.
- REST API под будущее мобильное приложение, отделённое от Filament.
- Сервисный слой `InventoryService`, `CostAnalyticsService`, `TagFilterService`.
- Actions для транзакционных операций покупок, списаний, услуг и корректировок.
- Seeders с русскими демо-данными.

## API

Все маршруты, кроме `/api/login`, защищены Sanctum.

```text
POST /api/login
POST /api/logout
GET  /api/user

GET|POST /api/projects
GET|PATCH|DELETE /api/projects/{project}

GET|POST /api/projects/{project}/materials
GET|PATCH|DELETE /api/materials/{material}

GET|POST /api/projects/{project}/material-purchases
GET|PATCH|DELETE /api/material-purchases/{purchase}

GET|POST /api/projects/{project}/material-write-offs
GET|PATCH|DELETE /api/material-write-offs/{writeOff}

GET|POST /api/projects/{project}/service-entries
GET|PATCH|DELETE /api/service-entries/{serviceEntry}

GET  /api/projects/{project}/inventory
GET  /api/projects/{project}/inventory/movements
POST /api/projects/{project}/inventory/adjustments

GET /api/projects/{project}/analytics/summary
GET /api/projects/{project}/analytics/by-tags
GET /api/projects/{project}/analytics/by-months
GET /api/projects/{project}/analytics/by-contractors
GET /api/projects/{project}/analytics/by-materials
```

## Архитектурные правила

Складской остаток не хранится в `materials`. Источником правды является таблица `inventory_movements`:

```text
SUM(in.quantity) - SUM(out.quantity) + SUM(adjustment.quantity)
```

Фактические платежи и себестоимость этапов разделены:

- фактические платежи: покупки материалов + услуги;
- себестоимость этапов: списания материалов + услуги.

Денежные поля хранятся как `decimal(14,2)`, количества — как `decimal(14,3)`, часы — как `decimal(10,2)`.
