<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsFilesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravel-cms.table_name.files') ?? 'cms_files', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned()->nullable()->index('user_id');
            $table->string('title', 190)->nullable();
            $table->text('description', 65535)->nullable();
            $table->string('suffix', 190)->nullable();
            $table->string('path', 190)->nullable();
            $table->string('filename', 190)->nullable();
            $table->string('mimetype', 190)->nullable()->index('filetype');
            $table->boolean('is_image')->nullable()->index('is_image');
            $table->boolean('is_video')->nullable()->index('is_video');
            $table->bigInteger('filesize')->unsigned()->nullable();
            $table->string('filehash', 190)->nullable()->index('filehash');
            $table->string('url', 190)->nullable();
            $table->timestamps();
            $table->softDeletes()->index('deleted_at');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(config('laravel-cms.table_name.files') ?? 'cms_files');
    }
}
