<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsInquirySettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravel-cms.table_name.inquiry_settings') ??  'cms_inquiry_settings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('form_layout')->nullable();
            $table->integer('page_id')->unsigned()->nullable()->index('page_id');
            $table->integer('default_setting_id')->unsigned()->nullable();
            $table->string('form_layout_filename')->nullable();
            $table->text('display_form_fields', 16777215)->nullable();
            $table->string('mail_from')->nullable();
            $table->string('mail_to')->nullable();
            $table->string('mail_subject')->nullable();
            $table->string('success_title')->nullable();
            $table->text('success_content', 16777215)->nullable();
            $table->text('google_recaptcha_site_key', 16777215)->nullable();
            $table->text('google_recaptcha_secret_key', 16777215)->nullable();
            $table->string('google_recaptcha_css_class')->nullable();
            $table->string('google_recaptcha_no_tick_msg')->nullable();
            $table->boolean('google_recaptcha_enabled')->default(0)->index('google_recaptcha_enabled');
            $table->boolean('form_enabled')->default(0)->index('form_enabled');
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
        Schema::drop(config('laravel-cms.table_name.inquiry_settings') ??  'cms_inquiry_settings');
    }
}
