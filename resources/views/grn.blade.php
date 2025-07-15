@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'GRN'])

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- GRN Modal Button -->
<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">GRN </h3>
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
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#grnModal" onclick="openAddGRNModal()">Add New GRN</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active ">
                                            <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">GRN Number</th>
                                                <th scope="col">GRN Date</th>
                                                <th scope="col">Supplier And Supplier Invoice Number</th>
                                                <th scope="col">Discount Type</th>
                                                <th scope="col">Store Location</th>
                                                <th scope="col">Total Before Discount</th>
                                                <th scope="col">Total FOC</th>
                                                <th scope="col">Total Discount</th>
                                                <th scope="col">Grand Total</th>
                                                <th scope="col">View Grn</th>
                                            </tr>
                                            </thead>
                                            <tbody id="grnTable">

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

<!-- GRN Modal -->
<div class="modal fade" id="grnModal" tabindex="-1" aria-labelledby="grnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90%; height: 90vh;">
        <div class="modal-content" style="height: 100%; overflow-y: auto;">
            <form id="grnForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add GRN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Step Tabs -->
                    <div class="mb-4 d-flex justify-content-center">
                        <ul class="nav nav-pills step-tabs" id="grnTabs">
                            <li class="nav-item">
                                <a class="nav-link active disabled" data-target="#grnStep1" onclick="event.preventDefault();">
                                    <strong>1</strong> Header Section
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" data-target="#grnStep2" onclick="event.preventDefault();">
                                    <strong>2</strong> Item Entry Section
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" data-target="#grnStep3" onclick="event.preventDefault();">
                                    <strong>3</strong> Finalization Section
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content px-3">
                        <!-- STEP 1 - HEADER -->
                        <div class="tab-pane fade show active" id="grnStep1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>GRN Number</label>
                                    <input type="text" class="form-control" id="grn_number">
                                    <input type="hidden" id="grn_id">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>GRN Date</label>
                                    <input type="date" class="form-control" id="grn_date" value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Supplier</label>
                                    <input type="text" class="form-control" id="supplier_name" placeholder="Search Supplier..." autocomplete="off">
                                    <input type="hidden" id="supplier_id">
                                    <input type="hidden" id="supplier_ledger_code">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Supplier Invoice Number</label>
                                    <input type="text" class="form-control" id="supplier_invoice_number">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label>GRN Type</label>
                                    <div class="d-flex gap-3 mt-1">
                                        <div class="form-check form-check-inline border rounded p-2 px-4">
                                            <input class="form-check-input" type="radio" name="grn_type" id="type_margin" value="Profit Margin" checked>
                                            <label class="form-check-label fw-bold" for="type_margin">Profit Margin</label>
                                        </div>
                                        <div class="form-check form-check-inline border rounded p-2 px-4">
                                            <input class="form-check-input" type="radio" name="grn_type" id="type_discount" value="Discount Based">
                                            <label class="form-check-label fw-bold" for="type_discount">Discount Based</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary" onclick="goToStep(2)">NEXT</button>
                            </div>
                        </div>

                        <!-- STEP 2 – ITEM ENTRY -->
                        <div class="tab-pane fade" id="grnStep2">
                            <div class="border p-3 rounded mb-3">
                                <div class="row g-2 align-items-start">
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" id="item_name" placeholder="Search Item..." autocomplete="off">
                                        <input type="hidden" id="item_id">
                                    </div>
                                    <div class="col-md-1"><input type="number" id="qty" class="form-control" placeholder="Qty"></div>
                                    <div class="col-md-1"><input type="number" id="foc" class="form-control" placeholder="FOC"></div>
                                    <div class="col-md-2"><input type="number" id="price" class="form-control" placeholder="Purchase Price"></div>
                                    <!-- MARGIN input group -->
                                    <div class="col-md-2" id="margin_group">
                                        <input type="number" id="margin" class="form-control" placeholder="Margin">
                                    </div>
                                    <!-- DISCOUNT input group -->
                                    <div class="col-md-2 d-none" id="discount_group">
                                        <input type="number" id="discount" class="form-control" placeholder="Discount">
                                    </div>
                                    <!-- DISCOUNT AMOUNT field (auto-calculated) -->
                                    <div class="col-md-2 d-none" id="discount_amount_group">
                                        <input type="text" id="discount_amount" class="form-control" placeholder="Dis. Amount" disabled>
                                    </div>
                                    <div class="col-md-2 text-center" id="item-action-buttons">
                                        <button type="button" class="btn btn-outline-secondary" id="btn_add_item" onclick="addItem()">+</button>

                                        <div class="d-none" id="edit_action_group">
                                            <button type="button" class="btn btn-outline-primary me-2" onclick="updateItem()">Update</button>
                                            <button type="button" class="btn btn-outline-danger" onclick="cancelEdit()">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered text-center mb-4">
                                <thead class="table-light">
                                <tr>
                                    <th>Action</th>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                    <th>FOC</th>
                                    <th>Price</th>
                                    <th>Margin</th>
                                    <th>Discount</th>
                                    <th>Final Price</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody id="itemTable"></tbody>
                            </table>

                            <!-- Bill Discount -->
                            <div id="discountSection" class="mt-4 border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <strong>Add Discount For Bill Value</strong>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleDiscountSection()">−</button>
                                </div>
                                <div class="form-check form-switch d-flex align-items-center gap-2 mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_percentage">
                                    <label class="form-check-label fw-semibold" for="is_percentage">Is Percentage Based</label>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <input type="number" class="form-control" id="bill_discount_amount" value="0.00" placeholder="Discount Amount">
                                    <span id="discountUnitLabel">LKR</span>
                                </div>
                                <div class="d-flex gap-2 mt-2">
                                    <button type="button" class="btn btn-success" onclick="applyDiscount()">✔</button>
                                    <button type="button" class="btn btn-danger" onclick="clearDiscount()">✖</button>
                                </div>
                            </div>

                            <div class="text-end pe-2">
                                <p>Total Before Discount: LKR <span id="totalBefore">0.00</span></p>
                                <p>Total FOC: LKR <span id="totalFOC">0.00</span></p>
                                <h5 class="text-danger fw-bold">Grand Total: LKR <span id="grandTotal">0.00</span></h5>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary" onclick="goToStep(3)">NEXT</button>
                            </div>
                        </div>

                        <!-- STEP 3 – FINALIZATION -->
                        <div class="tab-pane fade" id="grnStep3">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Store Location</label>
                                    <input type="text" id="store_location" class="form-control">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label>Note</label>
                                    <input type="text" id="note" class="form-control" placeholder="Optional notes">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" onclick="goToStep(2)">Back</button>
                                <button type="submit" class="btn btn-dark">Finalize GRN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

            <div class="modal fade" id="grnDetailModal" tabindex="-1" role="dialog" aria-labelledby="grnDetailModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">GRN Details</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="itemTable">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Item Code</th>
                                        <th scope="col">Item Description</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">Discount Percentage (%)</th>
                                        <th scope="col">Discount Amount</th>
                                        <th scope="col">Sub Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="7" class="text-end fw-bold">Grand Total</td>
                                        <td id="grandTotal" class="text-end fw-bold">0.00</td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="closeModal()">Close</button>
                        </div>
                    </div>
                </div>
            </div>



