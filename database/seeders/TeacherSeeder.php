<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Teacher::create([
            'name' => 'Admin Teacher',
            'email' => 'admin@school.com',
            'password' => Hash::make('password123'),
            'data' => json_encode(['role' => 'admin']),
        ]);
    }
}
