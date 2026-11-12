<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['full_name' => 'Isabel Santos', 'email' => 'isabel.santos@example.com', 'phone' => '0917 555 0101', 'status' => 'Active'],
            ['full_name' => 'Miguel Reyes', 'email' => 'miguel.reyes@example.com', 'phone' => '0918 555 0102', 'status' => 'Active'],
            ['full_name' => 'Sofia Cruz', 'email' => 'sofia.cruz@example.com', 'phone' => '0919 555 0103', 'status' => 'Inactive'],
            ['full_name' => 'Gabriel Lim', 'email' => 'gabriel.lim@example.com', 'phone' => '0920 555 0104', 'status' => 'Active'],
            ['full_name' => 'Amelia Garcia', 'email' => 'amelia.garcia@example.com', 'phone' => '0921 555 0105', 'status' => 'Active'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['email' => $customer['email']], $customer);
        }
    }
}
