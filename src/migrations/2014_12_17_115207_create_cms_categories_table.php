<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cms_categories', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->timestamps();

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
        Schema::drop('cms_categories');
	}

}
