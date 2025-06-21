<?php

use Illuminate\Support\Facades\Route;

// Route for the root path, redirecting to login
Route::get('/', function () {
    return redirect('/login');
});

// Route to display the login page
Route::get('/login', function () {
    return view('auth.login');
})->name('login'); // Assigning a name for easier referencing if needed later

// Route to display the registration page
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Route to display the dashboard page
// This route will serve the initial HTML.
// Authentication and dynamic content loading within the dashboard
// will still be handled by JavaScript making API calls with a token.
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Note: No specific web authentication middleware (like 'auth') is applied here
// because the frontend is expected to handle auth via API tokens stored in localStorage.
// If session-based web auth were desired for these routes, middleware would be added.
