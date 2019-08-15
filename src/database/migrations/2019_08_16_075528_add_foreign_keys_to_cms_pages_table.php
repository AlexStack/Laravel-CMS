<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCmsPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cms_pages', function(Blueprint $table)
		{
			$table->foreign('main_image', 'cms_pages_ibfk_1')->references('id')->on('cms_files')->onUpdate('RESTRICT')->onDelete('SET NULL');
			$table->foreign('main_banner', 'cms_pages_ibfk_2')->references('id')->on('cms_files')->onUpdate('RESTRICT')->onDelete('SET NULL');
			$table->foreign('extra_image', 'cms_pages_ibfk_3')->references('id')->on('cms_files')->onUpdate('RESTRICT')->onDelete('SET NULL');
			$table->foreign('extra_image_2', 'cms_pages_ibfk_4')->references('id')->on('cms_files')->onUpdate('RESTRICT')->onDelete('SET NULL');
			$table->foreign('parent_id', 'cms_pages_ibfk_5')->references('id')->on('cms_pages')->onUpdate('RESTRICT')->onDelete('SET NULL');
			$table->foreign('user_id', 'cms_pages_ibfk_6')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cms_pages', function(Blueprint $table)
		{
			$table->dropForeign('cms_pages_ibfk_1');
			$table->dropForeign('cms_pages_ibfk_2');
			$table->dropForeign('cms_pages_ibfk_3');
			$table->dropForeign('cms_pages_ibfk_4');
			$table->dropForeign('cms_pages_ibfk_5');
			$table->dropForeign('cms_pages_ibfk_6');
		});
	}

}
