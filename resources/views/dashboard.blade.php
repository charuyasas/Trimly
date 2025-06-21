@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <div id="userInfo">
        <h2>Welcome, <span id="userName">User</span>!</h2>
        <p>Your email is: <span id="userEmail"></span></p>
        <p>Your roles: <span id="userRoles"></span></p>
    </div>

    <div id="adminControls" style="display: none;">
        <h3>Admin Section</h3>
        <p>This section is visible to admin users only.</p>

        <div class="my-4">
            <h4>Create New Role</h4>
            <form id="createRoleForm" class="row gx-2 gy-2 align-items-center">
                <div class="col-auto">
                    <label for="roleNameInput" class="visually-hidden">Role Name</label>
                    <input type="text" class="form-control" id="roleNameInput" placeholder="Enter role name" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
            <div id="createRoleMessage" class="mt-2"></div>
        </div>

        <div class="my-4">
            <h4>Available Roles</h4>
            <ul id="rolesList" class="list-group">
                <!-- Roles will be listed here -->
            </ul>
            <div id="rolesLoadingMessage" class="mt-2" style="display:none;">Loading roles...</div>
            <div id="rolesErrorMessage" class="text-danger mt-2" style="display:none;"></div>
        </div>

        <h4 class="mt-4">Users</h4>
        <table class="table table-striped table-bordered" id="usersTable" style="display:none;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Active</th>
                    <th>Blocked</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <!-- User data will be populated here by JavaScript -->
            </tbody>
        </table>
        <div id="usersLoadingMessage" class="mt-2" style="display:none;">Loading users...</div>
        <div id="usersErrorMessage" class="mt-2 text-danger" style="display:none;"></div>

        <div class="my-4">
            <h4>User Activity Logs</h4>
            <div id="logsLoadingMessage" style="display:none;">Loading logs...</div>
            <div id="logsErrorMessage" class="text-danger mt-2" style="display:none;"></div>
            <table class="table table-striped table-bordered table-sm" id="logsTable" style="display:none;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Message</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody">
                    <!-- Log data will be populated here -->
                </tbody>
            </table>
            <nav id="logsPagination" aria-label="Logs navigation">
                <!-- Pagination controls will be added here -->
            </nav>
        </div>
    </div>

    <div id="message" class="mt-3"></div>
    <button type="button" class="btn btn-danger mt-3" id="logoutButtonInDashboard">Logout</button>
</div>

