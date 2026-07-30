# Multi-Tenant B2B Feature Flag & Metering API

## Architecture Overview
This is a high-performance infrastructure SaaS built with Laravel 11 and PHP 8.4. It allows B2B organizations to manage feature toggles and meter API usage across multiple environments. Client applications communicate with this engine via sub-millisecond API endpoints secured by environment-scoped, hashed access tokens.

## Local Execution
Ensure you have PHP 8.4 and Composer installed on your system.

```
git clone [https://github.com/sysbuildstate/feature-flag-engine.git](https://github.com/sysbuildstate/feature-flag-engine.git)
cd feature-flag-engine
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

## Testing
This repository enforces strict CI pipelines via GitHub Actions. Run the test suite locally before pushing.

```
php artisan test
```

