<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsPagesPostsMetaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_pages_posts_meta', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('page_id')->unsigned()->nullable();
			$table->integer('post_id')->unsigned()->nullable();
			$table->integer('page_translation_id')->unsigned()->nullable();
			$table->integer('post_translation_id')->unsigned()->nullable();
			$table->string('meta_key', 512);
			$table->mediumText('meta_value');
			$table->timestamps();

			$table->index('meta_key');
			$table->engine = "InnoDB";

			$table->foreign('page_id')->references('id')->on('cms_pages')->onDelete('cascade');
			$table->foreign('post_id')->references('id')->on('cms_posts')->onDelete('cascade');
			$table->foreign('page_translation_id')->references('id')->on('cms_page_translations')->onDelete('cascade');
			$table->foreign('post_translation_id')->references('id')->on('cms_post_translations')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_pages_posts_meta');
	}

}
