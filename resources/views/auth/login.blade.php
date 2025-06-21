@extends('layouts.app')

@section('content')
<div class="auth-container"> {{-- auth-container class from layouts.app for consistent styling --}}
    <h2 class="text-center">Login</h2>
    <form id="loginForm">
        <div class="form-group mb-3">
            <label for="email">Email address</label>
            <input type="email" class="form-control" id="email" placeholder="Enter email" required>
        </div>
        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div id="message" class="mt-3 text-center"></div>
    <div class="mt-3 text-center">
        <p>Don't have an account? <a href="/register">Register here</a></p> {{-- Updated href --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Redirect if already logged in
    if (localStorage.getItem('authToken')) {
        window.location.href = '/dashboard'; // Updated href
    }

    $("#loginForm").submit(function(event) {
        event.preventDefault();
        $("#message").text('').css('color', 'black'); // Clear previous messages

        $.ajax({
            url: '/api/login', // This remains the same as it's an API call
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                email: $("#email").val(),
                password: $("#password").val()
            }),
            success: function(response) {
                if (response.access_token) {
                    localStorage.setItem('authToken', response.access_token);
                    window.location.href = '/dashboard'; // Updated href
                } else {
                     $("#message").text('Login successful, but no token received.').css('color', 'orange');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred during login.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    errorMessage = xhr.statusText;
                }
                $("#message").text(errorMessage).css('color', 'red');
            }
        });
    });
});
</script>
@endpush
