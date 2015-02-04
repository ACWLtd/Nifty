<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertStarterData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(){
		$date_now = date('Y-m-d H:i:s');

		$data = [
			[
				'name'    => 'Administrator',
				'created_at' => $date_now,
				'updated_at' => $date_now
			],
			[
				'name'    => 'Contributor',
				'created_at' => $date_now,
				'updated_at' => $date_now
			],
			[
				'name'    => 'User',
				'created_at' => $date_now,
				'updated_at' => $date_now
			]
		];

		DB::table('groups')->insert($data);


		$data = [
			[
				'email'    => 'james@acw.uk.com',
				'password' => Hash::make('password'),
				'activated' => 1,
				'activated_at' => $date_now,
				'first_name' => 'Jamesy',
				'last_name' => 'Admin',
				'username' => 'Jamesy',
				'created_at' => $date_now,
				'updated_at' => $date_now
			]
		];

		DB::table('users')->insert($data);


		$data = [
			[
				'user_id'  => 1,
				'group_id' => 1
			]
		];

		DB::table('users_groups')->insert($data);


		$data = [
			[
				'name'    => 'Uncategorised',
				'created_at' => $date_now,
				'updated_at' => $date_now
			]
		];

		DB::table('cms_categories')->insert($data);


		$data = [
			[
				'locale'    => 'FR',
				'created_at' => $date_now,
				'updated_at' => $date_now
			],
			[
				'locale'    => 'ES',
				'created_at' => $date_now,
				'updated_at' => $date_now
			]
		];

		DB::table('cms_locales')->insert($data);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(){
		//
	}

}