<script>

    const apiUrl = '/api/grn';
    loadGRN();

    function loadGRN() {
        $.get(apiUrl, function(data) {
            let table = $('.lms_table_active').DataTable();
            table.clear();

            let rowID = 1;
            data.forEach(grn => {
                table.row.add([
                    rowID,
                    grn.grn_number,
                    grn.grn_date,
                    grn.supplier_invoice_number,
                    grn.grn_type,
                    grn.store_location,
                    grn.total_before_discount,
                    grn.total_foc,
                    grn.discount_amount,
                    grn.grand_total,
                    `
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#grnDetailModal" onclick="loadInvoiceDetails('${grn.id}','${grn.invoice_no}')">View</button>
                            `
                ]);
                rowID++;
            });

            table.draw();
        });
    }

    function closeModal() {
        const modalElement = document.getElementById('invoiceDetailModal');
        const modal = bootstrap.Modal.getInstance(modalElement);

        if (modal) {
            modal.hide();
        }
    }
    let grnItems = [];
    let discountConfig = { amount: 0, isPercentage: false };
    let editingIndex = null;
    var itemSelectedFromAutocomplete = false;
    var SupplierSelectedFromAutocomplete = false;

    function openAddGRNModal() {
        $('#grnForm')[0].reset();
        $('#supplier_id').val('');
        $('#item_id').val('');
        grnItems = [];
        discountConfig = { amount: 0, isPercentage: false };
        editingIndex = null;
        renderGrnItems();
        calculateGrnTotals();
        switchTab(1);
    }

    let itemsList = [];
    let GrnDetails = [];

    function addItem() {
        const grnNumber = $("#grn_number").val();
        const grnID = $("#grn_id").val();
        const grnDate = $("#grn_date").val();
        const supplierID = $("#supplier_id").val();
        const supplierInvoiceNumber = $("#supplier_invoice_number").val();
        const grnType = $("input[name='grn_type']:checked").val();

        if (!grnNumber) {
            alert("Please enter 'GRN Number'!");
            return false;
        }

        if (!grnDate) {
            alert("Please enter 'GRN Date'!");
            return false;
        }

        if (!supplierID) {
            alert("Please enter 'Supplier'!");
            return false;
        }

        if (!supplierInvoiceNumber) {
            alert("Please enter 'Supplier Invoice Number'!");
            return false;
        }

        const itemID = $("#item_id").val();
        const itemDescription = $("#item_name").val();
        const qty = parseFloat($("#qty").val()) || 0;
        const foc = parseFloat($("#foc").val()) || 0;
        const price = parseFloat($("#price").val()) || 0;
        const margin = parseFloat($("#margin").val()) || 0;
        const discount = parseFloat($("#discount").val()) || 0;

        let finalPrice = 0;
        if (grnType === 'Profit Margin') {
            finalPrice = price + ((price * margin) / 100);
        } else {
            finalPrice = price - ((price * discount) / 100);
        }

        const subtotal = finalPrice * qty;

        const isDuplicate = itemsList.some(item => item.item_id === itemID);
        if (isDuplicate) {
            alert("Item already exists.");
            return false;
        }

        itemsList.push({
            grn_id: grnID,
            item_id: itemID,
            item_name: itemDescription,
            qty: qty,
            foc: foc,
            price: price,
            margin: grnType === 'Profit Margin' ? margin : null,
            discount: grnType === 'Discount Based' ? discount : null,
            final_price: finalPrice,
            subtotal: subtotal
        });

         GrnDetails = {
            id: grnID,
            grn_number: grnNumber,
            grn_date: $("#grn_date").val(),
            supplier_id: supplierID,
            supplier_invoice_number: $("#supplier_invoice_number").val(),
            supplier_ledger_code: $("#supplier_ledger_code").val(),
            grn_type: grnType,
            store_location: $("#store_location").val(),
            note: $("#note").val(),
            discount_amount: $("#bill_discount_amount").val(),
            is_percentage:$('#is_percentage').is(':checked') ? 1 : 0,
            items: itemsList
        };

        $.ajax({
            url: `/api/new-grn`,
            method: 'POST',
            data: GrnDetails,
            success: function(response) {
                const grnDataRes = response.grn;
                itemsList = grnDataRes.items;

                renderGrnItems();

                // Reset item fields
                $("#item_id").val('');
                $("#item_name").val('');
                $("#qty").val('');
                $("#foc").val('');
                $("#price").val('');
                $("#margin").val('');
                $("#discount").val('');
                $("#discount_amount").val('');

                $("#grn_number").val(grnDataRes.grn_number);
                $("#grn_id").val(grnDataRes.id);
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

    // Autocomplete for supplier
    $("#supplier_name").autocomplete({
        source: function (request, response) {
            if (request.term.length < 1) return;
            $.ajax({
                url: '/api/suppliers-list',
                dataType: 'json',
                data: { search_key: request.term },
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            label: item.label,
                            value: item.value,
                            id: item.value,
                            ledger_code: item.ledger_code
                        };
                    }));
                    if (data.length === 1) {
                        $("#supplier_name").val(data[0].label);
                        $("#supplier_id").val(data[0].value);
                        $("#supplier_ledger_code").val(data[0].ledger_code);
                        SupplierSelectedFromAutocomplete = true;
                    }
                }
            });
        },
        minLength: 1,
        appendTo: "#grnModal",
        select: function (event, ui) {
            $("#supplier_name").val(ui.item.label);
            $("#supplier_id").val(ui.item.value);
            $("#supplier_ledger_code").val(ui.item.ledger_code);
            SupplierSelectedFromAutocomplete = true;
            return false;
        }
    });

    $("#supplier_name").on("input", function () {
        if (!SupplierSelectedFromAutocomplete) {
            $("#supplier_ledger_code").val('');
        }
        SupplierSelectedFromAutocomplete = false;
    });

    // Autocomplete for item
    $("#item_name").autocomplete({
        source: function (request, response) {
            if (request.term.length < 1) return;
            $.ajax({
                url: '/api/items-list',
                dataType: 'json',
                data: { search_key: request.term },
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            label: item.label,
                            value: item.value,
                            id: item.value,
                            retail_price: item.retail_price
                        };
                    }));
                    if (data.length === 1) {
                        $("#item_name").val(data[0].label);
                        $("#item_id").val(data[0].value);
                        $("#price").val(data[0].retail_price);
                        $("#qty").val(1);
                        itemSelectedFromAutocomplete = true;
                    }
                }
            });
        },
        minLength: 1,
        appendTo: "#grnModal",
        select: function (event, ui) {
            $("#item_name").val(ui.item.label);
            $("#item_id").val(ui.item.value);
            $("#price").val(ui.item.retail_price);
            $("#qty").val(1);
            itemSelectedFromAutocomplete = true;
            return false;
        }
    });

    $("#item_name").on("input", function () {
        if (!itemSelectedFromAutocomplete) {
            $("#price").val('');
            $("#qty").val('');
        }
        itemSelectedFromAutocomplete = false;
    });

    function renderGrnItems() {
        const tbody = $('#itemTable').empty();
        let totalBefore = 0;
        itemsList.forEach((item, index) => {
            totalBefore += parseFloat(item.subtotal);
            tbody.append(`
            <tr>
                <td>
                    <a href="javascript:void(0)" onclick="editItem(${index})" class="text-primary">Edit</a> |
                    <a href="javascript:void(0)" onclick="removeItem(${index})" class="text-danger">Delete</a>
                </td>
                <td>${item.item_name}</td>
                <td>${item.qty}</td>
                <td>${item.foc}</td>
                <td>${item.price}</td>
                <td>${item.margin}</td>
                <td>${item.discount}</td>
                <td>${item.final_price}</td>
                <td>${item.subtotal}</td>
            </tr>
        `);
        });
        $('#totalBefore').text(totalBefore.toFixed(2));
        calculateGrnTotals();
    }

    function calculateGrnTotals() {
        let subtotal = itemsList.reduce((acc, item) => acc + parseFloat(item.subtotal), 0);
        let focTotal = itemsList.reduce((acc, item) => acc + (parseFloat(item.final_price) * parseFloat(item.foc)), 0);
        let grand = subtotal;
        let bill_discount_amount = $("#bill_discount_amount").val();

        if (bill_discount_amount > 0) {
            grand = $('#is_percentage').is(':checked')
                ? grand * (1 - bill_discount_amount / 100)
                : grand - bill_discount_amount;
        }

        $('#totalBefore').text(subtotal.toFixed(2));
        $('#totalFOC').text(focTotal.toFixed(2));
        $('#grandTotal').text(grand.toFixed(2));
    }

    function editItem(index) {
        const item = itemsList[index];
        editingIndex = index;

        $('#item_name').val(item.item_name);
        $('#item_id').val(item.item_id);
        $('#qty').val(item.qty);
        $('#foc').val(item.foc);
        $('#price').val(item.price);
        $('#margin').val(item.margin);
        $('#discount').val(item.discount);

        // Show discount amount live
        const base = item.price * item.qty;
        const discAmt = (base * item.discount) / 100;
        $('#discount_amount').val(discAmt.toFixed(2));

        // Toggle button groups
        $('#btn_add_item').addClass('d-none');
        $('#edit_action_group').removeClass('d-none');

        handleGrnTypeToggle();
    }

    function updateItem() {
        if (editingIndex === null) return;

        const item = {
            item_id: $('#item_id').val(),
            item_name: $('#item_name').val(),
            qty: parseFloat($('#qty').val()) || 0,
            foc: parseFloat($('#foc').val()) || 0,
            price: parseFloat($('#price').val()) || 0,
            margin: parseFloat($('#margin').val()) || 0,
            discount: parseFloat($('#discount').val()) || 0,
        };

        const grnType = $('input[name="grn_type"]:checked').val();
        item.final_price = grnType === 'Profit Margin'
            ? item.price * (1 + item.margin / 100)
            : item.price * (1 - item.discount / 100);

        const base = item.final_price * item.qty;
        item.subtotal = item.discount > 0 ? base - ((base * item.discount) / 100) : base;

        itemsList[editingIndex] = item;
        editingIndex = null;

        resetItemForm();
        renderGrnItems();
        calculateGrnTotals();
    }

    function cancelEdit() {
        editingIndex = null;
        resetItemForm();

        $('#btn_add_item').removeClass('d-none');
        $('#edit_action_group').addClass('d-none');
    }

    function resetItemForm() {
        $('#item_name, #item_id, #qty, #foc, #price, #margin, #discount, #discount_amount').val('');
        editingIndex = null;

        $('#btn_add_item').removeClass('d-none');
        $('#edit_action_group').addClass('d-none');
    }

    function removeItem(index) {
        itemsList.splice(index, 1);
        renderGrnItems();
        calculateGrnTotals();
    }

    function applyDiscount() {
        const val = parseFloat($('#bill_discount_amount').val()); // changed
        if (isNaN(val)) return Swal.fire('Error', 'Invalid discount value', 'error');

        discountConfig = {
            amount: val,
            isPercentage: $('#is_percentage').is(':checked')
        };
        calculateGrnTotals();
    }

    function clearDiscount() {
        // $('#bill_discount_amount').val('');
        $('#is_percentage').prop('checked', false);
        discountConfig = { amount: 0, isPercentage: false };
        calculateGrnTotals();
    }

    function switchTab(step) {
        $('.nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');

        $(`#grnTabs .nav-link:eq(${step - 1})`).addClass('active').removeClass('disabled');
        $(`#grnStep${step}`).addClass('show active');
    }

    function goToStep(step) {
        if (step === 2 && !validateStep1()) return;
        if (step === 3 && itemsList.length === 0) {
            return Swal.fire('Validation', 'Please add at least one item.', 'warning');
        }
        switchTab(step);
    }

    function validateStep1() {
        let valid = true;
        ['grn_number', 'grn_date', 'supplier_id', 'supplier_invoice_number'].forEach(id => {
            const input = $(`#${id}`);
            if (!input.val()) {
                input.addClass('is-invalid');
                valid = false;
            } else {
                input.removeClass('is-invalid');
            }
        });
        return valid;
    }

    $('#grnForm').on('submit', function (e) {
        e.preventDefault();

        const grnID = $('#grn_id').val();
        if (!grnID) {
            return Swal.fire('Validation Error', 'Please add and save GRN items before finalizing.', 'warning');
        }

        if (itemsList.length === 0) {
            return Swal.fire('Validation Error', 'No items added.', 'error');
        }

        GrnDetails.discount_amount = parseFloat($('#bill_discount_amount').val());
        GrnDetails.grand_total = parseFloat($('#grandTotal').text());
        GrnDetails.is_percentage = $('#is_percentage').is(':checked') ? 1 : 0;
        GrnDetails.store_location = $('#store_location').val();
        GrnDetails.note = $('#note').val();

        axios.post(`/api/grn-finalize/${grnID}`, GrnDetails)
            .then(res => {
                Swal.fire('Success', 'GRN finalized successfully.', 'success')
                    .then(() => location.reload());
            })
            .catch(err => {
                const msg = err.response?.data?.message || 'Something went wrong!';
                Swal.fire('Error', msg, 'error');
            });
    });

    $(document).ready(function () {
        // On initial load
        handleGrnTypeToggle();

        // On toggle
        $('input[name="grn_type"]').on('change', function () {
            handleGrnTypeToggle();
        });
    });

    function handleGrnTypeToggle() {
        const isMarginType = $('#type_margin').is(':checked');

        if (isMarginType) {
            $('#margin_group').removeClass('d-none');
            $('#discount_group').addClass('d-none');
            $('#discount').val('');
        } else {
            $('#discount_group').removeClass('d-none');
            $('#margin_group').addClass('d-none');
            $('#margin').val('');
        }
    }

    // Whenever discount % changes, compute and show discount amount
    $('#discount').on('input', function() {
        const pct   = parseFloat($(this).val()) || 0;
        const qty   = parseFloat($('#qty').val()) || 0;
        const price = parseFloat($('#price').val()) || 0;
        const base  = price * qty;

        const amt = (base * pct) / 100;
        $('#discount_amount').val(amt.toFixed(2));

        // keep form in sync if you're doing live totals
        calculateGrnTotals();
    });

    // Ensure discount_amount field appears when discount_group is shown
    function handleGrnTypeToggle() {
        const isMargin = $('#type_margin').is(':checked');

        if (isMargin) {
            $('#margin_group').removeClass('d-none');
            $('#discount_group, #discount_amount_group').addClass('d-none');
            $('#discount, #discount_amount').val('');
        } else {
            $('#margin_group').addClass('d-none');
            $('#discount_group, #discount_amount_group').removeClass('d-none');
            $('#margin').val('');
        }
    }

    let selectedGrnLabel = '';

    $("#grn_number").autocomplete({
        source: function (request, response) {
            if (request.term.length < 3) return;

            $.ajax({
                url: '/api/grn-list-dropdown',
                dataType: 'json',
                data: {
                    q: request.term
                },
                success: function (data) {
                    response(data);

                    if (data.length === 3) {
                        selectedGrnLabel = data[0].label;
                        $("#grn_number").val(data[0].label);
                        $("#grn_id").val(data[0].value);
                        fetchGrnDetails(data[0].value);
                    }
                }
            });
        },
        minLength: 1,
        appendTo: "#grnModal",
        select: function (event, ui) {
            selectedGrnLabel = ui.item.label;
            $("#grn_number").val(ui.item.label);
            $("#grn_id").val(ui.item.value);
            fetchGrnDetails(ui.item.value);
            return false;
        }
    });

    $("#grn_number").on('input', function () {
        const currentValue = $(this).val();
        if (currentValue !== selectedGrnLabel) {
            selectedGrnLabel = '';
            $("#grn_id").val('');
            $("#supplier_id").val('');
            $("#supplier_name").val('');
            $("#supplier_invoice_number").val('');
            $("#store_location").val('');
            $("#note").val('');
            // $("#bill_discount_amount").val('');
            $('#is_percentage').prop('checked', false);
            itemsList = [];
            renderGrnItems();
            calculateGrnTotals();
        }
    });

    function fetchGrnDetails(grnId) {
        $.ajax({
            url: `/api/grn-details/${grnId}`,
            method: 'GET',
            success: function (res) {
                GrnDetails = res.grn;

                // Fill header fields
                $("#grn_id").val(res.grn.id);
                $("#grn_number").val(res.grn.grn_number);
                $("#grn_date").val(res.grn.grn_date);
                $("#supplier_id").val(res.grn.supplier_id);
                $("#supplier_name").val(res.grn.supplier_name);
                $("#supplier_invoice_number").val(res.grn.supplier_invoice_number);
                $("#supplier_ledger_code").val(res.grn.supplier_ledger_code);
                $("#store_location").val(res.grn.store_location);
                $("#note").val(res.grn.note);
                $("#bill_discount_amount").val(res.grn.discount_amount);
                $('#is_percentage').prop('checked', res.grn.is_percentage === 1);

                // Set GRN type radio button
                if (res.grn.grn_type === 'Discount Based') {
                    $('#type_discount').prop('checked', true);
                } else {
                    $('#type_margin').prop('checked', true);
                }

                // Load items
                itemsList = GrnDetails.items;
                renderGrnItems();
                calculateGrnTotals();
            },
            error: function () {
                Swal.fire('Error', 'Failed to load GRN details.', 'error');
            }
        });
    }

    $(document).on('keydown', 'input, select, textarea, button', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            const focusables = $('input, select, textarea, button')
                .filter(':visible:not([readonly]):not([disabled])');

            const index = focusables.index(this);

            if ($(this).is('button')) {
                $(this).click(); // Optional: trigger button click
            }

            if (index > -1 && index + 1 < focusables.length) {
                focusables.eq(index + 1).focus();
            }
        }
    });

</script>
