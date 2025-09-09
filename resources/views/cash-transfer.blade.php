@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Cash Transfer'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-3">
                <div class="white_card mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Cash Transfer</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="white_card mb_30">
                            <div class="white_card_body">
                                <div class="QA_section">
                                    <form id="cashTransferForm">
                                        <input type="hidden" id="cashTransfer_id">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="common_input mb_15">
                                                    <label>From</label>
                                                    <input type="text" class="form-control" id="cbo_creditAccount" name="cbo_creditAccount" placeholder="Select type..." tabindex="1">
                                                    <input type="hidden" id="cbo_creditAccountCode">
                                                    <input type="hidden" id="txt_creditBalance">
                                                </div>
                                                <div class="common_input mb_15">
                                                    <label>To</label>
                                                    <input type="text" class="form-control" id="cbo_debitAccount" name="cbo_debitAccount" placeholder="Select type..." tabindex="1">
                                                    <input type="hidden" id="cbo_debitAccountCode">
                                                </div>
                                                <div class="common_input mb_15">
                                                    <label>Description</label>
                                                    <input type="text" id="description" placeholder="Description" required>
                                                </div>
                                                <div class="common_input mb_15">
                                                    <label>Amount</label>
                                                    <input type="number" class="form-control common_input" id="amount" step="0.01"
                                                           placeholder="Amount" onkeyup="validateAmount()" required>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="saveCashTransferBtn">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-9">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Cash Transfer List</h3>
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
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="cashTransferTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th id="userColumn">User</th>
                                            </tr>
                                            </thead>
                                            <tbody id="cashTransferTable">
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

<script>
    const cashTransferApiUrl = '/api/cashTransfer';
    // const token = $('meta[name="api-token"]').attr('content');
    loadCashTransfer();
    loadAccounts();
    $("#cbo_creditAccount").focus();

    function loadCashTransfer() {
        $.ajax({
            url: cashTransferApiUrl,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                const data = response.cashTransfer;
                let table = $('.lms_table_active').DataTable();
                table.clear();

                let i = 1;
                data.forEach(cashTransfer => {
                    let row = [
                        i++,
                        cashTransfer.date,
                        cashTransfer.credit_account_name,
                        cashTransfer.debit_account_name,
                        cashTransfer.description,
                        cashTransfer.amount,
                        cashTransfer.user_name
                    ];

                    table.row.add(row);
                });

                table.draw();

            },
            error: function(xhr) {
                console.error('Error loading cashTransfer:', xhr.responseJSON);
                alert('Authentication failed. Please log in again.');
            }
        });
    }

    function loadAccounts(){

        $("#cbo_creditAccount").autocomplete({
            source: function (request, response) {
                if (request.term.length < 1) return;

                $.ajax({
                    url: '/api/cashTransfer-account-dropdown',
                    dataType: 'json',
                    data: { search_key: request.term, posting_Account: $("#cbo_debitAccountCode").val()},
                    success: function (data) {
                        response(data);
                        if (data.length === 1) {
                            $("#cbo_creditAccount").val(data[0].label);
                            $("#cbo_creditAccountCode").val(data[0].value);
                            $("#txt_creditBalance").val(data[0].balance);
                            validateAmount()
                        }
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                $("#cbo_creditAccount").val(ui.item.label);
                $("#cbo_creditAccountCode").val(ui.item.value);
                $("#txt_creditBalance").val(ui.item.balance);
                validateAmount()
                return false;
            }
        });

        $("#cbo_debitAccount").autocomplete({
            source: function (request, response) {
                if (request.term.length < 1) return;

                $.ajax({
                    url: '/api/cashTransfer-account-dropdown',
                    dataType: 'json',
                    data: { search_key: request.term, posting_Account: $("#cbo_creditAccountCode").val() },
                    success: function (data) {
                        response(data);
                        if (data.length === 1) {
                            $("#cbo_debitAccount").val(data[0].label);
                            $("#cbo_debitAccountCode").val(data[0].value);
                        }
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                $("#cbo_debitAccount").val(ui.item.label);
                $("#cbo_debitAccountCode").val(ui.item.value);
                return false;
            }
        });
        $("#cbo_creditAccount").focus();
    }

    function saveCashTransfer() {
        const cashTransfer_id = $('#cashTransfer_id').val();
        const data = {
            id: cashTransfer_id,
            credit_account: $('#cbo_creditAccountCode').val(),
            debit_account: $('#cbo_debitAccountCode').val(),
            description: $('#description').val(),
            amount: $('#amount').val(),
        };


        $.ajax({
            url: cashTransferApiUrl,
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            data: data,
            success: function () {
                Swal.fire({
                    icon: "success",
                    title: cashTransfer_id ? "Updated Successfully" : "Saved Successfully",
                    showConfirmButton: false,
                    timer: 1500
                });
                loadCashTransfer();
                $("#cbo_creditAccount").val("");
                $("#cbo_creditAccountCode").val("");
                $("#txt_creditBalance").val("");
                $("#cbo_debitAccount").val("");
                $("#cbo_debitAccountCode").val("");
                $('#description').val("");
                $('#amount').val("");
                $('#cashTransfer_id').val("");

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

    $(document).on('keydown', 'input, select, textarea, button', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            const focusables = $('input, select, textarea, button')
                .filter(':visible:not([readonly]):not([disabled])');

            const index = focusables.index(this);

            if (index > -1 && index + 1 < focusables.length) {
                const next = focusables.eq(index + 1);
                next.focus();

                if (next.is('button') && next.text().trim() === 'Save') {
                    next.click();
                }
            } else {
                saveCashTransfer();
            }
        }
    });

    $(document).on('click', 'button', function () {
        if ($(this).text().trim() === 'Save') {
            saveCashTransfer();
        }
    });

    function validateAmount(){
        const creditBalance = $('#txt_creditBalance').val();
        const amount = $('#amount').val();

        if(parseFloat(amount) > parseFloat(creditBalance)){
            Swal.fire({
                icon: 'error',
                title: 'Invalid Amount',
                text: `The amount entered (${amount}) exceeds your available balance of ${Math.max(creditBalance, 0)}.`,
                confirmButtonText: 'OK'
            });
            $('#amount').val(Math.max(creditBalance, 0));
        }
    }
</script>
