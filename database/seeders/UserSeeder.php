<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('users')->insert([    
            [
                'first_name' => 'John Admin',
                'last_name' => 'Doe',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'verified',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'John Staff',
                'last_name' => 'Doe',
                'email' => 'staff@gmail.com',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'status' => 'verified',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'John Patron',
                'last_name' => 'Doe',
                'email' => 'patron@gmail.com',
                'password' => Hash::make('patron123'),
                'role' => 'patron',
                'status' => 'verified',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
}
}