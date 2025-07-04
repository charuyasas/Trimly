@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Suppliers'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Supplier List</h3>
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
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="supplierTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal" onclick="openAddSupplierModal()">
                                                    Add New
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Code</th>
                                                    <th>Name</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Address</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="supplierTable">
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

<!-- Add/Edit Supplier Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="supplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Supplier</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="supplierForm">
                    <input type="hidden" id="supplier_id">
                    <div class="white_card_body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="supplier_code" placeholder="Supplier Code">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="supplier_name" placeholder="Supplier Name">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="supplier_contact" class="contactNo" placeholder="Contact No">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="email" id="supplier_email" placeholder="Email">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="supplier_address" placeholder="Address">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                    <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveSupplier()">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const apiUrl = '/api/suppliers';
    loadSuppliers();

    function loadSuppliers() {
        $.get(apiUrl, function(data) {
            let table = $('.lms_table_active').DataTable();
            table.clear();

            let rowID = 1;
            data.forEach(supplier => {
                table.row.add([
                    rowID++,
                    supplier.supplier_code,
                    supplier.name,
                    supplier.contact_no ?? '',
                    supplier.email ?? '',
                    supplier.address ?? '',
                    `
                    <button class="btn btn-sm btn-primary" onclick="editSupplier('${supplier.id}')">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteSupplier('${supplier.id}')">Delete</button>
                    `
                ]);
            });

            table.draw();
        });
    }

    function saveSupplier() {
        const supplier_id = $('#supplier_id').val();
        const data = {
            id: supplier_id,
            supplier_code: $('#supplier_code').val(),
            name: $('#supplier_name').val(),
            contact_no: $('#supplier_contact').val(),
            email: $('#supplier_email').val(),
            address: $('#supplier_address').val()
        };

        const method = supplier_id ? 'PUT' : 'POST';
        const url = supplier_id ? `${apiUrl}/${supplier_id}` : apiUrl;

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function () {
                Swal.fire({
                    icon: "success",
                    title: supplier_id ? "Updated Successfully" : "Saved Successfully",
                    showConfirmButton: false,
                    timer: 1500
                });
                loadSuppliers();
                closeModal();
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

    function editSupplier(id) {
        $.get(`${apiUrl}/${id}`, function(supplier) {
            $('#supplier_id').val(supplier.id);
            $('#supplier_code').val(supplier.supplier_code);
            $('#supplier_name').val(supplier.name);
            $('#supplier_contact').val(supplier.contact_no);
            $('#supplier_email').val(supplier.email);
            $('#supplier_address').val(supplier.address);
            $('.modal-title').text('Edit Supplier');
            $('#saveBtn').text('Update');
            const modal = new bootstrap.Modal(document.getElementById('supplierModal'));
            modal.show();
        });
    }

    function deleteSupplier(id) {
        if (confirm('Delete this supplier?')) {
            $.ajax({
                url: `${apiUrl}/${id}`,
                method: 'DELETE',
                success: function () {
                    loadSuppliers();
                    Swal.fire({
                        icon: 'success',
                        title: 'Supplier deleted successfully!',
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
        const modalElement = document.getElementById('supplierModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
        $('#supplierForm')[0].reset();
        $('#supplier_id').val('');
        $('.modal-title').text('Add Supplier');
        $('#saveBtn').text('Save');
    }

    function openAddSupplierModal() {
        $('#supplierForm')[0].reset();
        $('#supplier_id').val('');
        $('.modal-title').text('Add Supplier');
        $('#saveBtn').text('Save');
    }

    // Tab navigation on Enter
    $(document).on('keydown', 'input, select, textarea', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const form = $(this).closest('form');
            const focusables = form.find('input, select, textarea, button').filter(':visible:not([readonly]):not([disabled])');
            const index = focusables.index(this);
            if (index > -1 && index + 1 < focusables.length) {
                focusables.eq(index + 1).focus();
            } else {
                $('#saveBtn').click();
            }
        }
    });

    $('#supplierForm').on('submit', function (e) {
        e.preventDefault();
    });
</script>
