<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // dashboard page
    public function dashboardPage()
    {
        return view('dashboard');
    }
}
