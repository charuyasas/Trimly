@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'GRN'])

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    #grnModal .select2-container--default .select2-selection--single {
        height: 38px;
        padding: .375rem .75rem;
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

<div class="modal fade" id="grnModal" tabindex="-1" aria-labelledby="grnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90%; height: 90vh;">
        <div class="modal-content" style="height: 100%; overflow-y: auto;">
            <form id="grnForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add GRN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
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
                        </ul>
                    </div>

                    <div class="tab-content px-3">
                        <div class="tab-pane fade show active" id="grnStep1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Token Number</label>
                                    <select class="form-select" id="token_no"></select>
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
                                <button type="button" class="btn btn-primary" id="grn_step1_next" onclick="goToStep(2)">NEXT</button>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="grnStep2">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="border p-3 rounded mb-3">
                                        <div class="row g-2 align-items-start">
                                            <div class="col-md-2">
                                                <label for="item_name" class="form-label">Item</label>
                                                <input type="text" class="form-control" id="item_name" placeholder="Search Item..." autocomplete="off">
                                                <input type="hidden" id="item_id">
                                                <input type="hidden" id="grn_item_id">
                                            </div>
                                            <div class="col-md-2">
                                                <label for="qty" class="form-label">Qty</label>
                                                <input type="number" id="qty" class="form-control" min="1" step="1">
                                            </div>
                                            <div class="col-md-2">
                                                <label for="foc" class="form-label">FOC</label>
                                                <input type="number" id="foc" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label for="price" class="form-label">Purchase Price</label>
                                                <input type="number" id="price" class="form-control">
                                            </div>
                                            <div class="col-md-2" id="margin_group">
                                                <label for="margin" class="form-label">Margin (%)</label>
                                                <input type="number" id="margin" class="form-control">
                                            </div>
                                            <div class="col-md-1 d-none" id="discount_group">
                                                <label for="discount" class="form-label">Disc.(%)</label>
                                                <input type="number" id="discount" class="form-control">
                                            </div>
                                            <div class="col-md-2 d-none" id="discount_amount_group">
                                                <label for="discount_amount" class="form-label">Discount Amount</label>
                                                <input type="text" id="discount_amount" class="form-control" disabled>
                                            </div>
                                            <div class="col-md-1 text-center" id="item-action-buttons">
                                                <label class="form-label d-block invisible">Action</label>
                                                <button type="button" class="btn btn-outline-secondary" id="btn_add_item" onclick="addItem()">+</button>
                                                <div class="d-none d-flex gap-2" id="edit_action_group">
                                                    <button type="button" class="btn btn-outline-primary flex-fill" onclick="updateItem()">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger flex-fill" onclick="cancelEdit()">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <input type="text" id="grnTableFilter" class="form-control" placeholder="Filter items..." onkeyup="filterGrnTable()" />
                                    </div>

                                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
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
                                    </div>
                                    <br>
                                    <div class="d-flex justify-content-between gap-4 mt-4 align-items-center">
                                        <div id="discountSection" class="border rounded p-2 flex-fill" style="max-width: 50%; min-height: 100px;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong>Add Discount For Bill Value</strong>
                                                <button id="discountToggleBtn" type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleDiscountSection()">−</button>
                                            </div>

                                            <div id="discountSectionContent">
                                                <div class="form-check form-switch d-flex align-items-center gap-2 mb-2">
                                                    <input class="form-check-input" type="checkbox" id="is_percentage">
                                                    <label class="form-check-label fw-semibold" for="is_percentage">Is Percentage Based</label>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <input type="number" class="form-control" id="bill_discount_amount" value="0.00" placeholder="Discount Amount" style="height: 30px; font-size: 0.9rem;">
                                                    <span id="discountUnitLabel" style="font-size: 0.9rem;">LKR</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-end pe-2 flex-fill" style="max-width: 50%; min-height: 100px;">
                                            <p class="fs-5 mb-1">Total Before Discount: LKR <span id="totalBefore" class="fw-semibold text-dark">0.00</span></p>
                                            <p class="fs-5 mb-1">Total FOC: LKR <span id="totalFOC" class="fw-semibold text-dark">0.00</span></p>
                                            <h4 class="text-danger fw-bold mb-0 fs-4">Grand Total: LKR <span id="grandTotal" class="text-danger">0.00</span></h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="p-4 bg-white rounded-2 border border-opacity-25 shadow-sm">
                                        <h6 class="fw-bold text-center mb-4 border-bottom pb-3 text-muted fs-5 text-secondary">
                                            Stock Information
                                        </h6>

                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <span class="text-black fs-5">Old Quantity</span>
                                            <span class="fw-bold text-danger fs-3" id="old_stock_qty">0</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <span class="text-black fs-5">New Quantity</span>
                                            <span class="fw-bold text-success fs-3" id="new_stock_qty">0</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <span class="text-black fs-5">Last Cost</span>
                                            <span class="fw-bold text-primary fs-3" id="last_cost">0.00</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-black fs-5">Average Cost</span>
                                            <span class="fw-bold text-info fs-3" id="avg_cost">0.00</span>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="mb-3">
                                        <label for="note" class="form-label">Note</label>
                                        <textarea id="note" class="form-control" rows="4" placeholder="Optional notes"></textarea>
                                    </div>
                                    <br><br><br><br><br><br><br>
                                    <div class="d-flex justify-content-end mt-3" >
                                        <button type="submit" class="btn btn-dark" id="grn_finalize_btn">Finalize GRN</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="grnDetailModal" tabindex="-1" role="dialog" aria-labelledby="grnDetailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 90%; height: 90vh;">
        <div class="modal-content" style="height: 100%; overflow-y: auto;">
            <div class="modal-header">
                <h5 class="modal-title">GRN Details - <span id="modalGrnNumber">0000</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
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
                    <div class="col-md-9 mb-3">
                        <p class="mb-1 text-muted fw-semibold">Note</p>
                        <h6 id="modalNote" class="mb-0 text-dark fw-semibold">-</h6>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
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
                <br><br>

                <div class="text-end fs-5">
                    <p><strong>Total Before Discount:</strong> LKR <span id="modalTotalBefore" class="fw-semibold text-dark">0.00</span></p>
                    <p><strong>Total Discount:</strong> (<span id="modalTotalDiscount" class="fw-semibold text-primary">0.00</span>)</p>
                    <p><strong>Total FOC:</strong> LKR <span id="modalTotalFOC" class="fw-semibold text-dark">0.00</span></p>
                    <h4 class="fw-bold text-danger">Grand Total: LKR <span id="modalGrandTotal">0.00</span></h4>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button class="btn btn-outline-dark btn-lg me-auto d-flex align-items-center gap-2 shadow-sm"
                        onclick="printGrnDetails()">
                    <i class="bi bi-printer fs-5"></i> Print GRN
                </button>

                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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
                            <input type="text" id="item_supplier_name" class="form-control common_input" placeholder="Search supplier..." autocomplete="off" required>
                            <input type="hidden" id="item_supplier_id">
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

                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-primary" id="saveItemBtn" onclick="saveItem()">Save</button>
                    </div>
                </form>
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

    function updateNewStockQty() {
        const qty = parseFloat($('#qty').val()) || 0;
        const foc = parseFloat($('#foc').val()) || 0;
        const total = qty + foc;

        $('#new_stock_qty')
            .text(total)
            .addClass('highlight-change');

        setTimeout(() => {
            $('#new_stock_qty').removeClass('highlight-change');
        }, 300);
    }

    $('#qty, #foc').on('input', updateNewStockQty);

    function viewGrnDetails(grnId) {
        $.ajax({
            url: `/api/grn-details/${grnId}`,
            method: 'GET',
            success: function (res) {
                const grn = res.grn;
                const items = grn.items || [];

                const displayNo = grn.grn_number || grn.token_no || 'Draft';
                $('#modalGrnNumber').text(displayNo);
                $('#modalGrnNumberDisplay').text(displayNo);
                $('#modalGrnDate').text(grn.grn_date);
                $('#modalNote').text(grn.note || '-');
                $('#modalSupplierAndInvoice').text(`${grn.supplier_name ?? 'N/A'} - ${grn.supplier_invoice_number}`);
                $('#modalDiscountType').text(grn.grn_type);

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
                            <td class="text-start">${item.item_name ?? 'N/A'}</td>
                            <td>${qty}</td>
                            <td>${foc}</td>
                            <td class="text-end">${price.toFixed(2)}</td>
                            <td>${margin}</td>
                            <td>${discount}</td>
                            <td class="text-end">${finalPrice.toFixed(2)}</td>
                            <td class="text-end">${subtotal.toFixed(2)}</td>
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

    function initTokenSelect2() {
        if ($('#token_no').data('select2')) $('#token_no').select2('destroy');
        $('#token_no')
            .select2({
                dropdownParent: $('#grnModal'),
                width: '100%',
                placeholder: 'New GRN',
                minimumResultsForSearch: 0
            })
            .on('select2:select', function () {
                $('#grn_date').focus();
            })
            .on('select2:close', function () {
                if ($('#grnModal').hasClass('show')) $('#grn_date').focus();
            });
    }


    function loadDraftTokens(selectedId = null) {
        $.get('/api/grn-list-dropdown', function(data) {
            const $sel = $('#token_no');
            $sel.empty();
            $sel.append(`<option value="__new__">+ New GRN</option>`);
            data.forEach(item => {
                $sel.append(`<option value="${item.value}" data-token="${item.label}">${item.label}</option>`);
            });
            if (selectedId) $sel.val(selectedId);
            initTokenSelect2();
        });
    }

    function openAddGRNModal() {
        $('#grnForm')[0].reset();
        $('#supplier_id').val('');
        $('#item_id').val('');
        grnItems = [];
        discountConfig = { amount: 0, isPercentage: false };
        editingIndex = null;
        renderGrnItems();
        calculateGrnTotals();
        loadDraftTokens();
        switchTab(1);
    }

    $('#grnModal').on('shown.bs.modal', function () {
        itemsList = [];
        GrnDetails = {};
        $('#grn_id').val('');
        $('#supplier_id').val('');
        $('#supplier_name').val('');
        $('#supplier_invoice_number').val('');
        $('#supplier_ledger_code').val('');
        $('#note').val('');
        $('#bill_discount_amount').val('0.00');
        $('#is_percentage').prop('checked', false);
        renderGrnItems();
        calculateGrnTotals();
        handleGrnTypeToggle();
        $('#btn_add_item').removeClass('d-none');
        $('#edit_action_group').addClass('d-none');
        $('#grn_date').focus();
    });

    $('#token_no').on('change', function () {
        const val = $(this).val();
        if (val === '__new__') {
            $('#grn_id').val('');
            itemsList = [];
            renderGrnItems();
            calculateGrnTotals();
            $("#supplier_id").val('');
            $("#supplier_name").val('');
            $("#supplier_invoice_number").val('');
            $("#supplier_ledger_code").val('');
            $("#note").val('');
            $('#is_percentage').prop('checked', false);
            $('#bill_discount_amount').val('0.00');
        } else {
            fetchGrnDetails(val);
        }
    });

    let itemsList = [];
    let GrnDetails = [];

    function addItem() {

        const grnID = $("#grn_id").val();
        const grnDate = $("#grn_date").val();
        const supplierID = $("#supplier_id").val();
        const supplierInvoiceNumber = $("#supplier_invoice_number").val();
        const grnType = $("input[name='grn_type']:checked").val();

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
        if (!itemID) {
            alert("Please select a valid item.");
            return false;
        }

        const itemDescription = $("#item_name").val();
        const qty = parseInt($("#qty").val()) || 0;
        const foc = parseInt($("#foc").val()) || 0;
        const price = parseFloat($("#price").val()) || 0;
        const margin = parseInt($("#margin").val()) || 0;
        const discount = parseInt($("#discount").val()) || 0;

        let finalPrice = 0;
        if (grnType === 'Profit Margin') {
            finalPrice = price + ((price * margin) / 100);
        } else {
            finalPrice = price - ((price * discount) / 100);
        }

        const subtotal = finalPrice * qty;
        const isDuplicate = itemsList.some(item => item.item_id === itemID);
        if (isDuplicate) {
            alert("Duplicate Item, Item already exists in the GRN list.");
            return false;
        }
        if (qty === 0) {
            alert("Please enter a quantity");
            return false;
        }
        if (price === 0) {
            alert("Please enter a purchase price");
            return false;
        }

        itemsList.push({
            grn_id: grnID,
            item_id: itemID,
            item_name: itemDescription,
            qty: qty,
            foc: foc,
            price: price,
            margin: grnType === 'Profit Margin' ? margin : 0,
            discount: grnType === 'Discount Based' ? discount : 0,
            final_price: finalPrice,
            subtotal: subtotal
        });

        const selectedTokenVal  = $("#token_no").val();
        const selectedTokenText = $("#token_no option:selected").data('token') || null;

        GrnDetails = {
            id: grnID,
            token_no: selectedTokenVal === '__new__' ? null : selectedTokenText,
            grn_number: null,
            grn_date: $("#grn_date").val(),
            supplier_id: supplierID,
            supplier_invoice_number: $("#supplier_invoice_number").val(),
            supplier_ledger_code: $("#supplier_ledger_code").val(),
            grn_type: grnType,
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

                $("#item_id").val('');
                $("#item_name").val('');
                $("#qty").val('');
                $("#foc").val('');
                $("#price").val('');
                $("#margin").val('');
                $("#discount").val('');
                $("#discount_amount").val('');
                $("#old_stock_qty").text('0');
                $("#new_stock_qty").text('0');
                $("#last_cost").text('0.00');
                $("#avg_cost").text('0.00');
                $("#grn_id").val(grnDataRes.id);
                loadDraftTokens(grnDataRes.id);
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
                            value: item.label,
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
            $("#supplier_id").val(ui.item.id);
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

    $("#item_name").autocomplete({
        source: function (request, response) {
            if (request.term.length < 1) return;

            $.ajax({
                url: '/api/items-list',
                dataType: 'json',
                data: { search_key: request.term },
                success: function (data) {
                    const mappedItems = $.map(data, function (item) {
                        return {
                            label: item.label,
                            value: item.label,
                            id: item.value,
                            list_price: item.list_price
                        };
                    });

                    response(mappedItems);

                    if (mappedItems.length === 1) {
                        const item = mappedItems[0];
                        selectItem(item);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Autocomplete AJAX Error:', error);
                }
            });
        },
        minLength: 1,
        appendTo: "#grnModal",
        select: function (event, ui) {
            selectItem(ui.item);
            return false;
        }
    });

    function selectItem(item) {
        if (!item || !item.id) return;

        $("#item_name").val(item.label);
        $("#item_id").val(item.id);
        $("#price").val(item.list_price);
        $("#qty").val(1);
        $("#foc").val(0);
        $("#item_name").data("valid", true);

        loadAvailableStock(item.id);
        loadItemCostDetails(item.id);
        updateNewStockQty();

        $('#new_stock_qty')
            .text(1)
            .addClass('highlight-change');

        setTimeout(() => {
            $('#new_stock_qty').removeClass('highlight-change');
        }, 300);
    }

    function loadItemCostDetails(itemId) {
        if (!itemId) return;

        $.get('/api/item-cost-details', { item_id: itemId }, function (data) {
            const lastCost = parseFloat(data.last_cost ?? 0).toFixed(2);
            const avgCost = parseFloat(data.avg_cost ?? 0).toFixed(2);

            $("#last_cost").text(lastCost);
            $("#avg_cost").text(avgCost);
        }).fail(function (xhr, status, error) {
            console.error('Failed to load item cost details:', error);
            $("#last_cost").text('0.00');
            $("#avg_cost").text('0.00');
        });
    }

    function loadAvailableStock(itemId = null) {
        const item_id = itemId || $("#item_id").val();
        const store = '1-2-6-1000';

        if (!item_id) {
            console.warn('No item_id provided to loadAvailableStock.');
            return;
        }

        $.get('/api/available-stock', { item_id, store }, function (data) {
            const available = data.available_stock ?? 0;

            $("#old_stock_qty").text(available).addClass('highlight-change');

            setTimeout(() => {
                $('#old_stock_qty').removeClass('highlight-change');
            }, 300);
        }).fail(function (xhr, status, error) {
            console.error('Stock load error:', error);
            $("#old_stock_qty").text('0');
        });
    }

    $("#item_name").on("input", function () {
        if (!itemSelectedFromAutocomplete) {
            $("#foc").val('');
            $("#qty").val('');
            $("#price").val('');
            $("#margin").val('');
            $("#discount").val('');
            $("#discount_amount").val('');
            $("#old_stock_qty").text('0');
            $("#new_stock_qty").text('0');
            $("#last_cost").text('0.00');
            $("#avg_cost").text('0.00');
        }
        itemSelectedFromAutocomplete = false;
    });

    function filterGrnTable() {
        const filterValue = $('#grnTableFilter').val().toLowerCase();
        $('#itemTable tr').each(function () {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.includes(filterValue));
        });
    }

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
                <td class="text-start">${item.item_name}</td>
                <td class="text-end">${item.qty}</td>
                <td class="text-end">${item.foc}</td>
                <td class="text-end">${parseFloat(item.price).toFixed(2)}</td>
                <td class="text-end">${item.margin !== null ? parseInt(item.margin) : '-'}</td>
                <td class="text-end">${item.discount !== null ? parseInt(item.discount) : '-'}</td>
                <td class="text-end">${parseFloat(item.final_price).toFixed(2)}</td>
                <td class="text-end">${parseFloat(item.subtotal).toFixed(2)}</td>
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

        const base = item.price * item.qty;
        const discAmt = (base * item.discount) / 100;
        $('#discount_amount').val(discAmt.toFixed(2));

        $('#btn_add_item').addClass('d-none');
        $('#edit_action_group').removeClass('d-none');

        handleGrnTypeToggle();
    }

    function updateItem() {
        const itemDBId = $('#grn_item_id').val();

        if (!itemDBId) {
            Swal.fire('Error', 'Missing item ID for update', 'error');
            return;
        }

        const item = {
            item_name: $('#item_name').val(),
            qty: parseInt($('#qty').val()) || 0,
            foc: parseInt($('#foc').val()) || 0,
            price: parseFloat($('#price').val()) || 0,
            margin: parseInt($('#margin').val()) || 0,
            discount: parseInt($('#discount').val()) || 0
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
                itemsList.splice(index, 1);
                renderGrnItems();
                calculateGrnTotals();
            }
        });
    }

    function applyDiscount() {
        const val = parseFloat($('#bill_discount_amount').val());
        if (isNaN(val)) return Swal.fire('Error', 'Invalid discount value', 'error');

        discountConfig = {
            amount: val,
            isPercentage: $('#is_percentage').is(':checked')
        };
        calculateGrnTotals();
    }

    function clearDiscount() {
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

        switchTab(step);

        if (step === 2) {
            setTimeout(() => $('#item_name').focus(), 150);
        }

    }

    function validateStep1() {
        let valid = true;
        ['grn_date', 'supplier_id', 'supplier_invoice_number'].forEach(id => {
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
        handleGrnTypeToggle();

        $('input[name="grn_type"]').on('change', function () {
            handleGrnTypeToggle();
        });

        $('#bill_discount_amount').on('blur', function () {
            discountJustApplied = false;
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

    let isDiscountVisible = true;

    function toggleDiscountSection() {
        const content = document.getElementById("discountSectionContent");
        const toggleBtn = document.getElementById("discountToggleBtn");

        if (isDiscountVisible) {
            content.style.display = "none";
            toggleBtn.textContent = "+";
        } else {
            content.style.display = "block";
            toggleBtn.textContent = "−";
        }

        isDiscountVisible = !isDiscountVisible;
    }

    $('#discount').on('input', function() {
        const pct   = parseFloat($(this).val()) || 0;
        const qty   = parseFloat($('#qty').val()) || 0;
        const price = parseFloat($('#price').val()) || 0;
        const base  = price * qty;

        const amt = (base * pct) / 100;
        $('#discount_amount').val(amt.toFixed(2));

        calculateGrnTotals();
    });

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

    function fetchGrnDetails(grnId) {
        $.ajax({
            url: `/api/grn-details/${grnId}`,
            method: 'GET',
            success: function (res) {
                GrnDetails = res.grn;

                $("#grn_id").val(res.grn.id);
                $("#token_no").val(res.grn.id);
                $("#grn_date").val(res.grn.grn_date);
                $("#supplier_id").val(res.grn.supplier_id);
                $("#supplier_name").val(res.grn.supplier_name);
                $("#supplier_invoice_number").val(res.grn.supplier_invoice_number);
                $("#supplier_ledger_code").val(res.grn.supplier_ledger_code);
                $("#note").val(res.grn.note);
                $("#bill_discount_amount").val(res.grn.discount_amount);
                $('#is_percentage').prop('checked', res.grn.is_percentage === 1);

                if (res.grn.grn_type === 'Discount Based') {
                    $('#type_discount').prop('checked', true);
                } else {
                    $('#type_margin').prop('checked', true);
                }

                handleGrnTypeToggle();

                itemsList = GrnDetails.items;
                renderGrnItems();
                calculateGrnTotals();
            },
            error: function () {
                Swal.fire('Error', 'Failed to load GRN details.', 'error');
            }
        });
    }

    let discountJustApplied = false;

    $(document).on('keydown', 'input, select, textarea, button', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            const focusables = $('input, select, textarea, button')
                .filter(':visible:not([readonly]):not([disabled])')
                .sort((a, b) => $(a).offset().top - $(b).offset().top || $(a).offset().left - $(b).offset().left);

            const index = focusables.index(this);
            const id = $(this).attr('id');
            const $currentTab = $('.tab-pane.show.active');

            if ($(this).is(':radio') && id?.startsWith('type_')) {
                $('#grnStep1 button:contains("NEXT")').click();
                return;
            }

            if (id === 'grn_date') {
                $('#supplier_name').focus();
                return;
            }

            if (id === 'grn_step1_next' || ($(this).text().trim() === 'NEXT' && $('#grnStep1').is(':visible'))) {
                $('#grnStep1 button:contains("NEXT")').click();
                return;
            }

            if ($('#grnStep2').is(':visible')) {
                const grnType = $('input[name="grn_type"]:checked').val();
                const itemID = $('#item_id').val();
                const qty = $('#qty').val();
                const price = $('#price').val();
                const margin = $('#margin').val();
                const discount = $('#discount').val();

                const requiredFilled = itemID && qty && price &&
                    (grnType === 'Profit Margin' || grnType === 'Discount Based');

                const isOnLastField = grnType === 'Profit Margin'
                    ? id === 'margin'
                    : id === 'discount';

                if (requiredFilled && isOnLastField) {
                    if ($('#edit_action_group').is(':visible')) {
                        updateItem();
                    } else {
                        addItem();
                    }
                    return;
                }
            }

            if (id === 'bill_discount_amount') {
                const discountVal = $('#bill_discount_amount').val();
                if (discountVal !== '') {
                    if (!discountJustApplied) {
                        applyDiscount();
                        discountJustApplied = true;
                    } else {
                        discountJustApplied = false;
                        $('#note').focus();
                    }
                }
                return;
            }

            if (id === 'grn_step2_next' || ($(this).text().trim() === 'NEXT' && $('#grnStep2').is(':visible'))) {
                $('#grnStep2 button:contains("NEXT")').click();
                return;
            }

            if (id === 'note') {
                $('#grnForm').submit();
                return;
            }

            if (index > -1 && index + 1 < focusables.length) {
                focusables.eq(index + 1).focus();
            }
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && Swal.isVisible()) {
            const confirmButton = Swal.getConfirmButton();
            if (confirmButton) {
                confirmButton.click();
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

                    if (data.length === 1) {
                        $("#filter_supplier_name").val(data[0].label);
                        $("#filter_supplier_id").val(data[0].value);
                        filterSupplierSelected = true;
                    }
                }
            });
        },
        minLength: 1,
        appendTo: "#grnFilters",
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

    function printGrnDetails() {
        const modalBody = document.querySelector('#grnDetailModal .modal-body');
        const clonedContent = modalBody.cloneNode(true);

        const summaryRow = clonedContent.querySelector('.row.mb-4');
        if (summaryRow) {
            summaryRow.remove();
        }

        const tableWrapper = clonedContent.querySelector('.table-responsive');
        if (tableWrapper) {
            tableWrapper.style.maxHeight = 'unset';
            tableWrapper.style.overflow = 'unset';
        }

        const grnNumber = document.getElementById('modalGrnNumberDisplay').innerText.trim();
        const grnDate = document.getElementById('modalGrnDate').innerText.trim();
        const supplier = document.getElementById('modalSupplierAndInvoice').innerText.trim();
        const discountType = document.getElementById('modalDiscountType').innerText.trim();
        const note = document.getElementById('modalNote').innerText.trim();

        const detailSection = `
        <div class="grn-summary">
            <div class="grn-line">GRN Number: ${grnNumber}</div>
            <div class="grn-line">Date: ${grnDate}</div>
            <div class="grn-line">Supplier & Invoice: ${supplier}</div>
            <div class="grn-line">Discount Type: ${discountType}</div>
            <div class="grn-line">Note: ${note}</div>
        </div>
    `;

        const styles = `
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap');
            body {
                font-family: 'Roboto', 'Segoe UI', Arial, sans-serif;
                padding: 24px;
                font-size: 14px;
                color: #333;
            }
            h4 {
                font-size: 20px;
                margin-bottom: 20px;
            }
            .grn-summary {
                margin-bottom: 24px;
                line-height: 1.7;
                font-size: 15px;
            }
            .grn-line {
                margin-bottom: 6px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 16px;
                font-size: 12px;
            }
            th, td {
                border: 1px solid #ccc;
                padding: 6px 5px;
                text-align: center;
                vertical-align: middle;
            }
            th {
                background-color: #f8f9fa;
            }
            .text-end { text-align: right; }
            .text-primary { color: #0d6efd; }
            .text-danger { color: #dc3545; }
            .text-dark { color: #000 !important; }

            td:nth-child(1) { text-align: left; }
            td:nth-child(4),
            td:nth-child(7),
            td:nth-child(8) {
                text-align: right;
            }
        </style>
    `;

        const grnTitle = document.getElementById('modalGrnNumber').innerText.trim();

        const printWindow = window.open('', '', 'width=1200,height=1000');
        printWindow.document.write(`
        <html>
        <head>
            <title>GRN Print - ${grnTitle}</title>
            ${styles}
        </head>
        <body>
            <h4>GRN Details - ${grnTitle}</h4>
            ${detailSection}
            ${clonedContent.innerHTML}
        </body>
        </html>
    `);

        printWindow.document.close();
        printWindow.focus();

        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    }

    document.addEventListener('keydown', function(event) {
        if ((event.key === '+' || event.code === 'NumpadAdd') && !event.target.matches('input, textarea')) {
            event.preventDefault();
            $('#itemModal').on('shown.bs.modal', function () {
                $(this).find('form')[0].reset();
                $(this).find('select').val('').trigger('change');
                $(this).find('input[type=checkbox]').prop('checked');
            });
            const itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
            itemModal.show();
        }
    });

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

    $("#item_supplier_name").autocomplete({
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
                            value: item.label,
                            id: item.value
                        };
                    }));

                    if (data.length === 1) {
                        $("#item_supplier_name").val(data[0].label);
                        $("#item_supplier_id").val(data[0].value);
                    }
                }
            });
        },
        minLength: 1,
        appendTo: "#itemModal",
        select: function (event, ui) {
            $("#item_supplier_name").val(ui.item.label);
            $("#item_supplier_id").val(ui.item.id);
            return false;
        }
    });

    let isSaving = false;

    function saveItem() {
        if (isSaving) return;
        isSaving = true;

        const itemData = {
            code: $('#item_code').val(),
            description: $('#description').val(),
            rack_location: $('#rack_location').val(),
            supplier_id: $('#item_supplier_id').val(),
            category_id: $('#category_id').val(),
            sub_category_id: $('#sub_category_id').val(),
            measure_unit: $('#measure_unit').val(),
            is_active: $('#is_active').is(':checked') ? 1 : 0,
            list_price: $('#list_price').val(),
            retail_price: $('#retail_price').val(),
            wholesale_price: $('#wholesale_price').val()
        };

        $.ajax({
            url: '/api/items',
            method: 'POST',
            data: itemData,
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Item Saved',
                    text: response.message || 'Item saved successfully!',
                });
                $('#itemModal').modal('hide');
                isSaving = false;
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: Object.values(xhr.responseJSON.errors).map(e => `<div>${e}</div>`).join(''),
                });
                isSaving = false;
            }
        });
    }

    $(document).on('keydown', 'input, select, textarea', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const form = $(this).closest('form');
            const focusables = form.find('input, select, textarea, button')
                .filter(':visible:not([readonly]):not([disabled])');

            const index = focusables.index(this);
            const next = focusables.eq(index + 1);

            if (index > -1 && index + 1 < focusables.length) {
                if (next.is('button') && next.attr('id') === 'saveItemBtn') {
                    next.click();
                } else {
                    next.focus();
                }
            }
        }
    });

    $('#type_discount').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            goToStep(2);
        }
    });

    $('#wholesale_price').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveItem();
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const fields = ['qty', 'foc', 'price', 'margin', 'discount'];
        fields.forEach(id => {
            const input = document.getElementById(id);
            if (!input) return;
            input.addEventListener('input', function () {
                let value = parseFloat(this.value);
                if (value < 0) {
                    this.value = '';
                }
                if ((this.id === 'qty' || this.id === 'price') && (value === 0 || isNaN(value))) {
                    this.value = '';
                }
                if (this.id === 'discount') {
                    if (value >= 100) {
                        this.value = '';
                        document.getElementById('discount_amount').value = '';
                    }
                }
            });
            input.addEventListener('keydown', function (e) {
                if (e.key === '-' || e.keyCode === 189) {
                    e.preventDefault();
                }
            });
        });
    });

</script>

<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
