<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsPagesTable extends Migration
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
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned()->nullable()->index('user_id');
            $table->integer('parent_id')->unsigned()->nullable()->index('parent_id');
            $table->boolean('menu_enabled')->nullable()->default(0);
            $table->string('status', 190)->nullable()->index('status');
            $table->string('title', 190)->nullable();
            $table->string('menu_title', 190)->nullable();
            $table->string('slug', 190)->nullable()->index('slug');
            $table->string('template_file', 190)->nullable();
            $table->string('meta_title', 190)->nullable();
            $table->string('meta_keywords', 190)->nullable();
            $table->text('meta_description', 65535)->nullable();
            $table->text('abstract', 65535)->nullable();
            $table->integer('main_banner')->unsigned()->nullable()->index('main_banner');
            $table->integer('main_image')->unsigned()->nullable()->index('main_image');
            $table->text('sub_content', 65535)->nullable();
            $table->text('main_content', 65535)->nullable();
            $table->smallInteger('sort_value')->unsigned()->nullable()->index('sort_value');
            $table->integer('view_counts')->unsigned()->nullable()->index('view_counts');
            $table->string('tags', 190)->nullable();
            $table->integer('extra_image_1')->unsigned()->nullable()->index('extra_image');
            $table->text('extra_text_1', 65535)->nullable();
            $table->text('extra_content_1', 65535)->nullable();
            $table->integer('extra_image_2')->unsigned()->nullable()->index('extra_image_2');
            $table->text('extra_text_2', 65535)->nullable();
            $table->text('extra_content_2', 65535)->nullable();

            $table->integer('extra_image_3')->unsigned()->nullable()->index('extra_image_3');
            $table->text('extra_text_3', 65535)->nullable();
            $table->text('extra_content_3', 65535)->nullable();
            $table->text('special_text', 65535)->nullable();

            $table->text('file_data', 65535)->nullable();
            $table->string('redirect_url', 190)->nullable();
            $table->softDeletes()->index('deleted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop($this->table_name);
    }
}
