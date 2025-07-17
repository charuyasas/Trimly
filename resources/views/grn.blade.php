@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'GRN'])

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

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
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="grnTable">
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

                                    <!-- Filter GRNs Toggle Header -->
                                    <div class="d-flex justify-content-between align-items-center mb-3 border rounded px-4 py-3 bg-white shadow-sm">
                                        <h6 class="fw-semibold text-dark mb-0 fs-6">Filter GRNs</h6>
                                        <button class="btn btn-light border d-flex align-items-center justify-content-center rounded-circle shadow-sm"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#grnFilters"
                                                aria-expanded="false"
                                                aria-controls="grnFilters"
                                                style="width: 38px; height: 38px;">
                                            <i class="ti-plus fs-5 text-primary"></i>
                                        </button>
                                    </div>


                                    <!-- Collapsible Filters Panel -->
                                    <div class="collapse mb-3" id="grnFilters">
                                        <div class="card card-body shadow-sm p-4">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Search</label>
                                                    <input type="text" class="form-control" id="filter_search" placeholder="Search GRNs by number, supplier, or invoice number">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Suppliers</label>
                                                    <input type="text" class="form-control" id="filter_supplier_name" placeholder="Search supplier..." autocomplete="off">
                                                    <input type="hidden" id="filter_supplier_id">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Date Range</label>
                                                    <input type="text" class="form-control" id="filter_date_range" placeholder="Select Date Range">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Subtotal Min (LKR)</label>
                                                    <input type="number" class="form-control" id="filter_subtotal_min" placeholder="Min">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Subtotal Max (LKR)</label>
                                                    <input type="number" class="form-control" id="filter_subtotal_max" placeholder="Max">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                <button class="btn btn-secondary" onclick="clearGrnFilters()">Clear Filters</button>
                                                <button class="btn btn-primary" onclick="applyGrnFilters()">Apply Filters</button>
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
                                        <input type="hidden" id="grn_item_id">
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
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 95%; height: 90vh;">
        <div class="modal-content" style="height: 100%; overflow-y: auto;">
            <div class="modal-header">
                <h5 class="modal-title">GRN Details - <span id="modalGrnNumber">0000</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <!-- Summary Section -->
                <div class="row mb-4 px-4 py-3 rounded shadow-sm" style="background-color: #f8f9fa;">
                    <div class="col-md-3 mb-3">
                        <p class="mb-1 text-muted fw-semibold">GRN Number</p>
                        <h6 id="modalGrnNumberDisplay" class="mb-0 text-dark fw-semibold">-</h6>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="mb-1 text-muted fw-semibold">Date</p>
                        <h6 id="modalGrnDate" class="mb-0 text-dark fw-semibold">-</h6>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="mb-1 text-muted fw-semibold">Supplier & Invoice</p>
                        <h6 id="modalSupplierAndInvoice" class="mb-0 text-dark fw-semibold">-</h6>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="mb-1 text-muted fw-semibold">Discount Type</p>
                        <h6 id="modalDiscountType" class="mb-0 text-dark fw-semibold">-</h6>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="mb-1 text-muted fw-semibold">Store Location</p>
                        <h6 id="modalStoreLocation" class="mb-0 text-dark fw-semibold">-</h6>
                    </div>
                    <div class="col-md-9 mb-3">
                        <p class="mb-1 text-muted fw-semibold">Note</p>
                        <h6 id="modalNote" class="mb-0 text-dark fw-semibold">-</h6>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-responsive mb-3">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>FOC</th>
                            <th>Purchase Price</th>
                            <th>Margin</th>
                            <th>Discount</th>
                            <th>Final Retail Price</th>
                            <th>Sub Total</th>
                        </tr>
                        </thead>
                        <tbody id="grnDetailTableBody">
                        <tr>
                            <td colspan="8" class="text-danger text-center fw-semibold">Warning: Item details are missing for all GRN items.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals Section -->
                <div class="text-end">
                    <p><strong>Total Before Discount:</strong> LKR <span id="modalTotalBefore">0.00</span></p>
                    <p><strong>Total Discount:</strong> (<span id="modalTotalDiscount">0.00</span>)</p>
                    <p><strong>Total FOC:</strong> LKR <span id="modalTotalFOC">0.00</span></p>
                    <h5 class="fw-bold text-danger">Grand Total: LKR <span id="modalGrandTotal">0.00</span></h5>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                    `${grn.supplier?.name ?? 'N/A'} - ${grn.supplier_invoice_number}`,
                    grn.grn_type,
                    grn.store_location,
                    grn.total_before_discount,
                    grn.total_foc,
                    grn.discount_amount,
                    grn.grand_total,
                    `
                      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#grnDetailModal" onclick="viewGrnDetails('${grn.id}')">View</button>
                    `
                ]);
                rowID++;
            });

            table.draw();
        });
    }

    $(document).ready(function () {
        const table = $('.lms_table_active').DataTable();

        $('.searchBox').on('keyup', function () {
            const searchValue = $(this).val();
            table.search(searchValue).draw();
        });

        $('#grnModal').on('hidden.bs.modal', function () {
            loadGRN();
        });
    });

    function viewGrnDetails(grnId) {
        $.ajax({
            url: `/api/grn-details/${grnId}`,
            method: 'GET',
            success: function (res) {
                const grn = res.grn;
                const items = grn.items || [];

                $('#modalGrnNumber').text(grn.grn_number);
                $('#modalGrnNumberDisplay').text(grn.grn_number);
                $('#modalGrnDate').text(grn.grn_date);
                $('#modalStoreLocation').text(grn.store_location || '-');
                $('#modalNote').text(grn.note || '-');
                $('#modalSupplierAndInvoice').text(`${grn.supplier_name ?? 'N/A'} - ${grn.supplier_invoice_number}`);
                $('#modalDiscountType').text(grn.grn_type);

                // Render item table
                const tbody = $('#grnDetailTableBody').empty();
                let grandTotal = 0;
                let totalBefore = 0;
                let totalFOC = 0;

                if (items.length === 0) {
                    tbody.append(`<tr>
                    <td colspan="8" class="text-danger text-center fw-semibold">
                        Warning: Item details are missing for all GRN items.
                    </td>
                </tr>`);
                } else {
                    items.forEach(item => {
                        const price = parseFloat(item.price || 0);
                        const qty = parseFloat(item.qty || 0);
                        const foc = parseFloat(item.foc || 0);
                        const margin = parseFloat(item.margin || 0);
                        const discount = parseFloat(item.discount || 0);
                        const finalPrice = parseFloat(item.final_price || 0);
                        const subtotal = parseFloat(item.subtotal || 0);

                        totalBefore += subtotal;
                        totalFOC += foc * finalPrice;
                        grandTotal += subtotal;

                        tbody.append(`
                        <tr>
                            <td>${item.item_name ?? 'N/A'}</td>
                            <td>${qty}</td>
                            <td>${foc}</td>
                            <td>${price.toFixed(2)}</td>
                            <td>${margin.toFixed(2)}</td>
                            <td>${discount.toFixed(2)}</td>
                            <td>${finalPrice.toFixed(2)}</td>
                            <td>${subtotal.toFixed(2)}</td>
                        </tr>
                    `);
                    });
                }

                let billDiscount = parseFloat(grn.discount_amount || 0);
                let discountDisplay = grn.is_percentage == 1
                    ? `${billDiscount.toFixed(2)}%`
                    : `LKR ${billDiscount.toFixed(2)}`;

                if (grn.is_percentage == 1) {
                    grandTotal = grandTotal * (1 - billDiscount / 100);
                } else {
                    grandTotal -= billDiscount;
                }

                $('#modalTotalBefore').text(totalBefore.toFixed(2));
                $('#modalTotalDiscount').text(discountDisplay);
                $('#modalTotalFOC').text(totalFOC.toFixed(2));
                $('#modalGrandTotal').text(grandTotal.toFixed(2));
            },
            error: function () {
                Swal.fire('Error', 'Could not load GRN details.', 'error');
            }
        });
    }

    function closeModal() {
        const modalElement = document.getElementById('grnDetailModal');
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
        $('#grn_item_id').val(item.id || '');
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
        const itemDBId = $('#grn_item_id').val();  // GRN Item DB id

        if (!itemDBId) {
            Swal.fire('Error', 'Missing item ID for update', 'error');
            return;
        }

        const item = {
            item_name: $('#item_name').val(),
            qty: parseFloat($('#qty').val()) || 0,
            foc: parseFloat($('#foc').val()) || 0,
            price: parseFloat($('#price').val()) || 0,
            margin: parseFloat($('#margin').val()) || 0,
            discount: parseFloat($('#discount').val()) || 0
        };

        const grnType = $('input[name="grn_type"]:checked').val();

        item.final_price = grnType === 'Profit Margin'
            ? item.price * (1 + item.margin / 100)
            : item.price * (1 - item.discount / 100);

        item.subtotal = item.final_price * item.qty;

        axios.put(`/api/grn-item-update/${itemDBId}`, {
            ...item,
            final_price: item.final_price,
            subtotal: item.subtotal
        })
            .then(res => {
                if (editingIndex !== null) {
                    itemsList[editingIndex] = {
                        ...item,
                        item_id: $('#item_id').val(),
                        id: itemDBId,
                        item_name: $('#item_name').val(),
                        final_price: item.final_price,
                        subtotal: item.subtotal
                    };
                }

                Swal.fire('Updated', 'Item updated successfully', 'success');
                editingIndex = null;
                renderGrnItems();
                resetItemForm();
                calculateGrnTotals();
                loadGRN();
            })
            .catch(err => {
                Swal.fire('Error', 'Failed to update item.', 'error');
            });
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
        $('#grn_item_id').val('');
        $('#btn_add_item').removeClass('d-none');
        $('#edit_action_group').addClass('d-none');
    }

    function removeItem(index) {
        const item = itemsList[index];

        // Confirm deletion
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to remove this item from the GRN.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (!result.isConfirmed) return;

            // If item has a DB id (i.e. already saved), delete from backend
            if (item.id) {
                $.ajax({
                    url: `/api/grn-item-delete/${item.id}`,
                    type: 'DELETE',
                    success: function () {
                        itemsList.splice(index, 1);
                        renderGrnItems();
                        calculateGrnTotals();
                        loadGRN();
                        Swal.fire('Deleted!', 'Item has been removed.', 'success');
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to delete item.', 'error');
                    }
                });
            } else {
                // Frontend only (unsaved item)
                itemsList.splice(index, 1);
                renderGrnItems();
                calculateGrnTotals();
            }
        });
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

    function applyGrnFilters() {
        const search = $('#filter_search').val().trim().toLowerCase();
        const supplier = $('#filter_supplier_id').val();
        const dateRange = $('#filter_date_range').val();
        const subtotalMin = parseFloat($('#filter_subtotal_min').val()) || 0;
        const subtotalMax = parseFloat($('#filter_subtotal_max').val()) || Infinity;

        $.get(apiUrl, function(data) {
            const table = $('.lms_table_active').DataTable();
            table.clear();

            let rowID = 1;

            data.filter(grn => {
                const matchSearch = !search || (
                    grn.grn_number?.toLowerCase().includes(search) ||
                    grn.supplier?.name?.toLowerCase().includes(search) ||
                    grn.supplier_invoice_number?.toLowerCase().includes(search)
                );

                const matchSupplier = !supplier || grn.supplier_id == supplier;

                const dateRange = $('#filter_date_range').val().trim();
                let matchDate = true;

                if (dateRange && dateRange.includes(' - ')) {
                    const [startStr, endStr] = dateRange.split(' - ');
                    const start = moment(startStr, 'YYYY-MM-DD').startOf('day');
                    const end = moment(endStr, 'YYYY-MM-DD').endOf('day');

                    matchDate = grn.grn_date && moment(grn.grn_date).isBetween(start, end, null, '[]');
                }

                const subtotal = parseFloat(grn.total_before_discount || 0);
                const matchSubtotal = subtotal >= subtotalMin && subtotal <= subtotalMax;

                return matchSearch && matchSupplier && matchDate && matchSubtotal;
            }).forEach(grn => {
                table.row.add([
                    rowID++,
                    grn.grn_number,
                    grn.grn_date,
                    `${grn.supplier?.name ?? 'N/A'} - ${grn.supplier_invoice_number}`,
                    grn.grn_type,
                    grn.store_location,
                    grn.total_before_discount,
                    grn.total_foc,
                    grn.discount_amount,
                    grn.grand_total,
                    `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#grnDetailModal" onclick="viewGrnDetails('${grn.id}')">View</button>`
                ]);
            });

            table.draw();
        });
    }

    function clearGrnFilters() {
        $('#filter_search, #filter_supplier_name, #filter_date_range, #filter_subtotal_min, #filter_subtotal_max, #filter_supplier_id').val('');
        loadGRN();
    }

    let filterSupplierSelected = false;

    $("#filter_supplier_name").autocomplete({
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

                    // Auto select if exactly one match
                    if (data.length === 1) {
                        $("#filter_supplier_name").val(data[0].label);
                        $("#filter_supplier_id").val(data[0].value);
                        filterSupplierSelected = true;
                    }
                }
            });
        },
        minLength: 1,
        appendTo: "#grnFilters", // match the filter collapsible wrapper
        select: function (event, ui) {
            $("#filter_supplier_name").val(ui.item.label);
            $("#filter_supplier_id").val(ui.item.value);
            filterSupplierSelected = true;
            return false;
        }
    });


    $("#filter_supplier_name").on("input", function () {
        if (!filterSupplierSelected) {
            $("#filter_supplier_id").val('');
        }
        filterSupplierSelected = false;
    });


    $(function () {
        $('#filter_date_range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('#filter_date_range').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('#filter_date_range').on('cancel.daterangepicker', function () {
            $(this).val('');
        });
    });

</script>

<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
