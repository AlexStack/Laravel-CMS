<?php

return [

    'top_logo' => env('LARAVEL_CMS_TOP_LOGO', 'http://image.intervention.io/img/intervention.svg'),

    'page_top' => '<div class="row justify-content-end">
        <div class="col-md text-right page_top">
            page_top_link1 page_top_top_link2
        </div>
    </div>',

    'page_footer' => '<div class="row justify-content-center">
        <div class="col-md mb-5 footer">
            page_footer page_footer footer
        </div>
    </div>',

    'upload_dir' => env('LARAVEL_CMS_UPLOAD_DIR', 'laravel-cms-uploads'),

    'homepage_route' => env('LARAVEL_CMS_HOMEPAGE_ROUTE', '/cms-home'),

    'page_route_prefix' => env('LARAVEL_CMS_PAGE_PREFIX', '/cms-'),

    'admin_route' => env('LARAVEL_CMS_BACKEND_ROUTE', '/cmsadmin'),

    /*
    |--------------------------------------------------------------------------
    | User id in admin_id_ary can access the cms backend
    |--------------------------------------------------------------------------
    */
    'admin_id_ary' => [1, 1, 1],


    /*
    |--------------------------------------------------------------------------
    | Image Re-optimize Time (seconds)
    |--------------------------------------------------------------------------
    |
    | How long the optimized image will re-create for an existing one
    | By default it's no need to recreate for 10 years
    |
    */

    'image_reoptimize_time' => env('LARAVEL_CMS_IMAGE_REOPTIMIZE_TIME', 360000000),

    'image_encode' => env('LARAVEL_CMS_IMAGE_ENCODE', 'jpg'),
    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

    ],

];
