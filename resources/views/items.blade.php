@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Items'])

<style>
    .common_input {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 10px 14px;
        font-size: 15px;
        box-shadow: none;
        transition: border-color 0.2s ease-in-out;
    }

    .common_input:focus {
        border-color: #4c9fff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
    }

    .form-label {
        font-weight: 500;
        font-size: 14px;
    }
</style>

<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Item List</h3>
                            </div>
                        </div>
                    </div>

                    <div class="white_card_body">
                        <div class="QA_section">
                            <div class="white_box_tittle list_header">
                                <h4></h4>
                                <div class="box_right d-flex lms_block">
                                    <div class="serach_field_2">
                                        <div class="search_inner">
                                            <form action="#">
                                                <div class="search_field">
                                                    <input type="text" placeholder="Search item..." class="searchBox" data-target="itemTable">
                                                </div>
                                                <button type="submit"> <i class="ti-search"></i> </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="add_button ms-2">
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#itemModal" onclick="openAddItemModal()">Add New</button>
                                    </div>
                                </div>
                            </div>

                            <div class="QA_table mb_30">
                                <table class="table lms_table_active">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Rack</th>
                                        <th>Category</th>
                                        <th>Sub-Category</th>
                                        <th>Supplier</th>
                                        <th>Unit</th>
                                        <th>List Price</th>
                                        <th>Retail Price</th>
                                        <th>Wholesale Price</th>
                                        <th>Active</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemTable">
                                    <!-- Dynamic rows -->
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

<!-- Add/Edit Item Modal -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="max-height: 95vh; overflow-y: auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalLabel">Add Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="itemForm">
                    <input type="hidden" id="item_id">

                    <!--  Item Details -->
                    <h5 class="mb-3">🧾 Item Details</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control common_input" id="item_code" required>
                            <div class="invalid-feedback">Code is required</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control common_input" id="description" required>
                            <div class="invalid-feedback">Description is required</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Rack Location</label>
                            <input type="text" class="form-control common_input" id="rack_location" required>
                            <div class="invalid-feedback">Rack Location is required</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Supplier</label>
                            <input type="text" id="supplier_name" class="form-control common_input" placeholder="Search supplier..." autocomplete="off" required>
                            <input type="hidden" id="supplier_id">
                            <div class="invalid-feedback">Supplier is required</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <input type="text" id="category_name" class="form-control common_input" placeholder="Search category..." autocomplete="off" required>
                            <input type="hidden" id="category_id">
                            <div class="invalid-feedback">Category is required</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Sub Category</label>
                            <input type="text" id="sub_category_name" class="form-control common_input" placeholder="Search sub category..." autocomplete="off" required>
                            <input type="hidden" id="sub_category_id">
                            <div class="invalid-feedback">Sub Category is required</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Measure Unit</label>
                            <select class="form-select common_input" id="measure_unit" required>
                                <option value="">Select</option>
                                <option value="kg">Kg</option>
                                <option value="g">g</option>
                                <option value="unit">Unit</option>
                                <option value="l">L</option>
                                <option value="ml">ml</option>
                            </select>
                            <div class="invalid-feedback">Measured Unit is required</div>
                        </div>

                        <div class="col-md-4 d-flex align-items-center mt-4">
                            <label class="me-3">Is Active</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" style="transform: scale(1.6);" checked>
                            </div>
                        </div>
                    </div>

                    <!--  Bundle Section -->
                    <h5 class="mt-5 mb-3">📦 Bundle</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">List Price</label>
                            <input type="number" class="form-control common_input" id="list_price" step="0.01"
                                   placeholder="Standard price before discounts" required>
                            <div class="invalid-feedback">List Price is required</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Retail Price</label>
                            <input type="number" class="form-control common_input" id="retail_price" step="0.01"
                                   placeholder="Selling price for regular customers" required>
                            <div class="invalid-feedback">Retail Price is required</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Wholesale Price</label>
                            <input type="number" class="form-control common_input" id="wholesale_price" step="0.01"
                                   placeholder="Selling price for bulk buyers" required>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-primary" id="saveItemBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- JS -->
