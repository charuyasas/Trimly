@extends('layouts.app')

@section('content')
<div class="auth-container"> {{-- auth-container class from layouts.app for consistent styling --}}
    <h2 class="text-center">Register</h2>
    <form id="registerForm">
        <div class="form-group mb-3">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" placeholder="Enter your name" required>
        </div>
        <div class="form-group mb-3">
            <label for="email">Email address</label>
            <input type="email" class="form-control" id="email" placeholder="Enter email" required>
        </div>
        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Password" required>
        </div>
        <div class="form-group mb-3">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirmation" placeholder="Confirm password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
    <div id="message" class="mt-3 text-center"></div>
    <div class="mt-3 text-center">
        <p>Already have an account? <a href="/login">Login here</a></p> {{-- Updated href --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $("#registerForm").submit(function(event) {
        event.preventDefault();
        $("#message").text('').css('color', 'black');

        $.ajax({
            url: '/api/register', // This remains the same
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                name: $("#name").val(),
                email: $("#email").val(),
                password: $("#password").val(),
                password_confirmation: $("#password_confirmation").val()
            }),
            success: function(response) {
                $("#message").text(response.message || 'Registration successful! Please login.').css('color', 'green');
                $("#registerForm")[0].reset();
                // Optional: redirect to login after a delay
                // setTimeout(function() { window.location.href = '/login'; }, 3000); // Updated href
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred during registration.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        errorMessage += '<br/>';
                        for (const key in errors) {
                            errorMessage += errors[key].join('<br/>') + '<br/>';
                        }
                    }
                } else if (xhr.statusText) {
                    errorMessage = xhr.statusText;
                }
                $("#message").html(errorMessage).css('color', 'red');
            }
        });
    });
});
</script>
@endpush
