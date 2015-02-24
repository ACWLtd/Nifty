<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsPostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cms_posts', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('title', 255);
            $table->string('slug', 512);
            $table->string('summary',512);
            $table->mediumText('content');
            $table->integer('order');
            $table->boolean('is_online')->default(0);
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();

            $table->index('slug');
            $table->engine = "InnoDB";
            $table->foreign('user_id')->references('id')->on('users');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('cms_posts');
	}

}
