@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Employee Stock Issue'])

<style>
    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 50%;
        }
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
                                <h3 class="m-0"></h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_body">
                                <div class="QA_section">
                                    <div class="white_box_tittle list_header">
                                        <div class="box_left d-flex lms_block">
                                            <div class="add_button ms-2">
                                                <button type="button" class="btn btn-primary issueStockSection" onclick="displayTable('availableStockSection')" style="display: none">
                                                    Stock Details
                                                </button>
                                                <button type="button" class="btn btn-primary availableStockSection" onclick="displayTable('issueStockSection')">
                                                    Issue Details
                                                </button>
                                            </div>
                                        </div>
                                        <div class="box_right d-flex lms_block">
                                            <div class="serach_field_2">
                                                <div class="search_inner">
                                                    <form Active="#">
                                                        <div class="search_field">
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="employeeStockTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" onclick="showModal()" data-bs-target="#stockIssueModal">
                                                    Issue Stock
                                                </button>
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" onclick="showConsumptionModal()" data-bs-target="#consumptionModal">
                                                    Consumption
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30 issueStockSection" style="display: none;" id="issueStockSection">
                                        <table class="table lms_table_active">
                                            <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Store</th>
                                                <th scope="col">Employee</th>
                                                <th scope="col">Issue Date</th>
                                                <th scope="col">Issue Details</th>
                                            </tr>
                                            </thead>
                                            <tbody id="employeeStockTable">

                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="QA_table mb_30 availableStockSection" id="availableStockSection">
                                        <table class="table lms_table_active">
                                            <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Employee ID</th>
                                                <th scope="col">Employee Name</th>
                                                <th scope="col">Item</th>
                                                <th scope="col">Available Stock</th>
                                            </tr>
                                            </thead>
                                            <tbody id="availableStockTable">

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

            <div class="modal fade" id="stockIssueModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Issue Stock To Employee</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="white_card card_height_100 mb_30">
                                        <div class="white_card_body" style="padding-bottom: 5px;">
                                            <div>
                                                <form>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="cbo_store">Store <code>*</code></label>
                                                            <input type="text" class="form-control" id="cbo_store" name="store" placeholder="Select store..." tabindex="1">
                                                            <input type="hidden" id="store_id">
                                                            <input type="hidden" id="store_ledger_code">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="cbo_employee">Employee <code>*</code></label>
                                                            <input type="text" class="form-control" id="cbo_employee" name="employee" placeholder="Select employee..." tabindex="1">
                                                            <input type="hidden" id="employee_id">
                                                            <input type="hidden" id="employee_ledger_code">
                                                        </div>
                                                    </div>
                                                </form>
                                                <form>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="cbo_item">Item <code>*</code></label>
                                                            <input type="text" class="form-control" id="cbo_item" name="item" placeholder="Select item..." tabindex="3" >
                                                            <input type="hidden" id="item_id">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label" for="txt_qty">Qty <code>*</code></label>
                                                            <input type="number" class="form-control" id="txt_qty" name="qty" tabindex="4" min=1>
                                                            <span class="text-muted small available_stock_display"></span>
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-start pt-4">
                                                            <button type="button" class="btn btn-secondary btn_add" id="btn_add"> <i class="fas fa-plus"></i></button>&nbsp;&nbsp;
                                                            <button type="button" class="btn btn-secondary" onclick="itemRefresh()"> <i class="fas fa-sync-alt"></i></button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="white_card card_height_100 mb_30">

                                        <div class="white_card_body border p-3 rounded mb-3">
                                            <div class="QA_section">
                                                <div class="white_box_tittle list_header">
                                                    <h4>Issue Items</h4>
                                                </div>
                                                <hr>
                                                <div class="QA_table mb_30">
                                                    <table class="table table-striped" id="itemTbl">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">Item Code</th>
                                                                <th scope="col">Item Description</th>
                                                                <th scope="col">Qty</th>
                                                                <th scope="col">Delete</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <td colspan="3" class="text-end fw-bold">Total Quantity</td>
                                                            <td class="item-total text-end fw-bold">0</td>
                                                            <td></td>
                                                        </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="saveEmployeeStockIssue('itemTbl','stockIssueModal')">Issue</button>
                            <button type="button" class="btn btn-primary" onclick="closeModal('stockIssueModal')">Close</button>
                        </div>
                    </div>
                </div>
            </div>

