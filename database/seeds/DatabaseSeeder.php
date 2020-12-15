<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Users
        $this->call(UsersTableSeeder::class);

        // Countries
        $dumpSqlCountries = file_get_contents(database_path() . '/seeds/dumps/countries.sql');
        if ($dumpSqlCountries) {
            DB::statement($dumpSqlCountries);
        }
    }
}
