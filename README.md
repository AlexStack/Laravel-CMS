# Amila Laravel CMS

-   Simple laravel cms for EXISTING or new laravel website
-   Only add 2 database tables, not effect your existing database tables.

## How to install

```php
composer require alexstack/laravel-cms
// Create database tables
php artisan migrate --path=./vendor/alexstack/laravel-cms/src/database/migrations/
// Add Auth route to Laravel if you didn't install yet
php artisan make:auth
// Publish config file and view files
php artisan vendor:publish --provider="AlexStack\LaravelCms\LaravelCmsServiceProvider"
```

## License

-   MIT
