# Amila Laravel CMS

-   Simple laravel cms for EXISTING or new laravel website
-   Only add 2 database tables, not effect your existing database tables.

## How to install

```php
composer require alexstack/laravel-cms

// Publish config file and view files
// You can custom the database table name and route
// by change the settings in config/laravel-cms.php
php artisan vendor:publish --provider="AlexStack\LaravelCms\LaravelCmsServiceProvider"


// Create database tables
php artisan migrate --path=./vendor/alexstack/laravel-cms/src/database/migrations/
// Load test data
php artisan db:seed --class='AlexStack\LaravelCms\CmsPagesTableSeeder'

// Now you can access the cms frontend site: http://yourdomain/cms-home

// Access backend admin: http://yourdomain/cmsadmin

// Initial Laravel Auth if you see error "Route [login] not defined"
php artisan make:auth
php artisan migrate

// Create a link if the uploaded image show 404 error
php artisan storage:link

// Clear and cache the config file if you make a change
php artisan config:cache

```

## Custom the cms database table name in config/laravel-cms.php

-   Here you can define your own database table name as you want
-   By default the table names are below:

```php
'table_name' => [

    'pages' => 'laravelcms_pages',

    'files' => 'laravelcms_files',
]
```

## Custom the cms route in config/laravel-cms.php

-   **homepage_route**: This is the frontend homepage. By default it is /cms-home, you can change it to / after remove the existing / route in the routes/web.php
-   **page_route_prefix**: This is the frontend page prefix. By default it is /cms-, it will match path like /cms-\*. You can change it to a folder like /xxx/ or anything like xxx-, eg. Page- Article-
-   **admin_route**: This is the backend admin page route, By default it is /cmsadmin
-   After change the route, you will need to run below commands:
    -   php artisan optimize

## License

-   MIT

```

```
