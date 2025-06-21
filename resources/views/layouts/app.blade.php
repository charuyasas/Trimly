<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap1.min.css') }}" rel="stylesheet">
    <!-- Custom Styles (if any in the future) -->
    <!-- <link href="{{ asset('css/style.css') }}" rel="stylesheet"> -->

    <style>
        body { padding-top: 60px; /* Account for fixed navbar */ }
        .auth-container { max-width: 500px; margin: auto; }
        .dashboard-container { margin-top: 20px; }
        #message { margin-top: 15px; }
        .navbar { z-index: 1030; /* Ensure navbar is above other content */}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">{{ config('app.name', 'Laravel') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Navigation links will be dynamically shown based on auth status in actual views -->
                    <!-- Example:
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/register') }}">Register</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="logoutButton">Logout</a>
                        </li>
                    @endguest
                    -->
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery1-3.4.1.min.js') }}"></script>
    <!-- Bootstrap JS (Optional, if Bootstrap components needing JS are used) -->
    <script src="{{ asset('assets/js/bootstrap1.min.js') }}"></script>

    @stack('scripts') <!-- For page-specific scripts -->
</body>
</html>
