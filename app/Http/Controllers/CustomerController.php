<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = [
            ['id' => 1001, 'name' => 'Isabel Santos', 'email' => 'isabel.santos@example.com', 'phone' => '0917 555 0101', 'status' => 'Active'],
            ['id' => 1002, 'name' => 'Miguel Reyes', 'email' => 'miguel.reyes@example.com', 'phone' => '0918 555 0102', 'status' => 'Active'],
            ['id' => 1003, 'name' => 'Sofia Cruz', 'email' => 'sofia.cruz@example.com', 'phone' => '0919 555 0103', 'status' => 'Inactive'],
            ['id' => 1004, 'name' => 'Gabriel Lim', 'email' => 'gabriel.lim@example.com', 'phone' => '0920 555 0104', 'status' => 'Active'],
            ['id' => 1005, 'name' => 'Amelia Garcia', 'email' => 'amelia.garcia@example.com', 'phone' => '0921 555 0105', 'status' => 'Active'],
        ];

        return view('customers', compact('customers'));
    }
}
