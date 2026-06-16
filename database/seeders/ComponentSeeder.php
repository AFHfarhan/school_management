<?php

namespace Database\Seeders;

use App\Models\Component;
use Illuminate\Database\Seeder;

class ComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $components = [
            [
                'code' => 'school_profile',
                'structure' => 'school',
                'name' => 'School Profile',
                'description' => 'Default school profile configuration.',
                'category' => Component::CATEGORY_SCHOOL,
                'data' => [
                    'school_name' => 'School Name',
                    'school_tagline' => 'Quality Education',
                    'address' => 'Main Street',
                    'phone' => '0000000000',
                    'email' => 'school@example.com',
                ],
            ],
            [
                'code' => 'school_branding',
                'structure' => 'branding',
                'name' => 'School Branding',
                'description' => 'Default branding and identity settings.',
                'category' => Component::CATEGORY_BRANDING,
                'data' => [
                    'logo' => null,
                    'primary_color' => '#2563eb',
                    'secondary_color' => '#1f2937',
                    'favicon' => null,
                ],
            ],
            [
                'code' => 'academic_setting',
                'structure' => 'academic',
                'name' => 'Academic Setting',
                'description' => 'Default academic and curriculum configuration.',
                'category' => Component::CATEGORY_ACADEMIC,
                'data' => [
                    'academic_year' => '2026/2027',
                    'semester' => 'Odd Semester',
                    'grading_system' => 'standard',
                    'attendance_threshold' => 75,
                ],
            ],
            [
                'code' => 'registration_setting',
                'structure' => 'registration',
                'name' => 'Registration Setting',
                'description' => 'Default student registration settings.',
                'category' => Component::CATEGORY_REGISTRATION,
                'data' => [
                    'open_registration' => true,
                    'registration_fee' => 0,
                    'required_documents' => ['ID Card', 'Birth Certificate'],
                    'approval_required' => false,
                ],
            ],
            [
                'code' => 'payment_setting',
                'structure' => 'payment',
                'name' => 'Payment Setting',
                'description' => 'Default payment and fee configuration.',
                'category' => Component::CATEGORY_PAYMENT,
                'data' => [
                    'currency' => 'IDR',
                    'payment_gateway' => 'manual',
                    'late_fee' => 0,
                    'payment_due_days' => 7,
                ],
            ],
            [
                'code' => 'attendance_setting',
                'structure' => 'attendance',
                'name' => 'Attendance Setting',
                'description' => 'Default attendance tracking configuration.',
                'category' => Component::CATEGORY_ATTENDANCE,
                'data' => [
                    'enable_face_recognition' => false,
                    'mark_late_after_minutes' => 15,
                    'allow_self_check_in' => true,
                    'attendance_mode' => 'manual',
                ],
            ],
        ];

        foreach ($components as $component) {
            Component::updateOrCreate(
                ['code' => $component['code']],
                [
                    'structure' => $component['structure'],
                    'name' => $component['name'],
                    'description' => $component['description'],
                    'category' => $component['category'],
                    'data' => $component['data'],
                ]
            );
        }
    }
}
