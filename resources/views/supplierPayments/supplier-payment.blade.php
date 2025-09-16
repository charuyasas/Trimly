@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Supplier Payments'])

<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="row mb-3">
            <div class="col-md-6 d-flex align-items-center gap-2">
                <label class="w-25">Supplier</label>
                <select id="supplierSelect" class="form-control w-100" style="width: 100%;" placeholder="Select Supplier"></select>
            </div>
        </div>

        {{-- Summary --}}
        <div class="white_card mb_30">
            <div class="white_card_header">
                <div class="details-grid d-flex justify-content-between">
                    <div>
                        <div><strong>Supplier Name: </strong><span id="lblSupplier">—</span></div>
                        <div><strong>Pending GRNs: </strong><span id="lblPending">0</span></div>
                    </div>
                    <div>
                        <div><strong>Total Balance: </strong><span id="lblBalance">0.00</span></div>
                        <div><strong>Selected Total: </strong><span id="lblSelected">0.00</span></div>
                    </div>
                </div>
            </div>

            <div class="white_card_body">

                {{-- Auto Pay Section --}}
                <div class="row mb-4">
                    <div class="col-md-4 d-flex align-items-center gap-2">
                        <label class="w-50">Pay Amount</label>
                        <input type="number" id="autoPayAmount" class="form-control" placeholder="Enter amount">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" id="autoPayBtn">
                            Pay
                        </button>
                    </div>
                </div>

                {{-- Pending GRNs --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="grnTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>GRN No</th>
                            <th>Invoice No</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Balance</th>
                            <th>Payments</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- Filled by JS --}}
                        </tbody>
                    </table>
                </div>

                {{-- Selected Payments --}}
                <h5 class="mt-4">Selected Payments</h5>
                <table class="table table-bordered table-sm" id="selectedTable">
                    <thead>
                    <tr>
                        <th>GRN No</th>
                        <th class="text-end">Payment</th>
                        <th>Remove</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="text-center text-muted">
                        <td colspan="3">No payments selected.</td>
                    </tr>
                    </tbody>
                </table>
                <div class="mt-2"><strong>Total: </strong><span id="selectedTotal">0.00</span></div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#finishPaymentModal" id="finishBtn" disabled>
                        <i class="fa fa-check"></i> Finish Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Finish Payment Modal --}}
<div class="modal fade" id="finishPaymentModal" tabindex="-1" aria-labelledby="finishPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Finish Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <strong>Total Payment:</strong> <span id="modalTotalPayment">0.00</span>
                </div>
                <div class="mb-3">
                    <strong>Remaining Balance:</strong> <span id="modalRemainingBalance">0.00</span>
                </div>

                <hr>

                <!-- Payment Method Selector -->
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select id="paymentType" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Deposit</option>
                    </select>
                </div>

                <!-- Cash Input -->
                <div class="mb-3 payment-method payment-cash">
                    <label for="cashPayment" class="form-label">Cash Payment Amount</label>
                    <input type="number" class="form-control" id="cashPayment" placeholder="Enter cash amount">
                </div>

                <!-- Bank Inputs -->
                <div class="payment-method payment-bank d-none">
                    <div class="mb-3">
                        <label class="form-label">Bank Name</label>
{{--                        <input type="text" class="form-control" id="bankName" placeholder="Enter bank name">--}}
                        <input type="text" class="form-control" id="bankName" name="bank" placeholder="Select bank..." tabindex="1">
                        <input type="hidden" id="bank_id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank Slip No</label>
                        <input type="text" class="form-control" id="bankSlipNo" placeholder="Enter bank slip number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Effective Date</label>
                        <input type="date" class="form-control" id="bankDate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank Payment Amount</label>
                        <input type="number" class="form-control" id="bankAmount" placeholder="Enter bank payment amount">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="confirmFinishPaymentBtn">
                    <i class="fa fa-check"></i> Confirm Payment
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')

