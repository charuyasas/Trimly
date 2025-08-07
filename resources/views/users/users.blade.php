@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'User'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">User List </h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_body">
                                <div class="QA_section">
                                    <div class="white_box_tittle list_header">
                                        <h4></h4>
                                        <div class="box_right d-flex lms_block">
                                            <div class="serach_field_2">
                                                <div class="search_inner">
                                                    <form Active="#">
                                                        <div class="search_field">
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="userTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" onclick="showModal()" data-bs-target="#userModal">
                                                    Add New
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active ">
                                            <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Role</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="userTable">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

            @include('includes.footer')

            <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userModalLongTitle">Add User</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="userForm" onsubmit="saveUser(); return false;">
                                <input type="hidden" id="user_id">
                                <div class="white_card_body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <select class="form-select" id="cbo_roles" ></select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" id="user_name" placeholder="Name">
                                                <input type="hidden" id="employee_id">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="email" id="email" placeholder="Email">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" id="username" placeholder="Username">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="password" id="password" placeholder="Password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="saveBtn">Save</button>
                        </div>
                    </div>
                </div>
            </div>

<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLongTitle">Change Password</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm" onsubmit="changePassword(); return false;">
                    <input type="hidden" id="passwordChangeUser_id">
                    <div class="white_card_body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="password" id="oldPassword" placeholder="Old Password">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="password" id="newPassword" placeholder="New Password">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="passwordUpdateBtn">Update</button>
            </div>
        </div>
    </div>
</div>

            <script>
                const apiUrl = '/api/users';
                loadUsers();

                function loadUsers() {
                    $.get(apiUrl, function(data) {
                        let table = $('.lms_table_active').DataTable();
                        table.clear();

                        let rowID = 1;
                        data.forEach(user => {
                            table.row.add([
                                rowID,
                                user.name,
                                user.roles[0]?.name,
                                user.email,
                                `
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="editUser('${user.id}')">Edit</button>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#passwordModal" onclick="changePasswordUser('${user.id}')">Change Password</button>
                                    `
                            ]);
                            rowID++;
                        });

                        table.draw();
                    });
                }

                $(function () {

                    $("#user_name").autocomplete({
                        source: function (request, response) {
                            if (request.term.length < 1) return;

                            $.ajax({
                                url: '/api/employees-list',
                                dataType: 'json',
                                data: {search_key: request.term},
                                success: function (data) {
                                    response(data);
                                    if (data.length === 1) {
                                        $("#user_name").val(data[0].label);
                                        $("#employee_id").val(data[0].value);
                                    }
                                }
                            });
                        },
                        minLength: 1,
                        appendTo: "#userModal",
                        focus: function (event, ui) {
                            $("#user_name").val(ui.item.label);
                            return false;
                        },
                        select: function (event, ui) {
                            $("#user_name").val(ui.item.label);
                            $("#employee_id").val(ui.item.value);
                            return false;
                        }
                    });

                });

                function loadRoles(id){
                    $.ajax({
                        url: '/api/role-list-dropdown',
                        method: 'GET',
                        success: function (data) {
                            var select = $('#cbo_roles');
                            select.empty();
                            select.append('<option value="" disabled selected>Select Role</option>');

                            data.forEach(function (item) {
                                select.append('<option value="' + item.value + '">' + item.label + '</option>');
                            });
                            if(id != ''){
                                $('#cbo_roles').val(id);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error loading dropdown:', error);
                        }
                    });

                }

                function saveUser() {
                    const user_id = $('#user_id').val();
                    const data = {
                        id: $('#user_id').val(),
                        role: $('#cbo_roles').val(),
                        name: $('#user_name').val(),
                        employee_id: $('#employee_id').val(),
                        username: $('#username').val(),
                        email: $('#email').val(),
                        password: $('#password').val()
                    };

                    const method = user_id ? 'PUT' : 'POST';
                    const url = user_id ? `${apiUrl}/${user_id}` : apiUrl;

                        $.ajax({
                            url: url,
                            method: method,
                            data: data,
                            success: function() {
                                Swal.fire({
                                    icon: "success",
                                    title: user_id ? "Updated Successfully" : "Saved Successfully",
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                loadUsers();
                                closeModal('userModal');
                                $('#user_id').val('');
                            },
                            error: function (xhr) {
                                if (xhr.status === 422) {
                                    const response = xhr.responseJSON;
                                    Swal.fire({
                                        icon: "error",
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Something went wrong",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                }
                            }
                        });
                    }

                function changePassword() {
                    const data = {
                        id: $('#passwordChangeUser_id').val(),
                        oldPassword: $('#oldPassword').val(),
                        newPassword: $('#newPassword').val()
                    };

                    $.ajax({
                        url: `api/update-password`,
                        method: 'POST',
                        data: data,
                        success: function() {
                            Swal.fire({
                                icon: "success",
                                title: "Updated Successfully",
                                showConfirmButton: false,
                                timer: 1500
                            });
                            loadUsers();
                            closeModal('passwordModal');
                            $('#passwordChangeUser_id').val('');
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                const response = xhr.responseJSON;
                                Swal.fire({
                                    icon: "error",
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong",
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        }
                    });
                }

                function closeModal(modalName) {
                    const modalElement = document.getElementById(modalName);
                    const modal = bootstrap.Modal.getInstance(modalElement);

                    if (modal) {
                        modal.hide();
                        $('#userForm')[0].reset();
                        $('#changePasswordForm')[0].reset();
                    }
                }

                function showModal() {
                    $('#password').show();
                    $('#userForm')[0].reset();
                    $('#user_id').val("");
                    $('#userModalLongTitle').text('Add User');
                    $('#saveBtn').text('Save');
                    loadRoles('');
                }

                function changePasswordUser(user_id) {
                    $('#passwordChangeUser_id').val(user_id);
                }

                function editUser(user_id) {

                    $('#password').hide();
                    $.get(`${apiUrl}/${user_id}`, function(user) {
                        $('#user_id').val(user.id);
                        loadRoles(user.roles[0]?.name);
                        $('#user_name').val(user.name);
                        $('#username').val(user.username);
                        $('#employee_id').val(user.employee_id);
                        $('#email').val(user.email);

                        $('#userModalLongTitle').text('Edit User');
                        $('#saveBtn').text('Update');
                    });
                }

                function deleteUser(user_id) {
                    if (confirm('Delete this user?')) {
                        $.ajax({
                            url: `${apiUrl}/${user_id}`,
                            method: 'DELETE',
                            success: function() {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted Successfully",
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                loadUsers();
                            }
                        });
                    }
                }

                $(document).on('keydown', 'input, select, textarea, button', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();

                        const focusables = $('input, select, textarea, button')
                            .filter(':visible:not([readonly]):not([disabled])');

                        const index = focusables.index(this);

                        if (index > -1 && index + 1 < focusables.length) {
                            const next = focusables.eq(index + 1);
                            next.focus();

                            if (next.is('button') && (next.text().trim() === 'Save' || next.text().trim() === 'Update')) {
                                next.click();
                            }
                        } else {
                            triggerFormAction($(this));
                        }
                    }
                });

                $(document).on('click', 'button', function () {
                    const buttonText = $(this).text().trim();
                    if (buttonText === 'Save' || buttonText === 'Update') {
                        triggerFormAction($(this));
                    }
                });

                function triggerFormAction($element) {
                    const $modal = $element.closest('.modal');

                    if ($modal.attr('id') === 'userModal') {
                        saveUser();
                    } else if ($modal.attr('id') === 'passwordModal') {
                        changePassword();
                    }
                }


            </script>
