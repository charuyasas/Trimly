@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Customers'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
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
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" onclick="openAddCustomerModal()">
                                                    Add New
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active">
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
    </div>
</div>

@include('includes.footer')

<!--Add Customer Modal-->
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
                    <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveCustomer()">Save</button>
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
        let table = $('.lms_table_active').DataTable();
        table.clear();

        let rowID = 1;
        data.forEach(customer => {
            table.row.add([
                rowID,
                customer.name,
                customer.email,
                customer.phone ?? '',
                customer.address ?? '',
                `
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" onclick="editCustomer('${customer.id}')">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteCustomer('${customer.id}')">Delete</button>
                `
            ]);
            rowID++;
        });

        table.draw();
      });
    }

    function saveCustomer() {
        const customer_id = $('#customer_id').val();
        const data = {
            id: customer_id,
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
                    Swal.fire({
                        icon: "success",
                        title: "Updated Successfully",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    loadCustomers();
                    closeModal();
                    $('#customer_id').val('');
                },
                error: function(xhr) {
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
                    loadCustomers();
                    closeModal();
                },
                error: function(xhr) {
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
                            icon: "success",
                            title: "Something went wrong",
                            showConfirmButton: false,
                            timer: 1500
                        });
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
            $('.modal-title').text('Edit Customer');
            $('#saveBtn').text('Update');
        });
    }

    function deleteCustomer(customer_id) {
      if (confirm('Delete this customer?')) {
        $.ajax({
            url: `${apiUrl}/${customer_id}`,
            method: 'DELETE',
            success: function () {
                loadCustomers();

                Swal.fire({
                    icon: 'success',
                    title: 'Customer deleted successfully!',
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


    function closeModal() {
        const modalElement = document.getElementById('exampleModalCenter');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
            $('#customerForm')[0].reset();
            $('#customer_id').val('');
        }
    }

    // open modal
    function openAddCustomerModal() {
    $('#customerForm')[0].reset();
    $('#customer_id').val('');
    $('.modal-title').text('Add Customer');
    $('#saveBtn').text('Save');
    }

    //tab navigation for Enter key
     $(document).on('keydown', 'input, select, textarea', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            const form = $(this).closest('form');
            const focusables = form.find('input, select, textarea, button')
                .filter(':visible:not([readonly]):not([disabled])');

            const index = focusables.index(this);

            if (index > -1 && index + 1 < focusables.length) {
                focusables.eq(index + 1).focus();
            } else {
                $('#saveBtn').click();
            }
        }
    });


</script>
