<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsSettingsTable extends Migration
{
    private $config;
    private $table_name;

    public function __construct()
    {
        $this->config     = include base_path('config/laravel-cms.php');
        $this->table_name = $this->config['table_name']['settings'];
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('param_name', 190)->nullable()->index($this->table_name.'param_name');
            $table->integer('page_id')->unsigned()->nullable()->index($this->table_name.'page_id');
            $table->text('param_value', 65535)->nullable();
            $table->text('input_attribute', 65535)->nullable();
            $table->text('abstract', 65535)->nullable();
            $table->string('category', 190)->nullable()->index($this->table_name.'category');
            $table->boolean('enabled')->default(0)->index($this->table_name.'form_enabled');
            $table->smallInteger('sort_value')->nullable()->index($this->table_name.'sort_value');
            $table->timestamps();
            $table->softDeletes()->index($this->table_name.'deleted_at');
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
