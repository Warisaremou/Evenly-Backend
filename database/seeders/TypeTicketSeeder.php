<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TypeTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('type_tickets')->insert([
            [
                'id' => Str::uuid(),
                'name' => 'Free',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Paid',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
