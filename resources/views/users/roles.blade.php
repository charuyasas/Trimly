@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Roles'])

<style>
    .scrollable-permission-box {
        max-height: 400px;
        overflow-y: auto;
        padding: 12px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background-color: #ffffff;
    }

    .permission-item:hover {
        background-color: #f1f3f5;
    }

    .permission-parent {
        margin-top: 14px;
        padding-top: 8px;
        border-top: 1px solid #e9ecef;
        font-weight: bold;
        background-color: #f8f9fa;
    }

    .permission-parent .form-check-label {
        font-weight: bold;
    }

    .permission-checkbox {
        margin-right: 10px;
    }

</style>

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Role List</h3>
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
                                                    <form action="#">
                                                        <div class="search_field">
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="roleTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal" onclick="openAddRoleModal()">
                                                    Add New
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Role Name</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="roleTable">
                                            <!-- Dynamic Rows -->
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

<!-- Add/Edit Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Role</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="roleForm">
                    <input type="hidden" id="role_id">
                    <div class="white_card_body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="role_name" placeholder="Role Name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveRoleBtn" onclick="saveRole()">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role Permission Modal -->
<div class="modal fade" id="rolePermissionModal" tabindex="-1" role="dialog" aria-labelledby="rolePermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Permissions</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="rolePermissionForm">
                    <input type="hidden" id="role_permission_id">
                    <div id="permissionCheckboxes" class="p-2">
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveRoleBtn" onclick="updateRolePermissions()">Update</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const roleApiUrl = '/api/roles';
    loadRoles();

    function loadRoles() {
        $.get(roleApiUrl, function(data) {
            let table = $('.lms_table_active').DataTable();
            table.clear();

            let i = 1;
            data.forEach(role => {
                table.row.add([
                    i++,
                    role.name,
                    `
                    <button class="btn btn-sm btn-primary" onclick="editRole('${role.id}')">Edit</button>
                    <button class="btn btn-sm btn-primary" onclick="editRolePermissions('${role.id}')">Permissions</button>
<!--                    <button class="btn btn-sm btn-danger" onclick="deleteRole('${role.id}')">Delete</button>-->
                    `
                ]);
            });

            table.draw();
        });
    }

    function saveRole() {
        const role_id = $('#role_id').val();
        const data = {
            id: role_id,
            name: $('#role_name').val(),
            guard_name: 'web'
        };

        const method = role_id ? 'PUT' : 'POST';
        const url = role_id ? `${roleApiUrl}/${role_id}` : roleApiUrl;

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function () {
                Swal.fire({
                    icon: "success",
                    title: role_id ? "Updated Successfully" : "Saved Successfully",
                    showConfirmButton: false,
                    timer: 1500
                });
                loadRoles();
                closeRoleModal();
            },
            error: function (xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    icon: "error",
                    title: response?.message || "Something went wrong",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }

    function editRole(id) {
        $.get(`${roleApiUrl}/${id}`, function(role) {
            $('#role_id').val(role.id);
            $('#role_name').val(role.name);
            $('.modal-title').text('Edit Role');
            $('#saveRoleBtn').text('Update');
            const modal = new bootstrap.Modal(document.getElementById('roleModal'));
            modal.show();
        });
    }

        function editRolePermissions(id) {
        $.get(`api/role-permission-list/${id}`, function(sidebar) {
            $('#role_permission_id').val(id);
            renderSidebarPermissions(sidebar, '#permissionCheckboxes');
            const modal = new bootstrap.Modal(document.getElementById('rolePermissionModal'));
            modal.show();
        });
    }

    function renderSidebarPermissions(links, containerSelector) {
        const container = $(containerSelector);
        container.empty();

        const renderLinks = (items, indent = 0, parentId = null) => {
            let html = '';

            items.forEach(item => {
                const checked = item.permission_status ? 'checked' : '';
                const margin = indent * 20;
                const isTopLevel = indent === 0;

                html += `
                <div class="form-check permission-item ${isTopLevel ? 'permission-parent' : ''}"
                     style="margin-left:${margin}px; ${isTopLevel ? 'margin-top: 12px;' : ''}">
                    <input class="form-check-input permission-checkbox"
                           type="checkbox"
                           value="${item.id}"
                           id="perm_${item.id}"
                           data-parent="${parentId ?? ''}"
                           ${checked}>
                    <label class="form-check-label"
                           for="perm_${item.id}"
                           style="${isTopLevel ? 'font-weight: bold;' : ''}">
                        ${item.display_name}
                    </label>
                </div>
            `;

                if (item.children && item.children.length > 0) {
                    html += renderLinks(item.children, indent + 1, item.id);
                }
            });

            return html;
        };

        // Render HTML
        const contentHTML = `
        <input type="text" id="permissionSearch" class="form-control mb-2" placeholder="Search permissions...">
        <div id="permissionList" class="scrollable-permission-box">
            ${renderLinks(links)}
        </div>
    `;

        container.html(contentHTML);

        syncAllParentCheckboxes(); // maintain parent-child state

        // Search filter
        $('#permissionSearch').on('keyup', function () {
            const search = $(this).val().toLowerCase();
            $('#permissionList .permission-item').each(function () {
                const label = $(this).text().toLowerCase();
                $(this).toggle(label.includes(search));
            });
        });
    }


    function syncAllParentCheckboxes() {
        $('.permission-checkbox').each(function () {
            updateParentCheckbox($(this));
        });
    }


    $(document).on('change', '.permission-checkbox', function() {
        const checkbox = $(this);
        const isChecked = checkbox.prop('checked');
        const id = checkbox.val();

        checkUncheckChildren(id, isChecked);
        updateParentCheckbox(checkbox);
    });

    function checkUncheckChildren(parentId, isChecked) {
        $(`.permission-checkbox[data-parent="${parentId}"]`).each(function() {
            $(this).prop('checked', isChecked);
            checkUncheckChildren($(this).val(), isChecked);
        });
    }

    function updateParentCheckbox(childCheckbox) {
        const parentId = childCheckbox.data('parent');
        if (!parentId) return;

        const parentCheckbox = $(`#perm_${parentId}`);
        if (parentCheckbox.length === 0) return;

        const siblings = $(`.permission-checkbox[data-parent="${parentId}"]`);
        const allChecked = siblings.length === siblings.filter(':checked').length;

        parentCheckbox.prop('checked', allChecked);

        updateParentCheckbox(parentCheckbox);
    }

    function updateRolePermissions() {
        const selectedPermissionIds = $('.permission-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        const roleId = $('#role_permission_id').val();

        $.ajax({
            url: `/api/update-role-permissions/${roleId}`,
            method: 'POST',
            data: {
                permissions: selectedPermissionIds
            },
            success: function (response) {
                Swal.fire('Success', 'Permissions updated successfully!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('rolePermissionModal')).hide();
            },
            error: function (xhr) {
                Swal.fire('Error', 'Something went wrong.', 'error');
            }
        });
    }

    function deleteRole(id) {
        if (confirm('Delete this role?')) {
            $.ajax({
                url: `${roleApiUrl}/${id}`,
                method: 'DELETE',
                success: function () {
                    loadRoles();
                    Swal.fire({
                        icon: 'success',
                        title: 'Role deleted successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Something went wrong!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        }
    }

    function closeRoleModal() {
        const modalElement = document.getElementById('roleModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
        $('#roleForm')[0].reset();
        $('#role_id').val('');
        $('.modal-title').text('Add Role');
        $('#saveRoleBtn').text('Save');
    }

    function openAddRoleModal() {
        $('#roleForm')[0].reset();
        $('#role_id').val('');
        $('.modal-title').text('Add Role');
        $('#saveRoleBtn').text('Save');
    }

    // Tab on Enter Key
    $(document).on('keydown', 'input, select, textarea', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const form = $(this).closest('form');
            const focusables = form.find('input, select, textarea, button').filter(':visible:not([readonly]):not([disabled])');
            const index = focusables.index(this);
            if (index > -1 && index + 1 < focusables.length) {
                focusables.eq(index + 1).focus();
            } else {
                $('#saveRoleBtn').click();
            }
        }
    });

    $('#roleForm').on('submit', function (e) {
        e.preventDefault();
    });
</script>
