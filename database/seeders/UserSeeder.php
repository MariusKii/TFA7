<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['username' => 'andrea.mendoza', 'full_name' => 'Andrea Mendoza', 'role' => 'Administrator', 'status' => 'Active'],
            ['username' => 'daniel.ramos', 'full_name' => 'Daniel Ramos', 'role' => 'Manager', 'status' => 'Active'],
            ['username' => 'chloe.tan', 'full_name' => 'Chloe Tan', 'role' => 'Cashier', 'status' => 'Active'],
            ['username' => 'lucas.flores', 'full_name' => 'Lucas Flores', 'role' => 'Cashier', 'status' => 'Inactive'],
            ['username' => 'elena.torres', 'full_name' => 'Elena Torres', 'role' => 'Inventory Clerk', 'status' => 'Active'],
        ];

        foreach ($users as $user) {
            $email = $user['username'].'@example.com';

            if (User::where('username', $user['username'])->orWhere('email', $email)->exists()) {
                continue;
            }

            User::create([
                ...$user,
                'name' => $user['full_name'],
                'email' => $email,
                'password' => Str::random(64),
            ]);
        }
    }
}
