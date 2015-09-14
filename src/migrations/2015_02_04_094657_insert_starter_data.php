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
        $date_now = Carbon\Carbon::now();

        $data = [
            [
                'name'    => 'Administrator',
                'created_at' => $date_now,
                'updated_at' => $date_now
            ],
            [
                'name'    => 'Editor',
                'created_at' => $date_now,
                'updated_at' => $date_now
            ],
            [
                'name'    => 'User',
                'created_at' => $date_now,
                'updated_at' => $date_now
            ]
        ];

        DB::table('roles')->insert($data);

        $data = [
            [
                'email'    => 'james@acw.uk.com',
                'password' => bcrypt('password'),
                'first_name' => 'James',
                'last_name' => 'kJamesy',
                'username' => 'Jamesy',
                'active' => 1,
                'created_at' => $date_now,
                'updated_at' => $date_now
            ]
        ];

        DB::table('users')->insert($data);

        $data = [
            [
                'user_id'  => 1,
                'role_id' => 1
            ]
        ];

        DB::table('role_user')->insert($data);

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
