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
                                    <div class="col-md-2">
                                        <label class="form-label" for="cbo_tokenNo">Token No. <code>*</code></label>
                                        <input type="text" class="form-control" id="cbo_tokenNo">
                                        <input type="hidden" id="invoice_id">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="dtm_invoiceDate">Date <code>*</code></label>
                                        <input type="text" class="form-control" id="dtm_invoiceDate" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" disabled>

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
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label" for="txt_qty">Qty <code>*</code></label>
                                        <input type="number" class="form-control" id="txt_qty" name="qty" tabindex="4" min=1 onchange="calculateSubTotal();">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="txt_price">Price <code>*</code></label>
                                        <input type="text" class="form-control text-end" id="txt_price" disabled>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label" for="txt_discount">Discount</label>
                                        <input type="text" class="form-control" tabindex="5" id="txt_discount" onkeyup="calculateSubTotal()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="txt_discount_amount">Dis. Amount</label>
                                        <input type="text" class="form-control text-end" id="txt_discount_amount" onkeyup="calculateSubTotal()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="txt_sub_total">Sub Total <code>*</code></label>
                                        <input type="text" class="form-control text-end" id="txt_sub_total" disabled>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-secondary" id="btn_add"> <i class="fas fa-plus"></i></button>
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
                                        <th scope="col">Qty</th>
                                        <th scope="col">Price</th>
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
                                        <td class="right"><input type="text" class="form-control text-end" id="txt_total" disabled  value="0.00"></td>
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
                                                <strong><input type="text" class="form-control text-end" id="txt_grandtotal" disabled  value="0.00"></strong>
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

    $(function () {

        $("#cbo_employee").autocomplete({
            source: function (request, response) {
                if (request.term.length < 1) return;

                $.ajax({
                    url: '/api/employees-list',
                    dataType: 'json',
                    data: { q: request.term },
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
                        q: request.term
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
            select: function (event, ui) {
                $("#cbo_customer").val(ui.item.label);
                $("#customer_id").val(ui.item.value);
                return false;
            }
        });

        $("#cbo_tokenNo").autocomplete({
            source: function (request, response) {
                if (request.term.length < 1) return;

                $.ajax({
                    url: '/api/invoice-list',
                    dataType: 'json',
                    data: {
                        q: request.term
                    },
                    success: function (data) {
                        response(data);

                        if (data.length === 1) {
                            $("#cbo_tokenNo").val(data[0].label);
                            $("#invoice_id").val(data[0].value);
                            fetchInvoiceDetails(data[0].value);

                        }
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                $("#cbo_tokenNo").val(ui.item.label);
                $("#invoice_id").val(ui.item.value);
                fetchInvoiceDetails(ui.item.value);

                return false;
            },
            change: function (event, ui) {
                if (!ui.item) {
                    $("#cbo_tokenNo").val('');
                    $("#invoice_id").val('');
                    $("#employee_id").val('');
                    $("#cbo_employee").val();

                    $("#customer_id").val('');
                    $("#cbo_customer").val('');

                    $("#cbo_tokenNo").val('');
                    $("#invoice_id").val('');

                    $("#txt_totdiscount").val('');
                    $("#txt_totdiscount_amount").val('');

                    invoice = res;
                    itemsList = res.items;
                    renderItemsTable(itemsList);

                }
            }
        });

        $("#cbo_item").autocomplete({
            source: function (request, response) {
                if (request.term.length < 1) return;

                $.ajax({
                    url: '/api/item-list',
                    dataType: 'json',
                    data: {
                        q: request.term
                    },
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.label,
                                value: item.label,
                                id: item.value,
                                price: item.price
                            };
                        }));

                        if (data.length === 1) {
                            $("#cbo_item").val(data[0].label);
                            $("#item_id").val(data[0].value);
                            $("#txt_price").val(data[0].price);
                            $("#txt_qty").val(1);
                            $("#txt_sub_total").val((data[0].price*1).toFixed(2));
                            itemSelectedFromAutocomplete = true;
                        }
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                $("#cbo_item").val(ui.item.label);
                $("#item_id").val(ui.item.value);
                $("#txt_price").val(ui.item.price);
                $("#txt_qty").val(1);
                $("#txt_sub_total").val((ui.item.price*1).toFixed(2));
                itemSelectedFromAutocomplete = true;
                return false;
            }
        });

    });

    $("#cbo_item").on("input", function () {
        if (!itemSelectedFromAutocomplete) {
            $("#txt_price").val('');
            $("#txt_qty").val('');
            $("#txt_sub_total").val('');
        }
        itemSelectedFromAutocomplete = false;
    });

    function calculateSubTotal() {
        let unitPrice = $("#txt_price").val();
        let qty = $("#txt_qty").val();
        let discount = $("#txt_discount").val();
        let discount_amount = $("#txt_discount_amount").val();
        let subTotal = unitPrice*qty;

        if(discount != ""){
            $("#txt_discount_amount").prop("disabled", true);
            discount_amount = (subTotal*discount)/100;
            $("#txt_discount_amount").val(discount_amount);
            subTotal = subTotal-((subTotal*discount)/100);

        }else if(discount_amount != ""){
            $("#txt_discount").prop("disabled", true);
            subTotal = subTotal-discount_amount;

        }else{
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
            alert("Please enter 'Employee'!");
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

        let description = ($("#cbo_item").val()).split(" - ");
        let itemDescription = description[1];

        const isDuplicate = itemsList.some(item => item.item_id === itemID);
    if (isDuplicate) {
        alert("Item already exists.");
        return false;
    }

            itemsList .push({
        invoice_id: invoiceID,
        item_id: itemID,
        item_description: itemDescription,
        quantity: qty,
        amount: unitPrice,
        discount_percentage: discount,
        discount_amount: discount_amount,
        sub_total: sub_total
    });


         invoice = {
            id: invoiceID,
            invoice_no: tokenNo,
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
                $("#txt_price").val('');
                $("#txt_qty").val('');
                $("#txt_discount").val('');
                $("#txt_discount_amount").val('');
                $("#txt_sub_total").val('');
                $("#cbo_item").val('');
$("#txt_discount").prop("disabled", false);
                $("#txt_discount_amount").prop("disabled", false);

                $("#cbo_tokenNo").val(invoiceData.invoice_no);
                $("#invoice_id").val(invoiceData.id);
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

    });

    function fetchInvoiceDetails(invoiceId) {
        $.ajax({
            url: `/api/invoice-items/${invoiceId}`,
            method: 'GET',
            success: function (res) {
                $("#employee_id").val(res.employee_no);
                $("#cbo_employee").val(res.employee_name);

                $("#customer_id").val(res.customer_no);
                $("#cbo_customer").val(res.customer_name);

                $("#cbo_tokenNo").val(res.token_no);
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

    function renderItemsTable(items) {
        let tbody = $("#itemTbl tbody");
        tbody.empty(); let subTotal = 0;
        items.forEach((item, index) => {
            let row = `
            <tr>
                <td>${index + 1}</td>
                <td>${item.item_description}</td>
                <td>${item.quantity}</td>
                <td class="text-end">${item.amount}</td>
                <td>${item.discount_percentage || ''}</td>
                <td class="text-end">${item.discount_amount || ''}</td>
                <td class="text-end">${item.sub_total}</td>
            </tr>
        `;
            subTotal += parseFloat(item.sub_total);tbody.append(row);
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
    }

function finishInvoice() {
        var invoiceId = $("#invoice_id").val();

        invoice.discount_percentage = $("#txt_totdiscount").val();
        invoice.discount_amount = $("#txt_totdiscount_amount").val();
        invoice.grand_total = $("#txt_grandtotal").val();

        $.ajax({
            url: `/api/finish-invoice/${invoiceId}`,
            method: 'POST',
            data: invoice,
            success: function(response) {
                alert('Successfully finalized invoice.');
                location.reload();
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







