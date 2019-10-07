<?php

Route::group([
    'namespace' => 'AlexStack\LaravelCms\Http\Controllers',
], function () {
    Route::resource(config('laravel-cms.admin_route').'/dashboard', 'LaravelCmsDashboardAdminController', ['names' => 'LaravelCmsAdmin'])->middleware(['web', 'auth']);

    Route::get(config('laravel-cms.admin_route'), 'LaravelCmsDashboardAdminController@dashboard')->middleware(['web', 'auth']);

    Route::resource(config('laravel-cms.admin_route').'/pages', 'LaravelCmsPageAdminController', ['names' => 'LaravelCmsAdminPages'])->middleware(['web', 'auth']);

    Route::resource(config('laravel-cms.admin_route').'/settings', 'LaravelCmsSettingAdminController', ['names' => 'LaravelCmsAdminSettings'])->middleware(['web', 'auth']);

    Route::resource(config('laravel-cms.admin_route').'/files', 'LaravelCmsFileAdminController', ['names' => 'LaravelCmsAdminFiles'])->middleware(['web', 'auth']);

    Route::resource(config('laravel-cms.admin_route').'/plugins', 'LaravelCmsPluginAdminController', ['names' => 'LaravelCmsAdminPlugins'])->middleware(['web', 'auth']);

    //Route::resource(config('laravel-cms.admin_route').'/templates', 'LaravelCmsTemplateAdminController', ['names' => 'LaravelCmsAdminTemplates'])->middleware(['web', 'auth']);

    // frontend routes
    Route::get(config('laravel-cms.homepage_route'), 'LaravelCmsPageController@index')->name('LaravelCmsPages.index');
    Route::get(config('laravel-cms.page_route_prefix').'{slug}', 'LaravelCmsPageController@show')->name('LaravelCmsPages.show');
});

Route::group([
    'namespace' => 'AlexStack\LaravelCms\Helpers',
], function () {
    Route::post(config('laravel-cms.page_route_prefix').'Submit-Inquiry', 'LaravelCmsPluginInquiry@submitForm')->name('LaravelCmsPluginInquiry.submitForm');

    Route::post(config('laravel-cms.admin_route').'/search-inquiries', 'LaravelCmsPluginInquiry@search')->name('LaravelCmsPluginInquiry.search')->middleware(['web', 'auth']);

    Route::resource(config('laravel-cms.admin_route').'/inquiries', 'LaravelCmsPluginInquiry', ['names' => 'LaravelCmsPluginInquiry'])->middleware(['web', 'auth']);
});
