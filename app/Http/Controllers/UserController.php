<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = [
            ['id' => 1, 'name' => 'Andrea Mendoza', 'email' => 'andrea.mendoza@example.com', 'role' => 'Administrator', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Daniel Ramos', 'email' => 'daniel.ramos@example.com', 'role' => 'Manager', 'status' => 'Active'],
            ['id' => 3, 'name' => 'Chloe Tan', 'email' => 'chloe.tan@example.com', 'role' => 'Cashier', 'status' => 'Active'],
            ['id' => 4, 'name' => 'Lucas Flores', 'email' => 'lucas.flores@example.com', 'role' => 'Cashier', 'status' => 'Inactive'],
            ['id' => 5, 'name' => 'Elena Torres', 'email' => 'elena.torres@example.com', 'role' => 'Inventory Clerk', 'status' => 'Active'],
        ];

        return view('users', compact('users'));
    }
}