<!-- Include jQuery and Select2 CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
    let grns = [];
    let selectedPayments = [];
    let currentSupplierId = null;

    function fmtNumber(num) {
        return Number(num).toLocaleString('en-LK', { minimumFractionDigits: 2 });
    }

    function renderGRNTable() {
        const tbody = $('#grnTable tbody');
        tbody.empty();

        if (!grns.length) {
            tbody.append('<tr><td colspan="8" class="text-center p-3">No pending GRNs found.</td></tr>');
            return;
        }

        grns.forEach((grn, index) => {
            tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${grn.grn_date}</td>
                    <td>${grn.grn_number}</td>
                    <td>${grn.supplier_invoice_number}</td>
                    <td class="text-end">${fmtNumber(grn.grand_total)}</td>
                    <td class="text-end">${fmtNumber(grn.balance)}</td>
                    <td><input type="number" class="form-control form-control-sm pay-input"
                               data-id="${grn.id}" max="${grn.balance}" min="0" placeholder="0"></td>
                    <td><button class="btn btn-sm btn-primary add-btn" data-id="${grn.id}"><i class="fa fa-plus"></i></button></td>
                </tr>
            `);
        });
    }

    function renderSelectedTable() {
        const tbody = $('#selectedTable tbody');
        tbody.empty();

        if (!selectedPayments.length) {
            tbody.append('<tr class="text-center text-muted"><td colspan="3">No payments selected.</td></tr>');
            $('#finishBtn').prop('disabled', true);
            return;
        }

        let total = 0;
        selectedPayments.forEach((selected) => {
            total += selected.payment;
            tbody.append(`
                <tr>
                    <td>${selected.grn_number}</td>
                    <td class="text-end">${fmtNumber(selected.payment)}</td>
                    <td><button class="btn btn-sm btn-danger remove-btn" data-id="${selected.grn_id}"><i class="fa fa-trash"></i></button></td>
                </tr>
            `);
        });

        // update totals
        $('#selectedTotal').text(fmtNumber(total));
        $('#lblSelected').text(fmtNumber(total));
        $('#finishBtn').prop('disabled', false);
    }

    function loadGRNs(supplierID) {
        if (!supplierID) return;

        currentSupplierId = supplierID;

        $.get('/api/supplier-payments/' + supplierID + '/grns', function (response) {
            grns = response.grns || [];
            const supplierName = response.supplier?.name || '—';

            $('#lblSupplier').text(supplierName);
            $('#lblPending').text(grns.length);
            $('#lblBalance').text(fmtNumber(grns.reduce((sum, g) => sum + Number(g.balance), 0)));

            selectedPayments = [];
            renderSelectedTable();

            grns.sort((a, b) => new Date(a.grn_date) - new Date(b.grn_date));

            renderGRNTable();
        }).fail(function () {
            Swal.fire({
                icon: "error",
                title: "Failed to load supplier GRNs.!",
                showConfirmButton: false,
                timer: 1000
            });
        });
    }

    function updateModalSummary() {
        const total = selectedPayments.reduce((sum, s) => sum + s.payment, 0);
        const remaining = grns.reduce((sum, g) => sum + Number(g.balance), 0);
        $('#modalTotalPayment').text(fmtNumber(total));
        $('#modalRemainingBalance').text(fmtNumber(remaining));

        $("#bankName").autocomplete({
            source: function (request, response) {
                if (request.term.length < 1) return;

                $.ajax({
                    url: '/api/loadBanks',
                    dataType: 'json',
                    data: { search_key: request.term },
                    success: function (data) {
                        response(data);
                        if (data.length === 1) {
                            $("#bankName").val(data[0].label);
                            $("#bank_id").val(data[0].value);
                        }
                    }
                });
            },
            minLength: 1,
            appendTo: "#finishPaymentModal",
            focus: function (event, ui) {
                $("#bankName").val(ui.item.label);
                return false;
            },
            select: function (event, ui) {
                $("#bankName").val(ui.item.label);
                $("#bank_id").val(ui.item.value);
                return false;
            }
        });
    }

    // Prevent overpay input
    $(document).on('input', '.pay-input', function () {
        const max = parseFloat($(this).attr('max'));
        let val = parseFloat($(this).val());

        if (val > max) $(this).val(max);
        else if (val < 0) $(this).val(0);
    });

    // Add GRN manually
    $(document).on('click', '.add-btn', function () {
        const id = $(this).data('id');
        const grn = grns.find(g => g.id == id);
        const payInput = $(`.pay-input[data-id='${id}']`);
        const pay = Number(payInput.val());

        if (selectedPayments.find(s => s.grn_id === id)) {
            Swal.fire({
                icon: "error",
                title: "This GRN is already added.!",
                showConfirmButton: false,
                timer: 1000
            });
            return;
        }

        if (pay > 0 && pay <= grn.balance) {
            selectedPayments.push({ grn_id: grn.id, grn_number: grn.grn_number, payment: pay });
            grn.balance -= pay;
            renderSelectedTable();
            renderGRNTable();
            payInput.val('');
        } else {
            Swal.fire({
                icon: "error",
                title: "Invalid payment amount.!",
                showConfirmButton: false,
                timer: 1000
            });
        }
    });

    // Remove GRN
    $(document).on('click', '.remove-btn', function () {
        const grnId = $(this).data('id');
        const removed = selectedPayments.find(s => s.grn_id === grnId);
        const grn = grns.find(g => g.id === grnId);

        if (grn && removed) grn.balance += removed.payment;

        selectedPayments = selectedPayments.filter(s => s.grn_id !== grnId);
        renderSelectedTable();
        renderGRNTable();
    });

    // Auto allocation
    $('#autoPayBtn').on('click', function () {
        let amount = Number($('#autoPayAmount').val() || 0);

        if (!amount || amount <= 0) {
            Swal.fire({
                icon: "error",
                title: "Please enter a valid auto pay amount.!",
                showConfirmButton: false,
                timer: 1000
            });
            return;
        }

        selectedPayments = [];
        grns.sort((a, b) => new Date(a.grn_date) - new Date(b.grn_date));

        for (let g of grns) {
            if (amount <= 0) break;
            let pay = Math.min(amount, g.balance);
            if (pay > 0) {
                selectedPayments.push({ grn_id: g.id, grn_number: g.grn_number, payment: pay });
                g.balance -= pay;
                amount -= pay;
            }
        }

        if (amount > 0) {
            Swal.fire({
                icon: "error",
                title: "Not enough GRN balances to allocate full amount. Remaining: " + fmtNumber(amount),
                showConfirmButton: false,
                timer: 1000
            });
        }

        renderSelectedTable();
        renderGRNTable();

        // clear input after auto allocation
        $('#autoPayAmount').val('');
    });

    $('#finishPaymentModal').on('show.bs.modal', updateModalSummary);

    $('#paymentType').on('change', function () {
        const type = $(this).val();

        $('#cashPayment').val('');
        $('#bankName').val('');
        $('#bank_id').val('');
        $('#bankSlipNo').val('');
        $('#bankDate').val('');
        $('#bankAmount').val('');

        if (type === 'cash') {
            $('.payment-cash').removeClass('d-none');
            $('.payment-bank').addClass('d-none');
        } else {
            $('.payment-cash').addClass('d-none');
            $('.payment-bank').removeClass('d-none');
        }
    });

    $('#confirmFinishPaymentBtn').on('click', function () {
        if (!currentSupplierId) {
            Swal.fire({
                icon: "error",
                title: "Please select a supplier.!",
                showConfirmButton: false,
                timer: 1000
            });
            return;
        }

        const paymentType = $('#paymentType').val();
        const totalSelected = selectedPayments.reduce((sum, s) => sum + s.payment, 0);

        let payload = {
            supplier_id: currentSupplierId,
            payment_type: paymentType,
            payments: selectedPayments.map(s => ({ grn_no: s.grn_number, amount: s.payment })),
            amount: totalSelected
        };

        if (paymentType === 'cash') {
            const cashPayment = Number($('#cashPayment').val() || 0);
            if (cashPayment !== totalSelected) {
                Swal.fire({
                    icon: "error",
                    title: `Cash entered (${fmtNumber(cashPayment)}) does not match selected total (${fmtNumber(totalSelected)}).`,
                    showConfirmButton: false,
                    timer: 1000
                });
                return;
            }
        } else if (paymentType === 'bank') {
            const bank_id = $('#bank_id').val().trim();
            const slipNo = $('#bankSlipNo').val().trim();
            const date = $('#bankDate').val();
            const bankAmount = Number($('#bankAmount').val() || 0);

            if (!bank_id || !slipNo || !date) {
                Swal.fire({
                    icon: "error",
                    title: 'Bank name, slip number, and date are required for bank payments.',
                    showConfirmButton: false,
                    timer: 1000
                });
                return;
            }

            if (bankAmount !== totalSelected) {
                Swal.fire({
                    icon: "error",
                    title: `Bank amount (${fmtNumber(bankAmount)}) does not match selected total (${fmtNumber(totalSelected)}).`,
                    showConfirmButton: false,
                    timer: 1000
                });
                return;
            }

            payload.bank_id = bank_id;
            payload.bank_slip_no = slipNo;
            payload.date = date;
        }

        $.post('/api/supplier-payments', payload, function () {
            Swal.fire({
                icon: 'success',
                title: 'Payment finished!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                sessionStorage.removeItem('supplier_id');
                window.location.href = "{{ route('supplier.payment') }}";
            });

            // don't reset totals
            selectedPayments = [];

            renderSelectedTable();
            $('#finishPaymentModal').modal('hide');

            // clear supplier select after finish
            $('#supplierSelect').val(null).trigger('change');
            $('#lblSupplier').text('—');
            $('#lblPending').text(0);
            $('#lblBalance').text('0.00');

            $('#lblSelected').text('0.00');
            $('#selectedTotal').text('0.00');
            // keep #lblSelected and #selectedTotal untouched
        }).fail(function () {
            Swal.fire({
                icon: "error",
                title: 'Payment failed. Please try again.',
                showConfirmButton: false,
                timer: 1000
            });
        });
    });

    $(document).ready(function () {
        $('#supplierSelect').select2({
            placeholder: 'Select Supplier',
            allowClear: true,
            ajax: {
                url: '/api/suppliers-list',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data.map(s => ({
                            id: s.value,
                            text: s.label,
                            ledger_code: s.ledger_code
                        }))
                    };
                },
                cache: true
            }
        });

        $('#supplierSelect').on('change', function () {
            const supplierID = $(this).val();
            if (supplierID) {
                loadGRNs(supplierID);
                setTimeout(() => $('#autoPayAmount').focus(), 300);
            } else {
                grns = [];
                selectedPayments = [];
                currentSupplierId = null;
                $('#lblSupplier').text('—');
                $('#lblPending').text(0);
                $('#lblBalance').text('0.00');

                // don't clear totals here
                renderGRNTable();
                renderSelectedTable();
            }
            $('#lblSelected').text('0.00');
            $('#selectedTotal').text('0.00');

        });

        const supplierIDFromStorage = sessionStorage.getItem('supplier_id');
        if (supplierIDFromStorage) {
            $.ajax({
                url: '/api/suppliers',
                dataType: 'json'
            }).done(function(data) {
                const supplier = data.find(s => s.id == supplierIDFromStorage);
                if (supplier) {
                    var option = new Option(supplier.name, supplier.id, true, true);
                    $('#supplierSelect').append(option).trigger('change');
                    setTimeout(() => $('#autoPayAmount').focus(), 300);
                }
            });
        }
    });

    // ENTER key navigation
    $(document).on('keydown', function (e) {
        if (e.key !== 'Enter') return;

        const $target = $(e.target);

        // === MAIN PAGE ===
        if ($target.is('#autoPayAmount')) {
            e.preventDefault();
            const val = Number($target.val() || 0);

            if (val > 0) {
                $('#autoPayBtn').click();
                $('#finishBtn').focus();
            } else {
                // focus first GRN input
                const $firstInput = $('#grnTable .pay-input').first();
                if ($firstInput.length) $firstInput.focus();
            }
        }

        if ($target.hasClass('pay-input')) {
            e.preventDefault();
            const grnId = $target.data('id');
            const val = Number($target.val() || 0);

            const $inputs = $('#grnTable .pay-input');
            const index = $inputs.index($target);

            if (val > 0) {
                // trigger add
                $(`.add-btn[data-id="${grnId}"]`).click();

                // after re-render, focus next row input
                setTimeout(() => {
                    const $newInputs = $('#grnTable .pay-input');
                    if (index < $newInputs.length - 1) {
                        $newInputs.eq(index + 1).focus();
                    } else {
                        $('#finishBtn').focus();
                    }
                }, 100); // small delay to let render finish
            } else {
                // skip to next input if empty/0
                if (index < $inputs.length - 1) {
                    $inputs.eq(index + 1).focus();
                } else {
                    $('#finishBtn').focus();
                }
            }
        }


        if ($target.is('#finishBtn')) {
            e.preventDefault();
            $('#finishBtn').click();
        }

        // === MODAL ===
        if ($target.closest('#finishPaymentModal').length) {
            e.preventDefault();
            const $fields = $('#finishPaymentModal')
                .find('input:visible, select:visible')
                .filter(':enabled');

            const index = $fields.index($target);

            if (index > -1 && index < $fields.length - 1) {
                $fields.eq(index + 1).focus();
            } else {
                $('#confirmFinishPaymentBtn').focus().click();
            }
        }
    });

</script>