<div class="modal fade" id="consumptionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Employee Consumption</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_body" style="padding-bottom: 5px;">
                                <div>
                                    <form>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="cbo_consumer">Employee <code>*</code></label>
                                                <input type="text" class="form-control" id="cbo_consumer" name="consumer" placeholder="Select employee..." tabindex="1">
                                                <input type="hidden" id="consumer_id">
                                                <input type="hidden" id="consumer_ledger_code">
                                            </div>
                                        </div>
                                    </form>
                                    <form>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="cbo_item_consume">Item <code>*</code></label>
                                                <input type="text" class="form-control" id="cbo_item_consume" name="item" placeholder="Select item..." tabindex="3" >
                                                <input type="hidden" id="consume_item_id">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="txt_consume_qty">Qty <code>*</code></label>
                                                <input type="number" class="form-control" id="txt_consume_qty" name="qty" tabindex="4" min=1>
                                                <span class="text-muted small available_stock_display"></span>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-start pt-4">
                                                <button type="button" class="btn btn-secondary btn_add" id="btn_add"> <i class="fas fa-plus"></i></button>&nbsp;&nbsp;
                                                <button type="button" class="btn btn-secondary" onclick="itemRefresh()"> <i class="fas fa-sync-alt"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">

                            <div class="white_card_body border p-3 rounded mb-3">
                                <div class="QA_section">
                                    <div class="white_box_tittle list_header">
                                        <h4>Issue Items</h4>
                                    </div>
                                    <hr>
                                    <div class="QA_table mb_30">
                                        <table class="table table-striped" id="consumeItemTbl">
                                            <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Item Code</th>
                                                <th scope="col">Item Description</th>
                                                <th scope="col">Qty</th>
                                                <th scope="col">Delete</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end fw-bold">Total Quantity</td>
                                                <td class="item-total text-end fw-bold">0</td>
                                                <td></td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveEmployeeStockIssue('consumeItemTbl','consumptionModal')">Consume</button>
                <button type="button" class="btn btn-primary" onclick="closeModal('consumptionModal')">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="stockIssueDetailsModal" tabindex="-1" role="dialog" aria-labelledby="stockIssueDetailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Issue Details</h5>
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
                            <th scope="col">Qty</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total Quantity</td>
                            <td id="totalQuantity" class="text-end fw-bold">0.00</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="closeModal('stockIssueDetailsModal')">Close</button>
            </div>
        </div>
    </div>
