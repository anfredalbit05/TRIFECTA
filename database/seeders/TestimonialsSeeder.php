<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestimonialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('testimonials')->insert([

            [
                'fullname' => 'Maria Santos',
                'designation' => 'Member',
                'testimony' => 'Joining DAYONGAN gave me peace of mind knowing my family has financial protection during unexpected situations. The staff were very accommodating.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Juan Dela Cruz',
                'designation' => 'Member',
                'testimony' => 'The registration process was fast and easy. I appreciate the transparency and the affordable membership plan.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Ana Rodriguez',
                'designation' => 'Beneficiary',
                'testimony' => 'Our family received assistance during a difficult time. DAYONGAN truly lives by its mission of helping families.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Pedro Ramirez',
                'designation' => 'Member',
                'testimony' => 'The benefits are affordable yet comprehensive. I highly recommend DAYONGAN to anyone looking for family protection.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Jennifer Lopez',
                'designation' => 'Teacher',
                'testimony' => 'Excellent customer service and very responsive staff. Every concern was handled professionally.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Michael Reyes',
                'designation' => 'Business Owner',
                'testimony' => 'DAYONGAN has become an important part of our family planning. The membership provides confidence and security.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Grace Villanueva',
                'designation' => 'Member',
                'testimony' => 'The organization truly values its members. I appreciate the continuous updates and community activities.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Carlo Bautista',
                'designation' => 'Engineer',
                'testimony' => 'Affordable annual membership with benefits that exceed expectations. Highly recommended.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Liza Fernandez',
                'designation' => 'Member',
                'testimony' => 'The claims process was smooth and the staff guided us every step of the way.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Richard Gomez',
                'designation' => 'Government Employee',
                'testimony' => 'I admire DAYONGAN for promoting financial literacy while providing valuable community support.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Christine Ramos',
                'designation' => 'Member',
                'testimony' => 'My family feels more secure because of DAYONGAN. Thank you for your dedication and service.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Edgar Morales',
                'designation' => 'Farmer',
                'testimony' => 'Even in rural communities, DAYONGAN reaches out and provides genuine assistance to members.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Patricia Cruz',
                'designation' => 'Nurse',
                'testimony' => 'The organization demonstrates compassion and professionalism in serving every member.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Dennis Flores',
                'designation' => 'Member',
                'testimony' => 'DAYONGAN has become a trusted partner in protecting our family. I encourage others to join.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Rose Mendoza',
                'designation' => 'Entrepreneur',
                'testimony' => 'The value of the membership is far greater than its cost. It is truly worth investing in.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Allan Garcia',
                'designation' => 'Member',
                'testimony' => 'I appreciate the organization\'s commitment to transparency and excellent service.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Michelle Aquino',
                'designation' => 'Teacher',
                'testimony' => 'The support extended to our family during difficult times was truly remarkable.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Ronald Perez',
                'designation' => 'Member',
                'testimony' => 'I joined because of a friend\'s recommendation and I have never regretted my decision.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Catherine Lim',
                'designation' => 'Accountant',
                'testimony' => 'The financial literacy seminars and community programs make DAYONGAN stand out from other organizations.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'fullname' => 'Joseph Navarro',
                'designation' => 'Member',
                'testimony' => 'Excellent benefits, approachable staff, and a strong commitment to helping Filipino families. Highly recommended.',
                'rate' => 5,
                'status' => 'Published',
                'UserID' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}