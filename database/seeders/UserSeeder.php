<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
    //    $role_id = 'd058fd31-fd15-411d-bb33-c38040f173b8';
    //    $roleExists = DB::table('roles')->where('id', $role_id)->exists();

    //     if (!$roleExists) {
    //         DB::table('roles')->insert([
    //             [
    //                 'id' => $role_id,
    //                 'name' => 'organizer',
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ],
    //         ]);
    //     }

    //     DB::table('users')->insert([
    //         [
    //             'id' => Str::uuid(),
    //             'firstname' => 'John',
    //             'lastname' => 'DOE',
    //             'email' => 'john@gmail.com',
    //             'password' => Hash::make('12345678'),
    //             'role_id' => '$role_id',
    //             'organizer_name' => 'John Doe',
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ],
    //     ]);
        
    }
}
