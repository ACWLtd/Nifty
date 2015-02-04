<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsCategoryTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_category_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('category_id')->unsigned();
			$table->integer('locale_id')->unsigned();
			$table->string('name', 255);
			$table->timestamps();

			$table->foreign('category_id')->references('id')->on('cms_categories')->onDelete('cascade');
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
		Schema::drop('cms_category_translations');
	}

}
