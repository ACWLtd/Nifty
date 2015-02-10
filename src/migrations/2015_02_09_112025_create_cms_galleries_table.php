<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsGalleriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_galleries', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 255);
			$table->string('slug', 512);
			$table->timestamps();

			$table->index('slug');
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
		Schema::drop('cms_galleries');
	}

}