</div>

            <script>
                var itemSelectedFromAutocomplete = false;
                var consumeItemSelectedFromAutocomplete = false;
                const apiUrl = '/api/employee-stock';
                loadStockIssueDetails();

                function loadStockIssueDetails() {
                    $.get(apiUrl, function(data) {
                        const issues = data.issues;
                        const stockBalances = data.stock_balances;
                        let issueTable = $('#issueStockSection table').DataTable();
                        let employeeStockTable = $('#availableStockSection table').DataTable();
                        issueTable.clear();
                        employeeStockTable.clear();

                        let issueTableRowID = 1;
                        issues.forEach(issueDetails => {
                            issueTable.row.add([
                                issueTableRowID,
                                issueDetails.issued_store,
                                issueDetails.employee_name,
                                issueDetails.created_at,
                                `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#stockIssueDetailsModal" onclick="viewIssueDetails('${issueDetails.reference_id}')">View</button>`
                            ]);
                            issueTableRowID++;
                        });

                        let stockTableRowID = 1;
                        stockBalances.forEach(stockDetails => {
                            employeeStockTable.row.add([
                                stockTableRowID,
                                stockDetails.employee_code,
                                stockDetails.employee_name,
                                stockDetails.item_description,
                                stockDetails.current_stock,
                                `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#stockIssueDetailsModal" onclick="viewIssueDetails('${stockDetails.employee_code}')">View</button>`
                            ]);
                            stockTableRowID++;
                        });

                        issueTable.draw();
                        employeeStockTable.draw();
                    });
                }

                let itemsList = [];
                let issueNote = [];

                $(".btn_add").click(function () {

                    let employee_ledger_code = '';
                    let store_ledger_code = '';
                    let itemID = '';
                    let qty = '';
                    let itemDescription = '';
                    let itemCode = '';
                    let tableID = '';

                    if ($('#stockIssueModal').hasClass('show')) {
                        employee_ledger_code = $("#employee_ledger_code").val();
                        store_ledger_code = $("#store_ledger_code").val();
                        itemID = $("#item_id").val();

                        if (store_ledger_code == '') {
                            alert("Please enter 'Main Store'!");
                            return false;
                        }

                        qty = $("#txt_qty").val();

                        let description = ($("#cbo_item").val()).split(" - ");
                        itemDescription = description[1];
                        itemCode = description[0];
                        tableID = '#itemTbl';
                    }else{
                        employee_ledger_code = $("#consumer_ledger_code").val();
                        store_ledger_code = "";
                        itemID = $("#consume_item_id").val();
                        qty = $("#txt_consume_qty").val();

                        let description = ($("#cbo_item_consume").val()).split(" - ");
                        itemDescription = description[1];
                        itemCode = description[0];
                        tableID = '#consumeItemTbl';
                    }

                    if (employee_ledger_code === '') {
                        alert("Please enter 'Employee'!");
                        return false;
                    }

                    if (itemID === '') {
                        alert("Please enter 'Item'!");
                        return false;
                    }

                    if (qty === '' || qty === 0) {
                        alert("Please enter 'Quantity'!");
                        return false;
                    }

                    const isDuplicate = itemsList.some(item => item.item_id === itemID);
                    if (isDuplicate) {
                        alert("Item already exists.");
                        return false;
                    }

                    itemsList.push({
                        item_id: itemID,
                        item_description: itemDescription,
                        item_code: itemCode,
                        quantity: qty
                    });

                    issueNote = {
                        employee_ledger_code: employee_ledger_code,
                        store_ledger_code: store_ledger_code,
                        items: itemsList
                    };

                    renderItemsTable(tableID);

                    $("#txt_qty").val("");
                    $("#cbo_item").val("");
                    $("#item_id").val("");
                    $("#txt_consume_qty").val("");
                    $("#cbo_item_consume").val("");
                    $("#consume_item_id").val("");
                    $(".available_stock_display").hide();
                    $(".available_stock_display").text(0);

                });

                function renderItemsTable(tableID) {
                    let tbody = $(tableID + " tbody");
                    console.log(tableID);
                    tbody.empty();
                    let itemTotal = 0;

                    itemsList.forEach((item, index) => {
                        itemTotal += parseInt(item.quantity);
                        let row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.item_code}</td>
                                <td>${item.item_description}</td>
                                <td>${item.quantity}</td>
                                <td><a href="javascript:void(0)" onclick="removeItem(${index},'${tableID}')" class="text-danger">Delete</a></td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                    // $(tableID).text(itemTotal);
                    $(tableID + " tfoot .item-total").text(itemTotal);
                }

                function removeItem(index,tableID) {
                    itemsList.splice(index, 1);
                    renderItemsTable(tableID);
                }


                $(function () {
                    $("#cbo_store").autocomplete({
                        source: function (request, response) {
                            if (request.term.length < 1) return;

                            $.ajax({
                                url: '/api/store-list',
                                dataType: 'json',
                                data: { search_key: request.term },
                                success: function (data) {
                                    response(data);
                                    if (data.length === 1) {
                                        $("#cbo_store").val(data[0].label);
                                        $("#store_id").val(data[0].value);
                                        $("#store_ledger_code").val(data[0].store_ledger_code);
                                        itemsList = [];
                                        renderItemsTable('#itemTbl');
                                        loadAvailableStock();
                                    }
                                }
                            });
                        },
                        minLength: 1,
                        appendTo: "#stockIssueModal",
                        focus: function (event, ui) {
                            $("#cbo_store").val(ui.item.label);
                            return false;
                        },
                        select: function (event, ui) {
                            $("#cbo_store").val(ui.item.label);
                            $("#store_id").val(ui.item.value);
                            $("#store_ledger_code").val(ui.item.store_ledger_code);
                            itemsList = [];
                            renderItemsTable('#itemTbl');
                            loadAvailableStock();
                            return false;
                        }
                    });

                    $("#cbo_employee").autocomplete({
                        source: function (request, response) {
                            if (request.term.length < 1) return;

                            $.ajax({
                                url: '/api/employees-list',
                                dataType: 'json',
                                data: { search_key: request.term },
                                success: function (data) {
                                    response(data);
                                    if (data.length === 1) {
                                        $("#cbo_employee").val(data[0].label);
                                        $("#employee_id").val(data[0].value);
                                        $("#employee_ledger_code").val(data[0].employee_ledger_code);
                                    }
                                }
                            });
                        },
                        minLength: 1,
                        appendTo: "#stockIssueModal",
                        focus: function (event, ui) {
                            $("#cbo_employee").val(ui.item.label);
                            return false;
                        },
                        select: function (event, ui) {
                            $("#cbo_employee").val(ui.item.label);
                            $("#employee_id").val(ui.item.value);
                            $("#employee_ledger_code").val(ui.item.employee_ledger_code);
                            return false;
                        }
                    });

                    $("#cbo_consumer").autocomplete({
                        source: function (request, response) {
                            if (request.term.length < 1) return;

                            $.ajax({
                                url: '/api/employees-list',
                                dataType: 'json',
                                data: { search_key: request.term },
                                success: function (data) {
                                    response(data);
                                    if (data.length === 1) {
                                        $("#cbo_consumer").val(data[0].label);
                                        $("#consumer_id").val(data[0].value);
                                        $("#consumer_ledger_code").val(data[0].employee_ledger_code);
                                        renderItemsTable('#consumeItemTotal');
                                        loadAvailableStock();
                                    }
                                }
                            });
                        },
                        minLength: 1,
                        appendTo: "#consumptionModal",
                        focus: function (event, ui) {
                            $("#cbo_consumer").val(ui.item.label);
                            return false;
                        },
                        select: function (event, ui) {
                            $("#cbo_consumer").val(ui.item.label);
                            $("#consumer_id").val(ui.item.value);
                            $("#consumer_ledger_code").val(ui.item.employee_ledger_code);
                            renderItemsTable('#consumeItemTotal');
                            loadAvailableStock();
                            return false;
                        }
                    });

                    $("#cbo_item").autocomplete({
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
                                            id: item.value
                                        };
                                    }));
                                    if (data.length === 1) {
                                        $("#cbo_item").val(data[0].label);
                                        $("#item_id").val(data[0].value);
                                        $("#txt_qty").val(1);
                                        loadAvailableStock();
                                        itemSelectedFromAutocomplete = true;
                                    }
                                }
                            });
                        },
                        minLength: 1,
                        appendTo: "#stockIssueModal",
                        focus: function (event, ui) {
                            $("#cbo_item").val(ui.item.label);
                            return false;
                        },
                        select: function (event, ui) {
                            $("#cbo_item").val(ui.item.label);
                            $("#item_id").val(ui.item.value);
                            $("#txt_qty").val(1);
                            loadAvailableStock();
                            itemSelectedFromAutocomplete = true;
                            return false;
                        }
                    });

                    $("#cbo_item_consume").autocomplete({
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
                                            id: item.value
                                        };
                                    }));
                                    if (data.length === 1) {
                                        $("#cbo_item_consume").val(data[0].label);
                                        $("#consume_item_id").val(data[0].value);
                                        $("#txt_consume_qty").val(1);
                                        loadAvailableStock();
                                        consumeItemSelectedFromAutocomplete = true;
                                    }
                                }
                            });
                        },
                        minLength: 1,
                        appendTo: "#consumptionModal",
                        focus: function (event, ui) {
                            $("#cbo_item_consume").val(ui.item.label);
                            return false;
                        },
                        select: function (event, ui) {
                            $("#cbo_item_consume").val(ui.item.label);
                            $("#consume_item_id").val(ui.item.value);
                            $("#txt_consume_qty").val(1);
                            loadAvailableStock();
                            consumeItemSelectedFromAutocomplete = true;
                            return false;
                        }
                    });
                });

                $("#cbo_item").on("input", function () {
                    if (!itemSelectedFromAutocomplete) {
                        $("#item_id").val('');
                        $("#txt_qty").val('');
                        $(".available_stock_display").hide();
                    }
                    itemSelectedFromAutocomplete = false;
                });

                $("#cbo_item_consume").on("input", function () {
                    if (!consumeItemSelectedFromAutocomplete) {
                        $("#consume_item_id").val('');
                        $("#txt_consume_qty").val('');
                        $(".available_stock_display").hide();
                    }
                    consumeItemSelectedFromAutocomplete = false;
                });

                $("#txt_qty").on("keyup", function () {
                    const max = parseFloat($(this).attr("max")) || Infinity;
                    const value = parseFloat($(this).val()) || 0;

                    if (value > max) {
                        $(this).val(max);
                        alert(`Maximum allowed quantity is ${max}`);
                    }
                });

                function loadAvailableStock(){
                    let item_id;
                    let store;

                    if ($('#stockIssueModal').hasClass('show')) {
                        store = $('#store_ledger_code').val();
                        item_id = $("#item_id").val();
                        if(store == '') {
                            alert("Please select the main store");
                            $("#txt_qty").val("");
                        }
                    } else {
                        store = $('#consumer_ledger_code').val();
                        item_id = $("#consume_item_id").val();
                        if(store == '') {
                            alert("Please select the employee");
                            $("#txt_qty").val("");
                        }
                    }

                    if(item_id !== '' && store !== '') {
                        $.get(`/api/available-stock`, {item_id: item_id, store: store}, function (data) {

                            const available = data.available_stock ?? 0;

                            if ($('#stockIssueModal').hasClass('show')) {
                                if (available < 1) {
                                    $("#txt_qty").val(0);
                                    $(".btn_add").prop("disabled", true);
                                    $(".available_stock_display").addClass("red_color");
                                    $(".available_stock_display").removeClass("text-muted");

                                } else {
                                    $(".btn_add").prop("disabled", false);
                                    $(".available_stock_display").addClass("text-muted");
                                    $(".available_stock_display").removeClass("red_color");
                                }
                                $("#txt_qty").attr("max", available);
                                $(".available_stock_display").show();
                                $(".available_stock_display").text(`Available stock: ${available}`);

                                if ($("#txt_qty").val() > available) {
                                    $("#txt_qty").val(available);
                                }
                            }else {
                                if (available < 1) {
                                    $("#txt_consume_qty").val(0);
                                    $(".btn_add").prop("disabled", true);
                                    $(".available_stock_display").addClass("red_color");
                                    $(".available_stock_display").removeClass("text-muted");

                                } else {
                                    $(".btn_add").prop("disabled", false);
                                    $(".available_stock_display").addClass("text-muted");
                                    $(".available_stock_display").removeClass("red_color");
                                }
                                $("#txt_consume_qty").attr("max", available);
                                $(".available_stock_display").show();
                                $(".available_stock_display").text(`Available stock: ${available}`);

                                if ($("#txt_consume_qty").val() > available) {
                                    $("#txt_consume_qty").val(available);
                                }
                            }

                        });
                    }
                }

                function closeModal(modalId) {
                    const modalElement = document.getElementById(modalId);
                    const modal = bootstrap.Modal.getInstance(modalElement);

                    if (modal) {
                        modal.hide();
                    }
                }

                function saveEmployeeStockIssue(tableID,modalID) {
                    $.ajax({
                        url: `/api/employee-stock-issue`,
                        method: 'POST',
                        data: issueNote,
                        success: function(response) {
                            itemRefresh();
                            itemsList = [];
                            renderItemsTable('#'+tableID);
                            if(modalID === 'stockIssueModal') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Issued Successfully!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }else{
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Consume Successfully!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                            closeModal(modalID);
                            loadStockIssueDetails();
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

                function showModal(){
                    itemRefresh();
                    $("#cbo_employee").val('');
                    $("#employee_id").val('');
                    $("#employee_ledger_code").val('');
                    $("#cbo_store").val('');
                    $("#store_id").val('');
                    $("#store_ledger_code").val('');
                    itemsList = [];
                    renderItemsTable('#itemTbl');
                }

                function showConsumptionModal(){
                    itemRefresh();
                    $("#cbo_consumer").val('');
                    $("#consumer_id").val('');
                    $("#consumer_ledger_code").val('');
                    itemsList = [];
                    renderItemsTable('#consumeItemTbl');
                }

                function viewIssueDetails(reference_id) {
                    $.get(`${apiUrl}/${reference_id}`, function(data) {
                        const tbody = $('#itemTable tbody');
                        tbody.empty();

                        let rowID = 1;
                        let totalQuantity = 0;

                        data.forEach(issueList => {
                            totalQuantity += parseInt(issueList.quantity);

                            const row = `
                                <tr>
                                    <td>${rowID}</td>
                                    <td>${issueList.item_code ?? ''}</td>
                                    <td>${issueList.item_description ?? ''}</td>
                                    <td>${issueList.quantity ?? 0}</td>
                                </tr>
                            `;
                            tbody.append(row);
                            rowID++;
                        });

                        $('#totalQuantity').text(totalQuantity);
                    });
                }

                function itemRefresh() {
                    $("#item_id").val('');
                    $("#txt_qty").val('');
                    $("#cbo_item").val('');
                    $(".available_stock_display").hide();
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
                        }
                    }
                });

                function displayTable(tableID){
                    if(tableID === 'issueStockSection'){
                        $(".issueStockSection").show();
                        $(".availableStockSection").hide();
                    }else{
                        $(".availableStockSection").show();
                        $(".issueStockSection").hide();
                    }
                }



            </script>
