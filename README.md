# Finance Data Processing and Access Control Backend

This project is a backend assignment for a finance dashboard system. It focuses on clean API design, role-based access control, financial record management, summary analytics, validation, and reliable backend behavior.

The implementation is intentionally kept practical and easy to review. The goal was not to build a production-scale system, but to show clear backend thinking, sensible structure, and maintainable code.

## What This Project Includes

- user management with roles and active/inactive status
- financial record CRUD
- filtering, search, and pagination for records
- dashboard summary APIs for totals and category breakdowns
- backend access control for `viewer`, `analyst`, and `admin`
- consistent validation and error handling
- seeded demo data for quick review
- automated feature tests
- a lightweight Blade UI for demonstrating the backend flows

## Stack

- Laravel 13
- PHP 8.4
- SQLite for local simplicity
- Laravel Sanctum for API token authentication

## Roles and Permissions

### Viewer
- can view dashboard summary
- can view financial records
- cannot create, update, or delete records
- cannot manage users

### Analyst
- can view dashboard summary
- can view financial records
- can create, update, and delete financial records
- cannot manage users

### Admin
- full access to dashboard, records, and user management
- can activate or deactivate users

## Main Backend Modules

### Authentication
- `POST /api/login`
- `POST /api/logout`
- `GET /api/me`

### User Management
- `GET /api/users`
- `POST /api/users`
- `GET /api/users/{id}`
- `PUT /api/users/{id}`
- `DELETE /api/users/{id}`

These endpoints are restricted to admins.

### Financial Records
- `GET /api/financial-records`
- `POST /api/financial-records`
- `GET /api/financial-records/{id}`
- `PUT /api/financial-records/{id}`
- `DELETE /api/financial-records/{id}`

Supported filters:
- `type`
- `category`
- `record_date`
- `date_from`
- `date_to`
- `min_amount`
- `max_amount`
- `search`
- `sort_by`
- `sort_order`
- `page`
- `per_page`

### Dashboard Summary
- `GET /api/dashboard/summary`

The dashboard summary supports the same date/category/type filters as the record list and returns:
- total income
- total expense
- balance
- record count
- category totals
- latest records

## Data Model

### Users
The `users` table stores:
- name
- email
- password
- role
- is_active

### Financial Records
The `financial_records` table stores:
- title
- description
- type (`income` or `expense`)
- category
- amount
- record_date
- created_by
- updated_by

## API Response Style

Successful responses use a consistent shape:

```json
{
  "success": true,
  "message": "Request completed successfully.",
  "data": {}
}
```

Validation failures use:

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field": ["Validation message"]
  }
}
```

## Demo Accounts

After seeding the database, these users are available:

- `kavya.admin@financecontrol.in` / `password`
- `arjun.analyst@financecontrol.in` / `password`
- `priya.viewer@financecontrol.in` / `password`

## Local Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Start here in the browser:

- `GET /login`

## Running Tests

```bash
php artisan test
```

Current automated coverage includes:
- authentication flow
- role restrictions
- financial record validation and filtering
- dashboard summary calculations
- web access and error-flow behavior

## Project Structure

- `app/Http/Controllers/Api` for REST API controllers
- `app/Http/Controllers/Web` for the demo web interface
- `app/Http/Requests` for request validation
- `app/Http/Resources` for API response formatting
- `app/Http/Middleware` for role and active-user checks
- `app/Policies` for financial record authorization
- `app/Services` for filtering and dashboard aggregation
- `app/Models` for domain entities
- `resources/views` for the Blade-based demo UI

## Assumptions

- the dashboard works on a shared finance dataset rather than user-private ledgers
- roles are stored directly on the `users` table to keep the solution simple
- inactive users should immediately lose access, but their historical records should remain
- categories are restricted to a predefined list for cleaner validation and UI simplicity
- amounts are always positive, and `type` determines whether the record is income or expense

## Tradeoffs

- SQLite is used by default for a fast local setup, though the structure is compatible with MySQL
- the project includes a small Blade UI only to make review easier; the main focus remains the backend
- dashboard analytics cover totals, recent activity, and category summaries; weekly/monthly trend endpoints were intentionally not added to avoid unnecessary complexity
- user deletion is implemented as deactivation instead of hard deletion to protect record history

## Notes

- the codebase favors simple Laravel patterns over extra abstraction
- access control is enforced at the backend level through middleware and policies
- the implementation is designed to be easy to read, explain, and extend during review
