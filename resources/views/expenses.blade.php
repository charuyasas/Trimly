@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Expenses'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Expenses List</h3>
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
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="expensesTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expensesModal" onclick="openAddExpensesModal()">
                                                    Add New
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Account</th>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th id="userColumn">User</th>
                                            </tr>
                                            </thead>
                                            <tbody id="expensesTable">
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

<!-- Add/Edit Expenses Modal -->
<div class="modal fade" id="expensesModal" tabindex="-1" role="dialog" aria-labelledby="expensesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Expenses</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" style="padding-left: 50%;"><h6 class="red_color">Cash Balance : Rs. <span id="txt_cashBalance"></span></h6></div>
                <form id="expensesForm">
                    <input type="hidden" id="expenses_id">
                    <div class="white_card_body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <label>Expense Type</label>
                                    <input type="text" class="form-control" id="cbo_debitAccount" name="cbo_debitAccount" placeholder="Select type..." tabindex="1">
                                    <input type="hidden" id="cbo_debitAccountCode">
                                </div>
                                <div class="common_input mb_15">
                                    <label>Date</label>
                                    <input type="date" class="form-control" name="effectiveDate" id="effectiveDate" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="common_input mb_15">
                                    <label>Description</label>
                                    <input type="text" id="description" placeholder="Description" required>
                                </div>
                                <div class="common_input mb_15">
                                    <label>Amount</label>
                                    <input type="number" class="form-control common_input" id="amount" step="0.01"
                                       placeholder="Amount" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveExpensesBtn" onclick="saveExpenses()">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const expensesApiUrl = '/api/expenses';
    const token = $('meta[name="api-token"]').attr('content');
    loadExpenses();

    function loadExpenses() {
        $.ajax({
            url: expensesApiUrl,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                const isAdmin = response.is_admin;
                const data = response.expenses;
                let table = $('.lms_table_active').DataTable();
                table.clear();

                let i = 1;
                data.forEach(expense => {
                    let row = [
                        i++,
                        expense.posting_account_name,
                        expense.effective_date,
                        expense.description,
                        expense.amount
                    ];

                    if (isAdmin) {
                        row.push(expense.user_name);
                    }

                    table.row.add(row);
                });

                table.draw();

                if (!isAdmin) {
                    $('#userColumn').hide();

                    $('.lms_table_active tbody tr').each(function() {
                        $(this).find('td:eq(5)').hide();
                    });
                } else {
                    $('#userColumn').show();
                    $('.lms_table_active tbody tr').each(function() {
                        $(this).find('td:eq(5)').show();
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading expenses:', xhr.responseJSON);
                alert('Authentication failed. Please log in again.');
            }
        });
    }

    function loadDebitAccounts(){
        $("#cbo_debitAccount").autocomplete({
            source: function (request, response) {
                if (request.term.length < 1) return;

                $.ajax({
                    url: '/api/expenses-account-dropdown',
                    dataType: 'json',
                    data: { search_key: request.term },
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
            appendTo: "#expensesModal",
            select: function (event, ui) {
                $("#cbo_debitAccount").val(ui.item.label);
                $("#cbo_debitAccountCode").val(ui.item.value);
                return false;
            }
        });
    }

    function saveExpenses() {
        const expenses_id = $('#expenses_id').val();
        const data = {
            id: expenses_id,
            debit_account: $('#cbo_debitAccountCode').val(),
            effective_date: $('#effectiveDate').val(),
            description: $('#description').val(),
            amount: $('#amount').val(),
        };


        $.ajax({
            url: expensesApiUrl,
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            data: data,
            success: function () {
                Swal.fire({
                    icon: "success",
                    title: expenses_id ? "Updated Successfully" : "Saved Successfully",
                    showConfirmButton: false,
                    timer: 1500
                });
                loadExpenses();
                closeExpensesModal();
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


    function closeExpensesModal() {
        const modalElement = document.getElementById('expensesModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
        $('#expensesForm')[0].reset();
        $('#expenses_id').val('');
        $('.modal-title').text('Add Expenses');
        $('#saveExpensesBtn').text('Save');
    }

    function openAddExpensesModal() {
        $('#expensesForm')[0].reset();
        $('#expenses_id').val('');
        loadDebitAccounts();
        loadCashBalance();
    }

    function loadCashBalance(){
        $.ajax({
            url: '/api/cash-balance',
            method: 'GET',
            success: function (data) {
                $('#txt_cashBalance').text(Number(data).toFixed(2));
            },
            error: function (xhr, status, error) {
                console.error('Error loading cash balance:', error);
            }
        });
    }

    // Tab on Enter Key
    $(document).on('keydown', 'input, select, textarea', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const form = $(this).closest('form');
            const focusables = form.find('input, select, textarea, button').filter(':visible:not([readonly]):not([disabled])');
            const index = focusables.index(this);
            if (index > -1 && index + 1 < focusables.length) {
                focusables.eq(index + 1).focus();
            } else {
                $('#saveExpensesBtn').click();
            }
        }
    });

    $('#expensesForm').on('submit', function (e) {
        e.preventDefault();
    });
</script>
