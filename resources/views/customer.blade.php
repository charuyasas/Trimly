@include('includes.header')
@include('includes.sidebar')

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                    <div class="page_title_left d-flex align-items-center">
                        <h3 class="f_s_25 f_w_700 dark_text mr_30">Customers</h3>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                            <li class="breadcrumb-item active">Customers</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Customer List</h3>
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
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="customerTable">
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
                                        <table class="table lms_table_active3">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Email</th>
                                                    <th scope="col">Phone</th>
                                                    <th scope="col">Address</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="customerTable">
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

@include('includes.footer')

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <input type="hidden" id="customer_id">
                    <div class="white_card_body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="name" placeholder="Customer Name">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="email" id="email" placeholder="Email">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="phone" class="contactNo" placeholder="Phone">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="address" placeholder="Address">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveCustomer()">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const apiUrl = '/api/customers';
    loadCustomers();

    function loadCustomers() {
        $.get(apiUrl, function(data) {
            let rows = '';
            let rowID = 1;
            data.forEach(customer => {
                rows += `
                    <tr>
                        <td>${rowID}</td>
                        <td>${customer.name}</td>
                        <td>${customer.email}</td>
                        <td>${customer.phone ?? ''}</td>
                        <td>${customer.address ?? ''}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" onclick="editCustomer(${customer.id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCustomer(${customer.id})">Delete</button>
                        </td>
                    </tr>
                `;
                rowID++;
            });
            $('#customerTable').html(rows);
        });
    }

    function saveCustomer() {
        const customer_id = $('#customer_id').val();
        const data = {
            name: $('#name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            address: $('#address').val()
        };

        if (customer_id) {
            $.ajax({
                url: `${apiUrl}/${customer_id}`,
                method: 'PUT',
                data: data,
                success: function() {
                    loadCustomers();
                    closeModal();
                    $('#customer_id').val('');
                },
                error: function(xhr) {
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
                    loadCustomers();
                    closeModal();
                },
                error: function(xhr) {
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

    function editCustomer(customer_id) {
        $.get(`${apiUrl}/${customer_id}`, function(customer) {
            $('#customer_id').val(customer.id);
            $('#name').val(customer.name);
            $('#email').val(customer.email);
            $('#phone').val(customer.phone);
            $('#address').val(customer.address);
        });
    }

    function deleteCustomer(customer_id) {
        if (confirm('Delete this customer?')) {
            $.ajax({
                url: `${apiUrl}/${customer_id}`,
                method: 'DELETE',
                success: loadCustomers
            });
        }
    }

    function closeModal() {
        const modalElement = document.getElementById('exampleModalCenter');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
            $('#customerForm')[0].reset();
            $('#customer_id').val('');
        }
    }
</script>
