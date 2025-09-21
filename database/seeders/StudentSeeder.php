<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::create([
            'name' => 'John Doe',
            'age' => 16,
            'gender' => 'male',
            'contact' => json_encode([
                'phone' => '123-456-7890',
                'address' => '123 Main St, Springfield, USA',
            ]),
            'academic' => json_encode([
                'grade' => '10',
                'section' => 'A',
                'subjects' => ['Math', 'Science', 'History'],
            ]),
        ]);

        Student::create([
            'name' => 'Jane Smith',
            'age' => 15,
            'gender' => 'female',
            'contact' => json_encode([
                'phone' => '987-654-3210',
                'address' => '456 Elm St, Metropolis, USA',
            ]),
            'academic' => json_encode([
                'grade' => '9',
                'section' => 'B',
                'subjects' => ['English', 'Biology', 'Art'],
            ]),
        ]);

        Student::create([
            'name' => 'Alice Johnson',
            'age' => 17,
            'gender' => 'female',
            'contact' => json_encode([
                'phone' => '555-123-4567',
                'address' => '789 Oak St, Gotham, USA',
            ]),
            'academic' => json_encode([
                'grade' => '11',
                'section' => 'C',
                'subjects' => ['Physics', 'Chemistry', 'Computer Science'],
            ]),
        ]);
    }
}
