<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_images', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('gallery_id')->unsigned();
			$table->string('title', 255)->nullable();
			$table->string('caption',512)->nullable();
			$table->string('url', 512);
			$table->integer('order');
			$table->timestamps();

			$table->engine = "InnoDB";
			$table->foreign('gallery_id')->references('id')->on('cms_galleries')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_images');
	}

}