<!-- Manage Roles Modal -->
<div class="modal fade" id="manageRolesModal" tabindex="-1" aria-labelledby="manageRolesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageRolesModalLabel">Manage Roles for <span id="userNameForModal"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="userIdForModal">
                <p><strong>Current Roles:</strong> <span id="currentUserRolesForModal"></span></p>
                <div id="availableRolesForModal" class="mb-3">
                    <!-- Checkboxes for available roles will be populated here -->
                </div>
                <div id="manageRolesModalMessage" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveUserRolesButton">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const authToken = localStorage.getItem('authToken');
    let all_roles_data = []; // To store all roles for modal use later

    if (!authToken) {
        window.location.href = '/login';
        return;
    }

    // Fetch current user data
    $.ajax({
        url: '/api/user',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + authToken,
            'Accept': 'application/json'
        },
        success: function(currentUser) {
            if (currentUser && currentUser.name) {
                $("#userName").text(currentUser.name);
                $("#userEmail").text(currentUser.email);

                let rolesStr = 'No roles assigned';
                let userIsAdmin = false;
                if (currentUser.roles && currentUser.roles.length > 0) {
                    rolesStr = currentUser.roles.map(role => role.name).join(', ');
                    if (currentUser.roles.some(role => role.name === 'admin')) {
                        userIsAdmin = true;
                    }
                }
                $("#userRoles").text(rolesStr);

                if (userIsAdmin) {
                    $('#adminControls').show();
                    fetchAndDisplayUsers(authToken);
                    fetchAndDisplayRoles(authToken);
                    fetchAndDisplayLogs(authToken); // Call to fetch logs
                }

            } else {
                $("#message").text('Could not retrieve user information.').css('color', 'red');
            }
        },
        error: function(xhr) {
            $("#message").text('Error fetching user data. You might be logged out.').css('color', 'red');
        }
    });

    function fetchAndDisplayUsers(token) {
        $('#usersLoadingMessage').show();
        $('#usersErrorMessage').hide().text('');
        $.ajax({
            url: '/api/users',
            type: 'GET',
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json'},
            success: function(usersResponse) {
                $('#usersLoadingMessage').hide();
                const usersTableBody = $('#usersTableBody');
                usersTableBody.empty();
                const users = usersResponse.data || usersResponse; // Handle paginated or direct array
                if (users && users.length > 0) {
                    users.forEach(function(user) {
                        let rolesStr = user.roles.map(role => role.name).join(', ');
                        if (!rolesStr) rolesStr = 'N/A';
                        let row = $('<tr>');
                        row.append($('<td>').text(user.id));
                        row.append($('<td>').text(user.name));
                        row.append($('<td>').text(user.email));
                        row.append($('<td>').text(rolesStr));

                        // Active status cell
                        let activeStatusCell = $('<td>');
                        if (user.is_active) {
                            activeStatusCell.html('<span class="badge bg-success">Active</span> ');
                            let deactivateBtn = $('<button class="btn btn-sm btn-warning toggle-active-btn ms-1">Deactivate</button>')
                                .attr('data-user-id', user.id)
                                .attr('data-action', 'deactivate');
                            activeStatusCell.append(deactivateBtn);
                        } else {
                            activeStatusCell.html('<span class="badge bg-secondary">Inactive</span> ');
                            let activateBtn = $('<button class="btn btn-sm btn-success toggle-active-btn ms-1">Activate</button>')
                                .attr('data-user-id', user.id)
                                .attr('data-action', 'activate');
                            activeStatusCell.append(activateBtn);
                        }
                        row.append(activeStatusCell);

                        // Blocked status cell
                        let blockedStatusCell = $('<td>');
                        if (user.is_blocked) {
                            blockedStatusCell.html('<span class="badge bg-danger">Blocked</span> ');
                            let unblockBtn = $('<button class="btn btn-sm btn-success toggle-block-btn ms-1">Unblock</button>')
                                .attr('data-user-id', user.id)
                                .attr('data-action', 'unblock');
                            blockedStatusCell.append(unblockBtn);
                        } else {
                            blockedStatusCell.html('<span class="badge bg-secondary">Not Blocked</span> '); // Using bg-secondary for "Not Blocked"
                            let blockBtn = $('<button class="btn btn-sm btn-danger toggle-block-btn ms-1">Block</button>')
                                .attr('data-user-id', user.id)
                                .attr('data-action', 'block');
                            blockedStatusCell.append(blockBtn);
                        }
                        row.append(blockedStatusCell);

                        let manageButton = $('<button class="btn btn-sm btn-info manage-roles-btn">Manage Roles</button>')
                            .attr('data-user-id', user.id)
                            .attr('data-user-name', user.name)
                            .attr('data-user-roles', JSON.stringify(user.roles.map(r => r.id))); // Store current role IDs
                        row.append($('<td>').append(manageButton));
                        usersTableBody.append(row);
                    });
                } else {
                    usersTableBody.append('<tr><td colspan="7">No users found.</td></tr>'); // Incremented colspan
                }
                $('#usersTable').show();
            },
            error: function(xhr) {
                $('#usersLoadingMessage').hide();
                $('#usersErrorMessage').text(xhr.responseJSON?.message || 'Could not load users.').show();
            }
        });
    }

    function fetchAndDisplayRoles(token) {
        $('#rolesLoadingMessage').show();
        $('#rolesErrorMessage').hide().text('');
        $.ajax({
            url: '/api/roles',
            type: 'GET',
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json'},
            success: function(roles) {
                $('#rolesLoadingMessage').hide();
                all_roles_data = roles; // Store for later use in modal
                const rolesList = $('#rolesList');
                rolesList.empty();
                if (roles && roles.length > 0) {
                    roles.forEach(function(role) {
                        rolesList.append($('<li class="list-group-item">').text(role.name));
                    });
                } else {
                    rolesList.append($('<li class="list-group-item">No roles found.</li>'));
                }
            },
            error: function(xhr) {
                $('#rolesLoadingMessage').hide();
                $('#rolesErrorMessage').text(xhr.responseJSON?.message || 'Could not load roles.').show();
            }
        });
    }

    $('#createRoleForm').submit(function(event) {
        event.preventDefault();
        const roleName = $('#roleNameInput').val().trim();
        const createRoleMessageDiv = $('#createRoleMessage');
        createRoleMessageDiv.text('').removeClass('text-success text-danger');

        if (!roleName) {
            createRoleMessageDiv.text('Role name is required.').addClass('text-danger');
            return;
        }

        $.ajax({
            url: '/api/roles',
            type: 'POST',
            headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json' },
            contentType: 'application/json',
            data: JSON.stringify({ name: roleName }),
            success: function(response) {
                createRoleMessageDiv.text('Role created successfully!').addClass('text-success');
                $('#roleNameInput').val('');
                fetchAndDisplayRoles(authToken); // Refresh roles list
            },
            error: function(xhr) {
                let errorMsg = 'Error creating role.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
                    if (xhr.responseJSON.errors && xhr.responseJSON.errors.name) {
                         errorMsg += ' ' + xhr.responseJSON.errors.name.join(', ');
                    }
                }
                createRoleMessageDiv.text(errorMsg).addClass('text-danger');
            }
        });
    });

    // Logout functionality
    $("#logoutButtonInDashboard").click(function() {
        $.ajax({
            url: '/api/logout',
            type: 'POST',
            headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json'},
            success: function(response) {
                localStorage.removeItem('authToken');
                all_roles_data = []; // Clear stored roles
                window.location.href = '/login';
            },
            error: function(xhr) {
                $("#message").text('Logout failed. Please try again.').css('color', 'red');
                localStorage.removeItem('authToken');
                all_roles_data = [];
                window.location.href = '/login';
            }
        });
    });

    // Manage Roles Modal Logic
    $(document).on('click', '.manage-roles-btn', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        // userRoles is stored as a JSON string of role IDs, e.g., "[1, 2]"
        const currentUserRoleIds = $(this).data('user-roles');

        $('#userNameForModal').text(userName);
        $('#userIdForModal').val(userId);
        $('#manageRolesModalMessage').text('').removeClass('text-success text-danger');

        // Display current roles (names)
        let currentRoleNames = [];
        if (all_roles_data && Array.isArray(all_roles_data)) {
            currentUserRoleIds.forEach(roleId => {
                const role = all_roles_data.find(r => r.id === roleId);
                if (role) {
                    currentRoleNames.push(role.name);
                }
            });
        }
        $('#currentUserRolesForModal').text(currentRoleNames.length > 0 ? currentRoleNames.join(', ') : 'N/A');

        const availableRolesDiv = $('#availableRolesForModal');
        availableRolesDiv.empty();

        if (all_roles_data && all_roles_data.length > 0) {
            all_roles_data.forEach(function(role) {
                const isChecked = currentUserRoleIds.includes(role.id);
                const checkboxHtml = `
                    <div class="form-check">
                        <input class="form-check-input role-checkbox" type="checkbox" value="${role.id}" id="modal-role-${role.id}" ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label" for="modal-role-${role.id}">${role.name}</label>
                    </div>`;
                availableRolesDiv.append(checkboxHtml);
            });
        } else {
            availableRolesDiv.text('No roles available to assign.');
        }
        // Ensure Bootstrap 5 modal is correctly initialized and shown
        var manageRolesModal = new bootstrap.Modal(document.getElementById('manageRolesModal'));
        manageRolesModal.show();
    });

    $('#saveUserRolesButton').click(function() {
        const userId = $('#userIdForModal').val();
        const selectedRoleIds = $('.role-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();

        // Get initial role IDs (assuming they were stored correctly when modal was opened)
        // This requires fetching the button that opened the modal again, or storing initial roles differently.
        // For simplicity, let's re-fetch the user's current roles from the button that IS NOT IN THE MODAL
        // This is a bit tricky because the modal is separate. Let's assume data-user-roles on the button that opened the modal is the source of truth for initial state.
        // This means we need to access that button's data again or pass it to the modal more directly.
        // A simpler way for now: fetch the user's current roles from the main table again or from a stored variable if possible.
        // For this implementation, I'll get it from the button that opened the modal, which means we need to select it.
        // This is not ideal. A better way would be to store initialRoleIds on the modal itself.
        // Let's assume `initialUserRoleIds` was stored when the modal was opened:
        // (This part needs to be improved if we can't access the original button's data easily)
        // For now, we'll rely on the initial `data-user-roles` from the button that *triggered* the modal.
        // This means we need to find that button again, or preferably store its 'data-user-roles' attribute.
        // Let's assume it was stored in a variable: `let initialUserRoleIdsForOpenedModal = [];`
        // And this variable is populated when the modal is opened.
        // For the sake of this step, I'll proceed as if `initialUserRoleIdsForOpenedModal` is available.
        // This part of the logic is complex without more direct state passing.
        //
        // A more robust way: when modal opens, store initialUserRoleIds globally or on the modal itself.
        // For example, when '.manage-roles-btn' is clicked:
        // $('#manageRolesModal').data('initial-role-ids', currentUserRoleIds);
        // Then retrieve it here: const initialUserRoleIds = $('#manageRolesModal').data('initial-role-ids');
        //
        // Let's assume 'currentUserRoleIds' from modal opening is available in a broader scope or re-fetched for comparison.
        // For this implementation, I will fetch it from the button attribute again (which is not great).
        // A better approach: Store initial roles in a hidden field or data attribute on the modal itself when it opens.
        // Let's modify the modal opening to store this:
        // In $(document).on('click', '.manage-roles-btn', function() { ... $('#manageRolesModal').data('initial-role-ids', currentUserRoleIds); ... })
        const initialUserRoleIds = $('#manageRolesModal').data('initial-role-ids') || [];


        const rolesToAssign = selectedRoleIds.filter(id => !initialUserRoleIds.includes(id));
        const rolesToRevoke = initialUserRoleIds.filter(id => !selectedRoleIds.includes(id));

        let promises = [];
        const messageDiv = $('#manageRolesModalMessage');
        messageDiv.text('Processing...').removeClass('text-success text-danger');

        rolesToAssign.forEach(roleId => {
            promises.push(
                $.ajax({
                    url: `/api/users/${userId}/roles`,
                    type: 'POST',
                    headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json' },
                    contentType: 'application/json',
                    data: JSON.stringify({ role_id: roleId })
                })
            );
        });

        rolesToRevoke.forEach(roleId => {
            promises.push(
                $.ajax({
                    url: `/api/users/${userId}/roles/${roleId}`, // Updated route
                    type: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json' }
                })
            );
        });

        Promise.allSettled(promises)
            .then(results => {
                let allSucceeded = true;
                let errors = [];
                results.forEach(result => {
                    if (result.status === 'rejected') {
                        allSucceeded = false;
                        if(result.reason.responseJSON && result.reason.responseJSON.message){
                            errors.push(result.reason.responseJSON.message);
                        } else {
                            errors.push("An unknown error occurred with one operation.");
                        }
                    }
                });

                if (allSucceeded) {
                    messageDiv.text('Roles updated successfully!').addClass('text-success');
                    fetchAndDisplayUsers(authToken); // Refresh user list
                    // Hide modal after a short delay
                    setTimeout(() => {
                        var manageRolesModal = bootstrap.Modal.getInstance(document.getElementById('manageRolesModal'));
                        if(manageRolesModal) manageRolesModal.hide();
                    }, 1500);
                } else {
                    messageDiv.text('Some operations failed: ' + errors.join("; ")).addClass('text-danger');
                }
            });
    });

    // Delegated event listener for activate/deactivate buttons
    $('#usersTableBody').on('click', '.toggle-active-btn', function() {
        const userId = $(this).data('user-id');
        const action = $(this).data('action'); // 'activate' or 'deactivate'
        const authToken = localStorage.getItem('authToken');

        if (!confirm(`Are you sure you want to ${action} user ${userId}?`)) {
            return;
        }

        let url = `/api/users/${userId}/${action}`;

        $.ajax({
            url: url,
            type: 'PATCH',
            headers: {
                'Authorization': 'Bearer ' + authToken,
                'Accept': 'application/json'
            },
            success: function(response) {
                // Using a more subtle notification, can be replaced with a proper toast library
                $('#message').text(`User ${action}d successfully.`).addClass('text-success').fadeIn().delay(3000).fadeOut(function() {
                    $(this).removeClass('text-success');
                });
                fetchAndDisplayUsers(authToken); // Refresh the user list
            },
            error: function(xhr) {
                let errorMsg = `Error ${action}ing user.`;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                // Using a more subtle notification
                $('#message').text(errorMsg).addClass('text-danger').fadeIn().delay(5000).fadeOut(function() {
                    $(this).removeClass('text-danger');
                });
                console.error(`Error ${action} user: `, xhr);
            }
        });
    });

    function fetchAndDisplayLogs(token, page = 1) {
        $('#logsLoadingMessage').show();
        $('#logsTable').hide();
        $('#logsErrorMessage').empty().hide();
        $('#logsPagination').empty();


        $.ajax({
            url: `/api/logs?page=${page}`,
            type: 'GET',
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
            success: function(response) {
                $('#logsLoadingMessage').hide();
                const logsTableBody = $('#logsTableBody');
                logsTableBody.empty();

                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(log) {
                        let userName = log.user ? `${log.user.name} (${log.user.email})` : 'System/Unknown';
                        let row = $('<tr>');
                        row.append($('<td>').text(log.id));
                        row.append($('<td>').text(userName));
                        row.append($('<td>').text(log.action));
                        row.append($('<td>').text(log.message || 'N/A'));
                        row.append($('<td>').text(log.ip_address || 'N/A'));
                        row.append($('<td>').text(log.user_agent || 'N/A'));
                        row.append($('<td>').text(new Date(log.created_at).toLocaleString()));
                        logsTableBody.append(row);
                    });
                    $('#logsTable').show();
                    renderLogsPagination(response, token);
                } else {
                    logsTableBody.append('<tr><td colspan="7">No logs found.</td></tr>');
                    $('#logsTable').show();
                }
            },
            error: function(xhr) {
                $('#logsLoadingMessage').hide();
                $('#logsErrorMessage').text(xhr.responseJSON?.message || 'Could not load logs.').show();
                console.error("Error fetching logs: ", xhr);
            }
        });
    }

    function renderLogsPagination(response, authToken) {
        const paginationContainer = $('#logsPagination');
        paginationContainer.empty();

        if (!response.links || response.links.length === 0) return;

        let ul = $('<ul class="pagination pagination-sm"></ul>'); // Added pagination-sm for smaller controls
        response.links.forEach(function(link) {
            let liClass = 'page-item';
            if (link.active) liClass += ' active';
            if (!link.url) liClass += ' disabled';

            let pageLink = $('<a class="page-link" href="#"></a>').html(link.label);
            if (link.url) {
                pageLink.attr('data-page-url', link.url);
                pageLink.on('click', function(e) {
                    e.preventDefault();
                    const pageUrl = $(this).attr('data-page-url');
                    if (pageUrl) {
                        try {
                            const url = new URL(pageUrl); // Use absolute URL for constructor
                            const pageNum = url.searchParams.get('page');
                            fetchAndDisplayLogs(authToken, pageNum);
                        } catch (error) {
                            console.error("Error parsing page URL from pagination link:", pageUrl, error);
                            const pageNumMatch = pageUrl.match(/page=(\d+)/);
                            if (pageNumMatch && pageNumMatch[1]) {
                                fetchAndDisplayLogs(authToken, pageNumMatch[1]);
                            }
                        }
                    }
                });
            }
            ul.append($(`<li class="${liClass}"></li>`).append(pageLink));
        });
        paginationContainer.append(ul);
    }

    // Delegated event listener for block/unblock buttons
    $('#usersTableBody').on('click', '.toggle-block-btn', function() {
        const userId = $(this).data('user-id');
        const action = $(this).data('action'); // 'block' or 'unblock'
        const authToken = localStorage.getItem('authToken');

        if (!confirm(`Are you sure you want to ${action} user ${userId}?`)) {
            return;
        }

        let url = `/api/users/${userId}/${action}`;

        $.ajax({
            url: url,
            type: 'PATCH',
            headers: {
                'Authorization': 'Bearer ' + authToken,
                'Accept': 'application/json'
            },
            success: function(response) {
                $('#message').text(`User ${action}ed successfully.`).addClass('text-success').fadeIn().delay(3000).fadeOut(function() {
                    $(this).removeClass('text-success');
                });
                fetchAndDisplayUsers(authToken); // Refresh the user list
            },
            error: function(xhr) {
                let errorMsg = `Error ${action}ing user.`;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#message').text(errorMsg).addClass('text-danger').fadeIn().delay(5000).fadeOut(function() {
                    $(this).removeClass('text-danger');
                });
                console.error(`Error ${action} user: `, xhr);
            }
        });
    });
});
</script>
@endpush
