@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Invoice'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-lg-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_body" style="padding-top: 5px; padding-bottom: 5px;">
                        <div class="card-body">
                            <form>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="cbo_employee">Salesman <code>*</code></label>
                                        <input type="text" class="form-control" id="cbo_employee" name="salesman" placeholder="Select salesman..." tabindex="1">
                                        <input type="hidden" id="employee_id">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="cbo_customer">Customer <code>*</code></label>
                                        <input type="text" class="form-control" id="cbo_customer" name="customer" placeholder="Select customer..." tabindex="2">
                                        <input type="hidden" id="customer_id">
                                    </div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="cbo_tokenNo">Token No. <code>*</code></label>
                                        <select class="form-select" id="cbo_tokenNo" onchange="fetchInvoiceDetails(this.value)" tabindex="-1"></select>
                                        <input type="hidden" id="invoice_id">
                                    </div>
                                    <div class="col-md-1">
                                        <a href="javascript:location.reload()" class="btn btn-secondary" style="float: right">
                                            <i class="fas fa-sync-alt"></i>
                                        </a>

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
                    <div class="white_card_body" style="padding-top: 5px; padding-bottom: 5px;">
                        <div class="card-body">
                            <form>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label" for="cbo_item">Item <code>*</code></label>
                                        <input type="text" class="form-control" id="cbo_item" name="item" placeholder="Select item..." tabindex="3" >
                                        <input type="hidden" id="item_id">
                                        <input type="hidden" id="txt_item_type">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="txt_price">Price <code>*</code></label>
                                        <input type="text" class="form-control text-end" id="txt_price" disabled>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label" for="txt_qty">Qty <code>*</code></label>
                                        <input type="number" class="form-control" id="txt_qty" name="qty" tabindex="4" min=1 onchange="calculateSubTotal('qty');">
                                        <span class="text-muted small available_stock_display"></span>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label" for="txt_discount">Discount</label>
                                        <input type="text" class="form-control" tabindex="5" id="txt_discount" onkeyup="calculateSubTotal('percentage')">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="txt_discount_amount">Dis. Amount</label>
                                        <input type="text" class="form-control text-end" id="txt_discount_amount" onkeyup="calculateSubTotal('amount')">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="txt_sub_total">Sub Total <code>*</code></label>
                                        <input type="text" class="form-control text-end" id="txt_sub_total" disabled tabindex="-1">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-secondary" id="btn_add"> <i class="fas fa-plus"></i></button>&nbsp;&nbsp;
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
            <div class="col-lg-9">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0"></h3>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">
                        <div class="QA_section">
                            <div class="white_box_tittle list_header">
                                <h4>Sales Items</h4>
                            </div>
                            <hr>
                            <div class="QA_table mb_30">
                                <table class="table" id="itemTbl">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Item</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Qty</th>
                                            <th scope="col">Dis. %</th>
                                            <th scope="col">Dis. Amount</th>
                                            <th scope="col">Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0"></h3>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">
                        <div class="QA_section">
                            <div class="white_box_tittle list_header">
                                <h4>Billing</h4>
                            </div>
                            <hr>
                            <div class="QA_table mb_30">
                                <table class="table table-clear QA_table" id="invoice_details" >
                                    <tbody>
                                        <tr>
                                            <td class="left" width="45%">
                                                <strong>Return Amount</strong>
                                            </td>
                                            <td class="right"><input type="text" class="form-control text-end" id="txt_return" value="0.00"></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>Total Amount</strong>
                                            </td>
                                            <td class="right"><input type="text" class="form-control text-end" id="txt_total" disabled tabindex="-1" value="0.00"></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>Bill Discount %</strong>
                                            </td>
                                            <td class="right"><input type="text" class="form-control text-end" id="txt_totdiscount" onkeyup="calculateGrandTotal();"  value="0"></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>Bill Dis. Amount</strong>
                                            </td>
                                            <td class="right" ><input type="text" class="form-control text-end" id="txt_totdiscount_amount" onkeyup="calculateGrandTotal();" value="0.00"></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>Grand Total</strong>
                                            </td>
                                            <td class="right">
                                                <strong><input type="text" class="form-control text-end" id="txt_grandtotal" disabled tabindex="-1"  value="0.00"></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>Received Cash</strong>
                                            </td>
                                            <td class="right">
                                                <strong><input type="text" class="form-control text-end" id="txt_received_cash" style="font-weight: 900;" onkeyup="getBalance()"></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>Balance</strong>
                                            </td>
                                            <td class="right">
                                                <strong><input type="text" class="form-control text-end" id="txt_balance" disabled tabindex="-1"  value="0.00"></strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="button" class="btn btn-primary" onclick="finishInvoice()">Finish Sale</button>
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

