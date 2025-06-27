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
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter">
                                                    Add New
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active3 ">
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
                            <form id="employeeForm">
                                <input type="hidden" id="employee_id">
                                <div class="white_card_body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" id="employeeID" placeholder="Employee ID">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" id="employee_name" placeholder="Name">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" id="employee_address" placeholder="Address">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" id="employee_contactno" class="contactNo" placeholder="Contact Number">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                                <button type="button" class="btn btn-primary" onclick="saveEmployee()">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script>
                    const apiUrl = '/api/employees';
                    loadEmployees();
                    
                    function loadEmployees() {
                        $.get(apiUrl, function(data) {
                            let rows = '';
                            let rowID = 1;
                            data.forEach(employee => {
                                rows += `
                                <tr>
                                    <td>${rowID}</td>
                                    <td>${employee.employee_id}</td>
                                    <td>${employee.name}</td>
                                    <td>${employee.address}</td>
                                    <td>${employee.contact_no}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" onclick="editEmployee(${employee.id})">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteEmployee(${employee.id})">Delete</button>
                                    </td>
                                </tr>
                            `;
                                rowID++;
                            });
                            $('#employeeTable').html(rows);
                        });
                    }
                    
                    function saveEmployee() {
                        const employee_id = $('#employee_id').val();
                        const data = {
                            employee_id: $('#employeeID').val(),
                            name: $('#employee_name').val(),
                            address: $('#employee_address').val(),
                            contact_no: $('#employee_contactno').val()
                        };
                        
                        if (employee_id) {
                            $.ajax({
                                url: `${apiUrl}/${employee_id}`,
                                method: 'PUT',
                                data: data,
                                success: function() {
                                    loadEmployees();
                                    closeModal();
                                    $('#employee_id').val('');
                                },
                                error: function (xhr) {
                                    if (xhr.status === 422) {
                                        const response = xhr.responseJSON;
                                        alert(response.message);
                                    } else {
                                        alert('Something went wrong.');
                                    }
                                }
                            });
                        } else {
                            $.ajax({
                                url: `${apiUrl}`,
                                method: 'POST',
                                data: data,
                                success: function() {
                                    loadEmployees();
                                    closeModal();
                                },
                                error: function (xhr) {
                                    if (xhr.status === 422) {
                                        const response = xhr.responseJSON;
                                        alert(response.message);
                                    } else {
                                        alert('Something went wrong.');
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
                    
                    function editEmployee(employee_id) {
                        $.get(`${apiUrl}/${employee_id}`, function(employee) {
                            $('#employee_id').val(employee.id);
                            $('#employeeID').val(employee.employee_id);
                            $('#employee_name').val(employee.name);
                            $('#employee_address').val(employee.address);
                            $('#employee_contactno').val(employee.contact_no);
                        });
                    }
                    
                    function deleteEmployee(employee_id) {
                        if (confirm('Delete this employee?')) {
                            $.ajax({
                                url: `${apiUrl}/${employee_id}`,
                                method: 'DELETE',
                                success: loadEmployees
                            });
                        }
                    }
                    
                    
                    
                </script>
                
                
                