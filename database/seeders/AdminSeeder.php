<?php

namespace Database\Seeders;
use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'firstname' => 'Bikman',
            'lastname' =>'Djuma',
            'gender' => 'male',
            'phone' => '0785389000',
            'email' => 'ntiruhungwb@gmail.com',
            'image' => 'user.png',
            'dob' => '1994-12-20',
            'password' => bcrypt('bugarama'),
        ]);
    }
}
