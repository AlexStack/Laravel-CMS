<?php

Route::group([
    'namespace'  => 'AlexStack\LaravelCms\Http\Controllers',
], function () {
    Route::resource(config('laravel-cms.admin_route') . '/dashboard', 'LaravelCmsDashboardAdminController',  ['names' => 'LaravelCmsAdmin']);

    Route::get(config('laravel-cms.admin_route'), 'LaravelCmsDashboardAdminController@dashboard');

    Route::resource(config('laravel-cms.admin_route') . '/pages', 'LaravelCmsPageAdminController',  ['names' => 'LaravelCmsAdminPages']);

    Route::resource(config('laravel-cms.admin_route') . '/settings', 'LaravelCmsSettingAdminController',  ['names' => 'LaravelCmsAdminSettings']);

    Route::get(config('laravel-cms.homepage_route'), 'LaravelCmsPageController@index')->name('LaravelCmsPages.index');
    Route::get(config('laravel-cms.page_route_prefix') . '{slug}', 'LaravelCmsPageController@show')->name('LaravelCmsPages.show');

    Route::resource(config('laravel-cms.admin_route') . '/files', 'LaravelCmsFileAdminController',  ['names' => 'LaravelCmsAdminFiles']);
});

Route::group([
    'namespace'  => 'AlexStack\LaravelCms\Helpers',
], function () {
    Route::post(config('laravel-cms.page_route_prefix') . 'Submit-Inquiry', 'LaravelCmsPluginInquiry@submitForm')->name('LaravelCmsPluginInquiry.submitForm');

    Route::post(config('laravel-cms.admin_route') . '/search-inquiries', 'LaravelCmsPluginInquiry@search')->name('LaravelCmsPluginInquiry.search')->middleware(['web', 'auth']);

    Route::resource(config('laravel-cms.admin_route') . '/inquiries', 'LaravelCmsPluginInquiry',  ['names' => 'LaravelCmsPluginInquiry'])->middleware(['web', 'auth']);
});
