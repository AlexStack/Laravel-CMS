<?php

Route::group([
    'namespace'  => 'AlexStack\LaravelCms\Http\Controllers',
], function () {
    Route::get(config('laravel-cms.admin_route'), 'LaravelCmsPageAdminController@dashboard')->name('LaravelCmsAdmin.index');

    Route::resource(config('laravel-cms.admin_route') . '/pages', 'LaravelCmsPageAdminController',  ['names' => 'LaravelCmsAdminPages']);

    Route::get(config('laravel-cms.homepage_route'), 'LaravelCmsPageController@index')->name('LaravelCmsPages.index');
    Route::get(config('laravel-cms.page_route_prefix') . '{slug}', 'LaravelCmsPageController@show')->name('LaravelCmsPages.show');
});

Route::group([
    'namespace'  => 'AlexStack\LaravelCms\Helpers',
], function () {
    Route::post(config('laravel-cms.page_route_prefix') . 'Submit-Inquiry', 'LaravelCmsPluginInquiry@submitForm')->name('LaravelCmsPluginInquiry.submitForm');

    Route::post(config('laravel-cms.admin_route') . '/search-inquiries', 'LaravelCmsPluginInquiry@search')->name('LaravelCmsPluginInquiry.search');
});
