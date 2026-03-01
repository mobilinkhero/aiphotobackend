<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard overview.
     */
    public function index()
    {
        return view('dashboard');
    }
}
