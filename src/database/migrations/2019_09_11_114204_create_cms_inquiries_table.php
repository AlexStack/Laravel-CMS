<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsInquiriesTable extends Migration
{
    private $config;
    private $table_name;

    public function __construct()
    {
        $this->config     = include base_path('config/laravel-cms.php');
        $this->table_name = $this->config['table_name']['inquiries'];
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable()->index('parent_id');
            $table->integer('page_id')->unsigned()->nullable()->index('page_id');
            $table->timestamps();
            $table->string('first_name', 190)->nullable();
            $table->string('last_name', 190)->nullable();
            $table->string('company_name', 190)->nullable();
            $table->string('email', 190)->nullable();
            $table->string('phone', 190)->nullable();
            $table->string('mobile', 190)->nullable();
            $table->string('street', 190)->nullable();
            $table->string('address', 190)->nullable();
            $table->string('postal_code', 190)->nullable();
            $table->string('city', 190)->nullable();
            $table->string('state', 190)->nullable();
            $table->string('country', 190)->nullable();
            $table->string('website', 190)->nullable();
            $table->string('locale', 190)->nullable();
            $table->string('page_title', 190)->nullable();
            $table->string('category', 190)->nullable();
            $table->string('my_date', 190)->nullable();
            $table->string('ip', 190)->nullable();
            $table->string('subject', 190)->nullable();
            $table->text('message', 16777215)->nullable();
            $table->string('page_url', 190)->nullable();
            $table->text('admin_comment', 16777215)->nullable();
            $table->enum('status', ['New', 'Opened', 'Answered', 'Spam', 'Archived', 'Display'])->nullable()->default('New');
            $table->boolean('sort_value')->default(0)->index('Sort');
            $table->text('extra_data_1', 16777215)->nullable();
            $table->text('extra_data_2', 16777215)->nullable();
            $table->text('extra_data_3', 16777215)->nullable();
            $table->text('extra_data_4', 16777215)->nullable();
            $table->text('extra_data_5', 16777215)->nullable();
            $table->softDeletes()->index('deleted_at');
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
