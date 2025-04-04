<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        DB::table('roles')->insert([
            'name' => 'Admin',
            'slug' => 'admin',
            'created_at'    => date("Y-m-d H:i:s")
        ]);        

        DB::table('roles')->insert([
            'name' => 'Manager',
            'slug' => 'manager',
            'created_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('roles')->insert([
            'name' => 'User',
            'slug' => 'user',
            'created_at' => date("Y-m-d H:i:s")
        ]);
    }
}
