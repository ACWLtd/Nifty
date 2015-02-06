<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_events', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('is_approved')->default(0);
			$table->string('first_name', 255)->nullable();
			$table->string('last_name', 255)->nullable();
			$table->string('email', 255)->nullable();
			$table->string('organisation', 512)->nullable();
			$table->string('title', 255);
			$table->string('venue', 255)->nullable();
			$table->text('description')->nullable();
			$table->string('type', 255)->nullable();
			$table->date('start_date');
			$table->date('end_date');
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
		Schema::drop('cms_events');
	}

}
