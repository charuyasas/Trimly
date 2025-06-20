<?php

namespace App\Http\Controllers;

use App\Models\User; // Import User model
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get(); // Get 5 most recent users

        return view('index', [
            'totalUsers' => $totalUsers,
            'recentUsers' => $recentUsers,
        ]);
    }
}
