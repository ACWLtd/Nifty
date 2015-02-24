<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsPageTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_page_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('page_id')->unsigned();
			$table->integer('locale_id')->unsigned();
			$table->string('title', 255);
			$table->string('slug', 512);
			$table->string('summary',512);
			$table->mediumText('content');
			$table->integer('order');
			$table->boolean('is_online')->default(0);
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('page_id')->references('id')->on('cms_pages')->onDelete('cascade');
			$table->foreign('locale_id')->references('id')->on('cms_locales')->onDelete('cascade');

			$table->engine = "InnoDB";
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_page_translations');
	}

}