<script>
    const apiUrl = '/api/items';

    $(document).ready(function () {
        loadItems();
    });

    // Load all items
    function loadItems() {
        $.get(apiUrl, function (data) {
            let table = $('.lms_table_active').DataTable();
            table.clear();
            let i = 1;
            data.forEach(item => {
                table.row.add([
                    i++,
                    item.code,
                    item.description,
                    item.rack_location ?? '—',
                    item.category?.name ?? '—',
                    item.sub_category?.name ?? '—',
                    item.supplier?.name ?? '—',
                    item.measure_unit ?? '—',
                    item.list_price ?? '0.00',
                    item.retail_price ?? '0.00',
                    item.wholesale_price ?? '0.00',
                    item.is_active ? 'Yes' : 'No',
                    `
                     <button class="btn btn-sm btn-primary" onclick="editItem('${item.id}')">Edit</button>
                     <!--<button class="btn btn-sm btn-danger ms-1" onclick="deleteItem('${item.id}')">Delete</button>-->
                    `
                ]);
            });
            table.draw();
        });
    }

    // Supplier Autocomplete
    $("#supplier_name").autocomplete({
        source: function (request, response) {
            if (request.term.length < 1) return;
            $.ajax({
                url: '/api/suppliers-list',
                dataType: 'json',
                data: { search_key: request.term },
                success: function (data) {
                    response(data);
                    if (data.length === 1) {
                        $("#supplier_name").val(data[0].label);
                        $("#supplier_id").val(data[0].value);
                    }
                }
            });
        },
        minLength: 1,
        appendTo: "#itemModal",
        focus: function (event, ui) {
            $("#supplier_name").val(ui.item.label);
            return false;
        },
        select: function (event, ui) {
            $("#supplier_name").val(ui.item.label);
            $("#supplier_id").val(ui.item.value);
            return false;
        }
    });

    // Category Autocomplete
    $("#category_name").autocomplete({
        source: function (request, response) {
            if (request.term.length < 1) return;
            $.ajax({
                url: '/api/categories-list',
                dataType: 'json',
                data: { search_key: request.term },
                success: function (data) {
                    response(data);
                    if (data.length === 1) {
                        $("#category_name").val(data[0].label);
                        $("#category_id").val(data[0].value);
                    }
                }
            });
        },
        minLength: 1,
        appendTo: "#itemModal",
        focus: function (event, ui) {
            $("#category_name").val(ui.item.label);
            return false;
        },
        select: function (event, ui) {
            $("#category_name").val(ui.item.label);
            $("#category_id").val(ui.item.value);
            return false;
        }
    });

    // Sub-Category Autocomplete
    $("#sub_category_name").autocomplete({
        source: function (request, response) {
            if (request.term.length < 1) return;
            $.ajax({
                url: '/api/sub-categories-list',
                dataType: 'json',
                data: { search_key: request.term },
                success: function (data) {
                    response(data);
                    if (data.length === 1) {
                        $("#sub_category_name").val(data[0].label);
                        $("#sub_category_id").val(data[0].value);
                    }
                }
            });
        },
        minLength: 1,
        appendTo: "#itemModal",
        focus: function (event, ui) {
            $("#sub_category_name").val(ui.item.label);
            return false;
        },
        select: function (event, ui) {
            $("#sub_category_name").val(ui.item.label);
            $("#sub_category_id").val(ui.item.value);
            return false;
        }
    });

    // Save
    $('#saveItemBtn').click(function () {
        saveItem();
    });

    function saveItem() {
        const id = $('#item_id').val();

        const data = {
            id: id,
            code: $('#item_code').val(),
            description: $('#description').val(),
            rack_location: $('#rack_location').val(),
            supplier_id: $('#supplier_id').val(),
            category_id: $('#category_id').val(),
            sub_category_id: $('#sub_category_id').val(),
            measure_unit: $('#measure_unit').val(),
            is_active: $('#is_active').is(':checked') ? 1 : 0,
            list_price: $('#list_price').val(),
            retail_price: $('#retail_price').val(),
            wholesale_price: $('#wholesale_price').val()
        };

        const method = id ? 'PUT' : 'POST';
        const url = id ? `${apiUrl}/${id}` : apiUrl;

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function () {
                Swal.fire({
                    icon: 'success',
                    title: id ? 'Updated' : 'Saved',
                    timer: 1500,
                    showConfirmButton: false
                });
                loadItems();
                closeItemModal();
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: xhr.responseJSON?.message || 'Error occurred!'
                });
            }
        });
    }

    // Edit
    function editItem(id) {
        $.get(`${apiUrl}/${id}`, function (data) {
            $('#item_id').val(data.id);
            $('#item_code').val(data.code);
            $('#description').val(data.description);
            $('#rack_location').val(data.rack_location);

            $('#supplier_id').val(data.supplier_id);
            $('#supplier_name').val(data.supplier?.name || '');

            $('#category_id').val(data.category_id);
            $('#category_name').val(data.category?.name || '');

            $('#sub_category_id').val(data.sub_category_id);
            $('#sub_category_name').val(data.sub_category?.name || '');

            $('#measure_unit').val(data.measure_unit);
            $('#is_active').prop('checked', data.is_active);

            $('#list_price').val(data.list_price);
            $('#retail_price').val(data.retail_price);
            $('#wholesale_price').val(data.wholesale_price);

            $('.modal-title').text('Edit Item');
            $('#saveItemBtn').text('Update');

            const modal = new bootstrap.Modal(document.getElementById('itemModal'));
            modal.show();
        });
    }

    //delete
    function deleteItem(id) {
        if (confirm('Delete this item?')) {
            $.ajax({
                url: `${apiUrl}/${id}`,
                method: 'DELETE',
                success: function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted Successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    loadItems();
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to delete item!',
                        showConfirmButton: true
                    });
                }
            });
        }
    }

    function openAddItemModal() {
        $('#itemForm')[0].reset();
        $('#item_id').val('');
        $('#supplier_id, #category_id, #sub_category_id').val('');
        $('#supplier_name, #category_name, #sub_category_name').val('');
        $('.modal-title').text('Add Item');
        $('#saveItemBtn').text('Save');
    }

    function closeItemModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('itemModal'));
        if (modal) modal.hide();
    }

    //tab navigation for Enter key
    $(document).on('keydown', 'input, select, textarea', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            const form = $(this).closest('form');
            const focusables = form.find('input, select, textarea, button')
                .filter(':visible:not([readonly]):not([disabled])');

            const index = focusables.index(this);
            const next = focusables.eq(index + 1);

            if (index > -1 && index + 1 < focusables.length) {
                if (next.is('button')) {
                    next.click();
                } else {
                    next.focus();
                }
            } else {
                $('#saveItemBtn').click();
            }
        }
    });
    // Prevent full form submission if user presses Enter accidentally
    $('#itemForm').on('submit', function (e) {
        e.preventDefault();
    });

</script>
