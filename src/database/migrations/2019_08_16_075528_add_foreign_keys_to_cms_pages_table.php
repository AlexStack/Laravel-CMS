<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCmsPagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::table(config('laravel-cms.table_name.pages') ?? 'cms_pages', function (Blueprint $table) {

            $table_name_files = config('laravel-cms.table_name.files') ?? 'cms_files';
            $table_name_pages = config('laravel-cms.table_name.pages') ?? 'cms_pages';

            $table->foreign('main_image', 'cms_pages_ibfk_main_image')->references('id')->on($table_name_files)->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->foreign('main_banner', 'cms_pages_ibfk_main_banner')->references('id')->on($table_name_files)->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->foreign('extra_image_1', 'cms_pages_ibfk_extra_image_1')->references('id')->on($table_name_files)->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->foreign('extra_image_2', 'cms_pages_ibfk_extra_image_2')->references('id')->on($table_name_files)->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->foreign('extra_image_3', 'cms_pages_ibfk_extra_image_3')->references('id')->on($table_name_files)->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->foreign('parent_id', 'cms_pages_ibfk_parent_id')->references('id')->on($table_name_pages)->onUpdate('RESTRICT')->onDelete('SET NULL');
            //$table->foreign('user_id', 'cms_pages_ibfk_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('SET NULL');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('laravel-cms.table_name.pages') ??  'cms_pages', function (Blueprint $table) {
            $table->dropForeign('cms_pages_ibfk_1');
            $table->dropForeign('cms_pages_ibfk_2');
            $table->dropForeign('cms_pages_ibfk_3');
            $table->dropForeign('cms_pages_ibfk_4');
            $table->dropForeign('cms_pages_ibfk_5');
            $table->dropForeign('cms_pages_ibfk_6');
        });
    }
}