<script>
    var itemSelectedFromAutocomplete = false;
    const apiUrl = '/api/new-invoice';

    $(document).ready(function () {
        $(document).on('keydown', function (e) {
            if (e.key === '+' || (e.shiftKey && e.key === '=')) {
                e.preventDefault();
                $('#txt_received_cash').focus();
            }
        });
        loadTotkenNo('');
    });

    function loadTotkenNo(tokenID){
        $.ajax({
            url: '/api/invoice-list-dropdown',
            method: 'GET',
            success: function (data) {
                var select = $('#cbo_tokenNo');
                select.empty();
                select.append('<option value="">New Invoice</option>');

                data.forEach(function (item) {
                    select.append('<option value="' + item.value + '">' + item.label + '</option>');
                });
                $("#cbo_tokenNo").val(tokenID);
            },
            error: function (xhr, status, error) {
                console.error('Error loading dropdown:', error);
            }
        });

    }

    $(function () {

        $("#cbo_employee").autocomplete({
            source: function (request, response) {
                if (request.term.length < 1) return;

                $.ajax({
                    url: '/api/employees-list',
                    dataType: 'json',
                    data: { search_key : request.term },
                    success: function (data) {
                        response(data);
                        if (data.length === 1) {
                            $("#cbo_employee").val(data[0].label);
                            $("#employee_id").val(data[0].value);
                        }
                    }
                });
            },
            minLength: 1,
            focus: function (event, ui) {
                $("#cbo_employee").val(ui.item.label);
                return false;
            },
            select: function (event, ui) {
                $("#cbo_employee").val(ui.item.label);
                $("#employee_id").val(ui.item.value);
                return false;
            }
        });

        $("#cbo_customer").autocomplete({
            source: function (request, response) {
                if (request.term.length < 1) return;

                $.ajax({
                    url: '/api/customer-list',
                    dataType: 'json',
                    data: {
                        search_key: request.term
                    },
                    success: function (data) {
                        response(data);

                        if (data.length === 1) {
                            $("#cbo_customer").val(data[0].label);
                            $("#customer_id").val(data[0].value);
                        }
                    }
                });
            },
            minLength: 1,
            focus: function (event, ui) {
                $("#cbo_customer").val(ui.item.label);
                return false;
            },
            select: function (event, ui) {
                $("#cbo_customer").val(ui.item.label);
                $("#customer_id").val(ui.item.value);
                return false;
            }
        });

        $("#cbo_item").autocomplete({
            source: function (request, response) {
                if (request.term.length < 1) return;

                $(".available_stock_display").hide();
                $.ajax({
                    url: '/api/item-list',
                    dataType: 'json',
                    data: {
                        search_key: request.term
                    },
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.label,
                                value: item.value,
                                id: item.value,
                                price: item.price,
                                item_type: item.item_type
                            };
                        }));

                        if (data.length === 1) {
                            $("#cbo_item").val(data[0].label);
                            $("#item_id").val(data[0].value);
                            $("#txt_price").val(data[0].price);
                            $("#txt_item_type").val(data[0].item_type);
                            $("#txt_qty").val(1);
                            $("#txt_sub_total").val((data[0].price*1).toFixed(2));
                            itemSelectedFromAutocomplete = true;
                            if(data[0].item_type == 'item') {
                                loadAvailableStock();
                            }
                        }
                    }
                });
            },
            minLength: 1,
            focus: function (event, ui) {
                $("#cbo_item").val(ui.item.label);
                return false;
            },
            select: function (event, ui) {
                $("#cbo_item").val(ui.item.label);
                $("#item_id").val(ui.item.value);
                $("#txt_price").val(ui.item.price);
                $("#txt_item_type").val(ui.item.item_type);
                $("#txt_qty").val(1);
                $("#txt_sub_total").val((ui.item.price*1).toFixed(2));
                itemSelectedFromAutocomplete = true;
                if(ui.item.item_type == 'item') {
                    loadAvailableStock();
                }
                return false;
            }
        });

    });

    $("#cbo_item").on("input", function () {
        if (!itemSelectedFromAutocomplete) {
            $("#txt_price").val('');
            $("#txt_item_type").val('');
            $("#txt_qty").val('');
            $("#txt_sub_total").val('');
            $(".available_stock_display").hide();
        }
        itemSelectedFromAutocomplete = false;
    });

    function clearData() {
        $("#invoice_id").val('');
        $("#employee_id").val('');
        $("#cbo_employee").val('');

        $("#customer_id").val('');
        $("#cbo_customer").val('');

        $("#txt_totdiscount").val('');
        $("#txt_totdiscount_amount").val('');

        invoice = null;
        itemsList = [];
        renderItemsTable([]);
        itemRefresh();
    }

    function calculateSubTotal(type) {
        let unitPrice = $("#txt_price").val();
        let qty = $("#txt_qty").val();
        let discount = $("#txt_discount").val();
        let discount_amount = $("#txt_discount_amount").val();
        let subTotal = unitPrice*qty;

        if(type == 'percentage'){
            $("#txt_discount_amount").prop("disabled", true);
            discount_amount = (subTotal*discount)/100;
            if(discount_amount == 0){
                $("#txt_discount_amount").prop("disabled", false);
            }
            $("#txt_discount_amount").val(discount_amount);
            subTotal = subTotal-((subTotal*discount)/100);
        }else if(type == 'amount'){
            if(discount_amount == 0 || discount_amount == ''){
                $("#txt_discount").prop("disabled", false);
            }else{
                $("#txt_discount").prop("disabled", true);
            }
            subTotal = subTotal-discount_amount;
        }else if(discount == '' && discount_amount == ''){
            $("#txt_discount").prop("disabled", false);
            $("#txt_discount_amount").prop("disabled", false);
        }

        $("#txt_sub_total").val(subTotal.toFixed(2));
    }

    let itemsList = [];
    let invoice = [];

    $("#btn_add").click(function () {
        var employee = $("#employee_id").val();
        var customer = $("#customer_id").val();
        var tokenNo = $("#cbo_tokenNo").val();
        var invoiceID = $("#invoice_id").val();
        let itemID = $("#item_id").val();

        if(employee == ''){
            alert("Please enter 'Salesman'!");
            return false;
        }

        if(customer == ''){
            alert("Please enter 'Customer'!");
            return false;
        }

        if(itemID == ''){
            alert("Please enter 'Item'!");
            return false;
        }

        let unitPrice = $("#txt_price").val();
        let qty = $("#txt_qty").val();
        let discount = $("#txt_discount").val();
        let discount_amount = $("#txt_discount_amount").val();
        let sub_total = $("#txt_sub_total").val();
        let itemType = $("#txt_item_type").val();

        let description = ($("#cbo_item").val()).split(" - ");
        let itemDescription = description[1];

        const isDuplicate = itemsList.some(item => item.item_id === itemID);
        if (isDuplicate) {
            alert("Item already exists.");
            return false;
        }

        if (qty == 0 || qty == '') {
            alert("Please enter quantity!.");
            return false;
        }

        itemsList.push({
            invoice_id: invoiceID,
            item_id: itemID,
            item_description: itemDescription,
            item_type: itemType,
            quantity: qty,
            amount: unitPrice,
            discount_percentage: discount,
            discount_amount: discount_amount,
            sub_total: sub_total
        });

        invoice = {
            id: invoiceID,
            employee_no: employee,
            customer_no: customer,
            token_no: tokenNo,
            items: itemsList
        };

        $.ajax({
            url: `/api/new-invoice`,
            method: 'POST',
            data: invoice,
            success: function(response) {
                const invoiceData = response.invoice;
                itemsList = invoiceData.items;

                renderItemsTable(itemsList);

                $("#item_id").val('');
                $("#cbo_item").focus();
                $("#txt_price").val('');
                $("#txt_qty").val('');
                $("#txt_discount").val('');
                $("#txt_discount_amount").val('');
                $("#txt_sub_total").val('');
                $("#cbo_item").val('');
                $(".available_stock_display").hide();

                $("#txt_discount").prop("disabled", false);
                $("#txt_discount_amount").prop("disabled", false);

                loadTotkenNo(invoiceData.id);

                $("#invoice_id").val(invoiceData.id);

            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const response = xhr.responseJSON;
                    alert(response.message);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Something went wrong.!",
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
            }
        });

    });

    function itemRefresh() {
        $("#item_id").val('');
        $("#txt_price").val('');
        $("#txt_qty").val('');
        $("#txt_discount").val('');
        $("#txt_discount_amount").val('');
        $("#txt_sub_total").val('');
        $("#cbo_item").val('');
        $(".available_stock_display").hide();
    }

    function fetchInvoiceDetails(invoiceId) {
        if(invoiceId == ''){
            clearData();
        }else{
            $.ajax({
                url: `/api/invoice-items/${invoiceId}`,
                method: 'GET',
                success: function (res) {
                    $("#employee_id").val(res.employee_no);
                    $("#cbo_employee").val(res.employee_name);

                    $("#customer_id").val(res.customer_no);
                    $("#cbo_customer").val(res.customer_name);

                    $("#cbo_tokenNo").val(res.invoice_id);
                    $("#invoice_id").val(res.invoice_id);

                    $("#txt_totdiscount").val(res.discount_percentage);
                    $("#txt_totdiscount_amount").val(res.discount_amount);

                    invoice = res;
                    itemsList = res.items;
                    renderItemsTable(itemsList);

                },
                error: function () {
                    alert("Failed to load invoice details.");
                }
            });
        }
    }

    function renderItemsTable(items) {
        let tbody = $("#itemTbl tbody");
        tbody.empty();
        let subTotal = 0;

        items.forEach((item, index) => {
            let row = `
            <tr>
                <td>${index + 1}</td>
                <td>${item.item_description}</td>
                <td class="text-end">${item.amount}</td>
                <td>${item.quantity}</td>
                <td>${item.discount_percentage || ''}</td>
                <td class="text-end">${item.discount_amount || ''}</td>
                <td class="text-end">${item.sub_total}</td>
            </tr>
        `;

            subTotal += parseFloat(item.sub_total);
            tbody.append(row);
        });

        $("#txt_total").val(subTotal.toFixed(2));
        calculateGrandTotal();
    }

    function calculateGrandTotal(){
        let grandTotal = $("#txt_total").val();

        let discount = $("#txt_totdiscount").val();
        let discount_amount = $('#txt_totdiscount_amount').val();

        if(discount > 0){
            $("#txt_totdiscount_amount").prop("disabled", true);
            grandTotal = grandTotal-((grandTotal*discount)/100);

        }else if(discount_amount > 0){
            $("#txt_totdiscount").prop("disabled", true);
            grandTotal = grandTotal-discount_amount;

        }else{
            $("#txt_totdiscount").prop("disabled", false);
            $("#txt_totdiscount_amount").prop("disabled", false);
        }

        $("#txt_grandtotal").val(parseFloat(grandTotal).toFixed(2));
        const hasItem = itemsList.some(i => i.item_type === 'item');
        const hasService = itemsList.some(i => i.item_type === 'service');

        if (hasItem && hasService) {
            $('#txt_totdiscount').prop('disabled', true);
            $('#txt_totdiscount_amount').prop('disabled', true);
        } else {
            $('#txt_totdiscount').prop('disabled', false);
            $('#txt_totdiscount_amount').prop('disabled', false);
        }
    }

    function finishInvoice() {
        var invoiceId = $("#invoice_id").val();

        if($("#txt_received_cash").val() === '' || parseFloat($("#txt_received_cash").val()) < parseFloat($("#txt_grandtotal").val())){
            Swal.fire({
                icon: "error",
                title: "Insufficient Cash Received!",
                showConfirmButton: false,
                timer: 1000
            });
            $("#txt_received_cash").focus();
            return false;
        }

        invoice.discount_percentage = $("#txt_totdiscount").val();
        invoice.discount_amount = $("#txt_totdiscount_amount").val();
        invoice.grand_total = $("#txt_grandtotal").val();
        invoice.received_cash = $("#txt_received_cash").val();
        invoice.balance = $("#txt_balance").val();

        $.ajax({
            url: `/api/finish-invoice/${invoiceId}`,
            method: 'POST',
            data: invoice,
            success: function(response) {
                if(response.message === 'Invoice finalized successfully.'){
                    Swal.fire({
                        icon: "success",
                        title: "Successfully finalized invoice.!",
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }else{
                    Swal.fire({
                        icon: "error",
                        title: "Finalization Failed",
                        text: response.error ?? 'Something went wrong while finalizing the invoice.',
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const response = xhr.responseJSON;
                    alert(response.message);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Something went wrong.!",
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
            }
        });

    }

    $(document).on('keydown', 'input, select, textarea, button', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            if (this.id === 'txt_discount_amount') {
                $('#btn_add').click();
                return;
            }

            if (this.id === 'txt_received_cash') {
                finishInvoice();
                return;
            }

            const focusables = $('input, select, textarea, button')
                .filter(':visible:not([readonly]):not([disabled])')
                .filter(function () {
                    return $(this).attr('tabindex') !== '-1';
                });

            const index = focusables.index(this);

            if ($(this).is('button')) {
                $(this).click();
            }

            if (index > -1 && index + 1 < focusables.length) {
                focusables.eq(index + 1).focus();
            }
        }
    });



    function getBalance(){
        let grand_total = $('#txt_grandtotal').val();
        let recieved_cash = $('#txt_received_cash').val();

        let balance = parseFloat(recieved_cash) - parseFloat(grand_total);
        if(balance > 0){
            $('#txt_balance').val(balance.toFixed(2));
        }else{
            $('#txt_balance').val('0.00');
        }
    }

    function loadAvailableStock(){

            $.get(`/api/available-stock`, {item_id: $("#item_id").val(), store: '1-2-6-1000'}, function (data) {
                const available = data.available_stock ?? 0;

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

            });
    }

    $("#txt_qty").on("keyup", function () {
        const max = parseFloat($(this).attr("max")) || Infinity;
        const value = parseFloat($(this).val()) || 0;

        if (value > max) {
            $(this).val(max);
            alert(`Maximum allowed quantity is ${max}`);
        }
    });


</script>







