<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Level : SUper Administrator
        DB::table('users')->insert([
            'firstname' => 'Admin IGN',
            'email' => 'admin@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('admin'),
            'country' => 100,
            'user_type' => 1,
            'created_at'  => now(),
            'updated_at'  => now()
        ]);
    }
}
