<?php

Route::group([
    'namespace'  => 'AlexStack\LaravelCms\Http\Controllers',
], function () {
    Route::get(config('laravel-cms.admin_route'), 'LaravelCmsPageAdminController@dashboard')->name('LaravelCmsAdmin.index');

    Route::resource(config('laravel-cms.admin_route') . '/pages', 'LaravelCmsPageAdminController',  ['names' => 'LaravelCmsAdminPages']);

    Route::get(config('laravel-cms.homepage_route'), 'LaravelCmsPageController@index')->name('LaravelCmsPages.index');
    Route::get(config('laravel-cms.page_route_prefix') . '{slug}', 'LaravelCmsPageController@show')->name('LaravelCmsPages.show');
});
