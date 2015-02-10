<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsImageTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_image_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('image_id')->unsigned();
			$table->integer('locale_id')->unsigned();
			$table->string('title', 255)->nullable();
			$table->string('caption',512)->nullable();
			$table->timestamps();

			$table->engine = "InnoDB";
			$table->foreign('image_id')->references('id')->on('cms_images')->onDelete('cascade');
			$table->foreign('locale_id')->references('id')->on('cms_locales')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_image_translations');
	}

}
