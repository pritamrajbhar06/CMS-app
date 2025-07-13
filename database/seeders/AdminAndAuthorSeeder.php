<?php

namespace Database\Seeders;

use App\Models\ApiUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminAndAuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('aaaaaaaa'),
            'user_type' => 'admin'
        ]);

        User::create([
            'name' => 'Author',
            'email' => 'author@example.com',
            'password' => bcrypt('aaaaaaaa'),
            'user_type' => 'author'
        ]);

    }
}
