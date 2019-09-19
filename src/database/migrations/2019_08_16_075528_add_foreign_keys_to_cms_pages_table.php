<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCmsPagesTable extends Migration
{
    private $config;
    private $table_name;

    public function __construct()
    {
        $this->config     = include base_path('config/laravel-cms.php');
        $this->table_name = $this->config['table_name']['pages'];
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table_name_files = $this->config['table_name']['files'];
            $table_name_pages = $this->table_name;

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
     */
    public function down()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropForeign('cms_pages_ibfk_main_image');
            $table->dropForeign('cms_pages_ibfk_main_banner');
            $table->dropForeign('cms_pages_ibfk_extra_image_1');
            $table->dropForeign('cms_pages_ibfk_extra_image_2');
            $table->dropForeign('cms_pages_ibfk_extra_image_3');
            //$table->dropForeign('cms_pages_ibfk_user_id');
        });
    }
}
