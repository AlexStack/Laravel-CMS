# Amila Laravel CMS

-   Free, open source Simple Bootstrap Laravel CMS for any EXISTING Laravel 5.x or new Laravel 6 website.
-   Only add a few database tables with prefix, not effect your existing database tables.
-   You can easy custom the database table names, the page URL path(route) and the template(theme)
-   Website is ready after install. Easy to use, simple enough but flexible.
-   Basic Laravel 5.x /Laravel 6.x syntax and blade template, no need to learn a new "language"

## How to install & uninstall (Support Laravel 5.x & Laravel 6.x)

```php
// Make sure you already configured the database in the .env
// Go to the laravel project folder and install it via composer
// Initialize the CMS (You can set up database table prefix and locale here)

composer require alexstack/laravel-cms && php artisan laravelcms


// Now you can access the cms frontend site: http://yourdomain/cms-home

// Access the backend with the FIRST USER of your site: http://yourdomain/cmsadmin

// Uninstall the CMS
php artisan laravelcms --action=uninstall

```

## Screenshot of the output of install command

![image](docs/images/min/artisan-install-command-min.png)

## Screenshot of the output of uninstall command

![image](docs/images/min/artisan-uninstall-command-min.png)

## Screenshot of the admin panel

![image](docs/images/min/all-pages-min.png)
![image](docs/images/min/settings-template-min.png)
![image](docs/images/min/create-new-page-min.png)

## Set locale language to **cn** instead of **en**

![cn_image](docs/images/min/settings-global-cn-min.png)

## Error "Route [login] not defined" while access the backend /cmsadmin/

-   This means you did not install Laravel Auth
-   Can be fixed by the below commands:

```php
// Laravel 5.x
php artisan make:auth && php artisan migrate
// Laravel 6.x
composer require laravel/ui && php artisan ui vue --auth
```

## How to log into the backend /cmsadmin/ ?

-   Amila CMS use your existing Laravel user system
-   You need to login with the FIRST USER of your site (user_id = 1)
-   You can add more admin users by change the admin_ary in config/laravel-cms.php
-   If you don't have any existing user, then register a new one via http://your-domain/register

## Why the uploaded image can not display (404 error)

-   You can fix it by create a storage public link
-   **php artisan storage:link**
-   eg. The public/storage should link to ../storage/app/public, if the public/storage is a real folder, you should remove/rename it and run "php artisan storage:link" to set up the link.

## Custom the cms route in config/laravel-cms.php

-   **homepage_route**: This is the frontend homepage. By default it is /cms-home, you can change it to / then remove the existing / route in the routes/web.php

```php
# Change homepage_route to /  in config/laravel-cms.php
'homepage_route'    => env('LARAVEL_CMS_HOMEPAGE_ROUTE', '/'),

# Remove the existing / route in the routes/web.php

// Route::get('/', function () {
//     return view('welcome');
// });
```

-   **page_route_prefix**: This is the frontend page prefix. By default it is /cms-, it will match path like /cms-\*. You can change it to a folder like /xxx/ or anything like xxx-, eg. Page- Article-

```php
'page_route_prefix' => env('LARAVEL_CMS_PAGE_PREFIX', '/Article-'),
```

-   **admin_route**: This is the backend admin page route, By default it is /cmsadmin

```php
'admin_route'       => env('LARAVEL_CMS_BACKEND_ROUTE', '/admin2019'),
```

-   After change the route, you will need to run below commands:
    -   php artisan config:cache
    -   or
    -   php artisan optimize

## Display an image with different size in the frontend Laravel .blade.php template file

-   .blade.php Code examples:

```php
@if ( isset($file_data->main_image) )
    <img src="{{$helper->imageUrl($file_data->main_image, '1000') }}" class="img-fluid" />

    <img src="{{$helper->imageUrl($file_data->main_image, '500') }}" class="img-fluid" />

    <img src="{{$helper->imageUrl($file_data->main_image, 'w', '150') }}" class="img-fluid" />

    <img src="{{$helper->imageUrl($file_data->main_image, '100', '100') }}" class="img-fluid" />

    <img src="{{$helper->imageUrl($file_data->main_image, 'original', 'original') }}" class="img-fluid" />

@endif

```

-   You can get an image with any width and height. or use the original image.
-   Available image variables: $file_data->main_image, $file_data->main_banner, $file_data->extra_image, $file_data->extra_image_2
-   The CMS will resize the image at the first time, then will directly use it afterwards.

## How to change the CSS & JS assets of the frontend?

-   The asset files located at public/laravel-cms/<theme_name>, eg. public/laravel-cms/frontend/css
-   Example code to load css or js:

```php
<link rel="stylesheet" href="{{ $helper->assetUrl('css/main.css') }}">
...
<script src="{{ $helper->assetUrl('js/bottom.js') }}"></script>
```

-   The default template file will load css and js asset with last_modify_time parameter to avoid cache from browser

## How to set up a different template theme from the default?

-   Copy the default theme folder /resources/views/laravel-cms/**frontend** to /resources/views/laravel-cms/**new_theme**
-   Change the frontend_dir in the settings page to **new_theme**
-   Default value in config/laravel-cms.php

```php
    'template' => [
        'frontend_dir'      => 'frontend',
        'backend_dir'       => 'backend',
        'backend_language'  => 'en',
        'frontend_language' => 'en',
    ]
```

-   run **php artisan config:cache** to load new config file
-   Change template settings for the pages in the backend
-   The css/js asset files will locate at public/laravel-cms/**new_theme**

## Set default slug format and suffix for page SEO URL in config/laravel-cms.php

-   You can change it in the settings page
-   'slug_format' can be from_title, id, pinyin
-   'slug_suffix' can be anything you want, empty means no suffix

```php
    'slug_format'   => 'from_title',
    'slug_suffix'   => '.html',
    'slug_separate' => '-',
```

## How to set up a brand new Laravel 6.x website & install our CMS

-   It's good for a local test

```php
// Install Laravel 6.x & the CMS package
composer create-project --prefer-dist laravel/laravel cms && cd cms && composer require alexstack/laravel-cms

// Then you need to change the database settings in the .env, after that initialize CMS
cd cms & vi .env
php artisan laravelcms

// Or initialize the CMS with silent mode
php artisan laravelcms --action=initialize --locale=en --table_prefix=cms_  --silent=yes

// Enable auth system for Laravel 6.x
composer require laravel/ui && php artisan ui vue --auth && php artisan migrate

// Config the document root to point to the cms/public then you can access the backend
// Tips: You will need register a new user, the first user will be the admin user
```

## How to upgrade the CMS?

-   First, run composer require alexstack/laravel-cms to upgrade the package
-   Then follow the document to upgrade database and template if needed
-   Override the frontend tempalte if you didn't change anything

```php
php artisan vendor:publish --tag=view --force  --provider=AlexStack\LaravelCms\LaravelCmsServiceProvider
```

## License

-   MIT

```

```
