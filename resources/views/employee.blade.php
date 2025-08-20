@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Employee'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Employee List </h3>
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
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="employeeTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" onclick="showModal()" data-bs-target="#exampleModalCenter">
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
                                                    <th scope="col">Employee ID</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Address</th>
                                                    <th scope="col">Phone No.</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="employeeTable">

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

            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Add Employee</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="employeeForm" onsubmit="saveEmployee(); return false;">
                                <input type="hidden" id="employee_id">
                                <div class="white_card_body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <label class="form-label">Employee ID</label>
                                                <input type="text" id="employeeID" placeholder="Enter ...">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <label class="form-label">Name</label>
                                                <input type="text" id="employee_name" placeholder="Enter ...">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <label class="form-label">Address</label>
                                                <input type="text" id="employee_address" placeholder="Enter ...">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <label class="form-label">Contact Number</label>
                                                <input type="text" id="employee_contactno" class="contactNo" placeholder="Enter ...">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <label class="form-label">Commission (%)</label>
                                                <input type="number" id="employee_commission" placeholder="Enter ..." onkeyup="validateCommission()">
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

                <script>
                    const apiUrl = '/api/employees';
                    loadEmployees();

                    function loadEmployees() {
                        $.get(apiUrl, function(data) {
                            let table = $('.lms_table_active').DataTable();
                            table.clear();

                            let rowID = 1;
                            data.forEach(employee => {
                                table.row.add([
                                rowID,
                                employee.employee_id,
                                employee.name,
                                employee.address,
                                employee.contact_no,
                                `
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" onclick="editEmployee('${employee.id}')">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteEmployee('${employee.id}')">Delete</button>
                                    `
                                ]);
                                rowID++;
                            });

                            table.draw();
                        });
                    }

                    function saveEmployee() {
                        const employee_id = $('#employee_id').val();
                        const data = {
                            id: $('#employee_id').val(),
                            employee_id: $('#employeeID').val(),
                            name: $('#employee_name').val(),
                            address: $('#employee_address').val(),
                            contact_no: $('#employee_contactno').val(),
                            commission: $('#employee_commission').val()
                        };

                        if (employee_id) {
                            $.ajax({
                                url: `${apiUrl}/${employee_id}`,
                                method: 'PUT',
                                data: data,
                                success: function() {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Updated Successfully",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    loadEmployees();
                                    closeModal();
                                    $('#employee_id').val('');
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
                        } else {
                            $.ajax({
                                url: `${apiUrl}`,
                                method: 'POST',
                                data: data,
                                success: function() {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Saved Successfully",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    loadEmployees();
                                    closeModal();
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
                    }

                    function closeModal() {
                        const modalElement = document.getElementById('exampleModalCenter');
                        const modal = bootstrap.Modal.getInstance(modalElement);

                        if (modal) {
                            modal.hide();
                            $('#employeeForm')[0].reset();
                        }
                    }

                    function showModal() {
                        $('#employeeForm')[0].reset();
                        $('#employee_id').val("");
                        $('#exampleModalLongTitle').text('Add Employee');
                        $('#saveBtn').text('Save');
                    }

                    function editEmployee(employee_id) {
                        $.get(`${apiUrl}/${employee_id}`, function(employee) {
                            $('#employee_id').val(employee.id);
                            $('#employeeID').val(employee.employee_id);
                            $('#employee_name').val(employee.name);
                            $('#employee_address').val(employee.address);
                            $('#employee_contactno').val(employee.contact_no);
                            $('#employee_commission').val(employee.commission);
                            $('#exampleModalLongTitle').text('Edit Employee');
                            $('#saveBtn').text('Update');
                        });
                    }

                    function deleteEmployee(employee_id) {
                        if (confirm('Delete this employee?')) {
                            $.ajax({
                                url: `${apiUrl}/${employee_id}`,
                                method: 'DELETE',
                                success: function() {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted Successfully",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    loadEmployees();
                                }
                            });
                        }
                    }

                    function validateCommission(){
                        let commission = parseFloat($('#employee_commission').val()) || 0;
                        if(commission < 0 || commission > 100) {
                            alert(`Commission percentage must be between 0 and 100.`);
                            $("#employee_commission").val('');
                        }
                    }

                    $(document).on('keydown', 'input, select, textarea, button', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();

                            const focusables = $('input, select, textarea, button')
                            .filter(':visible:not([readonly]):not([disabled])');

                            const index = focusables.index(this);

                            if (index > -1 && index + 1 < focusables.length) {
                                const next = focusables.eq(index + 1);
                                next.focus();

                                if (next.is('button') && next.text().trim() === 'Save' || next.is('button') && next.text().trim() === 'Update') {
                                    next.click();
                                }
                            } else {
                                saveEmployee();
                            }
                        }
                    });

                    $(document).on('click', 'button', function () {
                        if ($(this).text().trim() === 'Save' || $(this).text().trim() === 'Update') {
                            saveEmployee();
                        }
                    });

                </script>
