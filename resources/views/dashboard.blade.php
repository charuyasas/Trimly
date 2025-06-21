@extends('layouts.app')

@section('content')
<div class="dashboard-container"> {{-- dashboard-container class from layouts.app for consistent styling --}}
    <div id="userInfo">
        <h2>Welcome, <span id="userName">User</span>!</h2>
        <p>Your email is: <span id="userEmail"></span></p>
        <p>Your roles: <span id="userRoles"></span></p>
    </div>
    <div id="adminControls" style="display: none;">
        <h3>Admin Controls</h3>
        <p>User management features will be here.</p>
        {{-- Example: Button to navigate to a user list page --}}
        {{-- <a href="/admin/users" class="btn btn-info mt-2">Manage Users</a> --}}
    </div>
    <div id="message" class="mt-3"></div>

    {{-- The logout button is now part of the main layout's navbar, but we need to ensure --}}
    {{-- the script can target it or we add a specific one here if preferred. --}}
    {{-- For simplicity, let's assume the layout will eventually have a logout button with id "logoutButton" --}}
    {{-- or this script correctly handles a local one if the layout one is not for this specific page. --}}
    {{-- The current app.blade.php has a placeholder comment for logout, not an actual button. --}}
    {{-- So, we need a logout button here, or the script won't find #logoutButton from the layout. --}}
    {{-- Re-adding a simple logout button here for the script to work, until layout navbar is fully dynamic. --}}
    <button type="button" class="btn btn-danger mt-3" id="logoutButtonInDashboard">Logout</button>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const authToken = localStorage.getItem('authToken');

    if (!authToken) {
        window.location.href = '/login'; // Updated href
        return;
    }

    // Fetch user data
    $.ajax({
        url: '/api/user', // This remains the same
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + authToken,
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response && response.name) {
                $("#userName").text(response.name);
                $("#userEmail").text(response.email);

                let rolesStr = 'No roles assigned';
                if (response.roles && response.roles.length > 0) {
                    rolesStr = response.roles.map(role => role.name).join(', ');
                    if (response.roles.some(role => role.name === 'admin')) {
                        $('#adminControls').show();
                    }
                }
                $("#userRoles").text(rolesStr);

            } else {
                $("#message").text('Could not retrieve user information.').css('color', 'red');
            }
        },
        error: function(xhr) {
            $("#message").text('Error fetching user data. You might be logged out.').css('color', 'red');
            // Consider redirecting or attempting token refresh here
            // localStorage.removeItem('authToken');
            // window.location.href = '/login';
        }
    });

    // Logout functionality - targets button within this dashboard page
    $("#logoutButtonInDashboard").click(function() {
        $.ajax({
            url: '/api/logout', // This remains the same
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + authToken,
                'Accept': 'application/json'
            },
            success: function(response) {
                localStorage.removeItem('authToken');
                window.location.href = '/login'; // Updated href
            },
            error: function(xhr) {
                $("#message").text('Logout failed. Please try again.').css('color', 'red');
                localStorage.removeItem('authToken'); // Fallback
                window.location.href = '/login'; // Updated href
            }
        });
    });
});
</script>
@endpush
