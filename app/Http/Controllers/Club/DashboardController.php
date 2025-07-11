<?php

namespace App\Http\Controllers\Club;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('clubs.dashboard.index');
    }
}
