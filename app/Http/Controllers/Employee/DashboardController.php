<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('employee.dashboard'); // points to resources/views/employee/dashboard.blade.php
    }
}
