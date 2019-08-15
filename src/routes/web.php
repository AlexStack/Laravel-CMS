<?php

Route::group([
    'namespace'  => 'AlexStack\LaravelCms\Http\Controllers',
], function () {

    Route::resource('laravelCms/pages', 'LaravelCmsPageAdminController',  ['names' => 'LaravelCmsAdminPages']);

    Route::get('/cms-home', 'LaravelCmsPageController@index')->name('LaravelCmsPages.index');
    Route::get('cms-{slug}', 'LaravelCmsPageController@show')->name('LaravelCmsPages.show');
});
