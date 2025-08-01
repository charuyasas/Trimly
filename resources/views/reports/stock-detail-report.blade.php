@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Stock Detail Report'])

<!-- Styles -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #printableArea, #printableArea * {
            visibility: visible;
        }

        #printableArea {
            background: white;
            position: static !important;
            top: 0;
            left: 0;
            width: 100%;
            overflow: visible !important;
            margin: 0;
        }

        .no-print {
            display: none !important;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group !important;
        }

        tfoot {
            display: table-footer-group !important;
        }

        @page {
            size: A4 portrait;
            margin: 10mm 15mm 15mm 15mm; /* add bottom margin for spacing */
        }

        html, body {
            height: auto !important;
            overflow: visible !important;
        }

        #printTitle {
            display: block !important;
            text-align: center;
            font-size: 24px;
        }

    }

    .fixed-table {
        width: 50%;
        margin: 0 auto 2rem auto;
        border-collapse: collapse;
        table-layout: fixed;
        position: relative;
    }

    .fixed-table, .fixed-table * {
        border: none !important;
    }

    .fixed-table tbody::before {
        content: "";
        position: absolute;
        top: calc(1.8em + 8px);
        left: 0;
        width: 100%;
        border-top: 3px solid rgba(136, 79, 251, 0.9);
    }

    .fixed-table tbody::after {
        content: "";
        position: absolute;
        top: calc(1.8em + 8px);
        left: 50%;
        height: calc(100% - 4.5em);
        border-left: 3px solid rgba(136, 79, 251, 0.9);
    }

    .fixed-table thead th {
        text-align: center;
        font-weight: bold;
        padding-top: 8px;
    }

    .fixed-table th, .fixed-table td {
        padding: 8px 12px;
        vertical-align: middle;
        text-align: left;
    }

    /* Container spans inside each cell */
    .ref-type {
        float: left;
        font-weight: 500;
    }

    .qty {
        float: right;
        font-weight: 600;
    }

    /* Clear floats after cell content */
    .fixed-table td::after {
        content: "";
        display: table;
        clear: both;
    }

    /* Balance cell styling */
    .balance-cell {
        padding-right: 12px;
    }

</style>

<!-- Content -->
<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30 m-3 p-4">
                    <!-- Filter Section -->
                    <div class="row align-items-end mb-4 no-print">
                        <!-- Store -->
                        <div class="col-md-3">
                            <label class="form-label">Store <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cbo_store" placeholder="Select store..." autocomplete="off">
                            <input type="hidden" id="store_id">
                            <input type="hidden" id="store_ledger_code">
                        </div>

                        <!-- Date Range -->
                        <div class="col-md-3">
                            <label class="form-label">Date Range <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="filter_date_range" placeholder="Select Date Range">
                        </div>

                        <!-- Item -->
                        <div class="col-md-3">
                            <label class="form-label">Item</label>
                            <input type="text" class="form-control" id="item_name" placeholder="Search Item..." autocomplete="off">
                            <input type="hidden" id="item_id">
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-primary" onclick="filterStockDetail()">Filter</button>
                            <button class="btn btn-secondary" onclick="resetFilters()" title="Reset Filters">
                                <i class="fa fa-refresh"></i>
                            </button>
                            <button class="btn btn-primary ms-2" onclick="printReport()">Print Report</button>
                        </div>
                    </div>

                    <!-- Printable Area -->
                    <div id="printableArea">
                        <h2 id="printTitle" class="text-center d-none d-print-block">Stock Detail Report</h2>
                        <div id="reportContent" class="table-responsive"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
    // ===== Item Autocomplete =====
    $('#item_name').autocomplete({
        source: function (request, response) {
            if (request.term.length < 1) return;

            $.ajax({
                url: '/api/items-list',
                dataType: 'json',
                data: { search_key: request.term },
                success: function (data) {
                    const items = data.map(item => ({
                        label: item.label,
                        value: item.value
                    }));
                    response(items);
                    if (items.length === 1) {
                        $('#item_name').val(items[0].label);
                        $('#item_id').val(items[0].value);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Item autocomplete error:', error);
                }
            });
        },
        minLength: 1,
        focus: function (event, ui) {
            $('#item_name').val(ui.item.label);
            return false;
        },
        select: function (event, ui) {
            $('#item_name').val(ui.item.label);
            $('#item_id').val(ui.item.value);
            return false;
        }
    });

    $('#item_name').on('input', function () {
        $('#item_id').val('');
    });

    // ===== Store Autocomplete =====
    $('#cbo_store').autocomplete({
        source: function (request, response) {
            if (request.term.length < 1) return;

            $.ajax({
                url: '/api/store-list',
                dataType: 'json',
                data: { search_key: request.term },
                success: function (data) {
                    const stores = data.map(store => ({
                        label: store.label,
                        value: store.value,
                        store_ledger_code: store.store_ledger_code
                    }));
                    response(stores);

                    if (stores.length === 1) {
                        $('#cbo_store').val(stores[0].label);
                        $('#store_id').val(stores[0].value);
                        $('#store_ledger_code').val(stores[0].store_ledger_code);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Store autocomplete error:', error);
                }
            });
        },
        minLength: 1,
        appendTo: ".white_card",
        focus: function (event, ui) {
            $('#cbo_store').val(ui.item.label);
            return false;
        },
        select: function (event, ui) {
            $('#cbo_store').val(ui.item.label);
            $('#store_id').val(ui.item.value);
            $('#store_ledger_code').val(ui.item.store_ledger_code);
            return false;
        }
    });

    // ===== Filter Logic =====
    function filterStockDetail() {
        const itemId = $('#item_id').val();
        const startDate = $('#filter_date_range').data('start');
        const endDate = $('#filter_date_range').data('end');
        const storeId = $('#store_ledger_code').val();

        if (!startDate || !endDate || !storeId) {
            alert('Please fill in start date, end date, and store.');
            return;
        }

        const payload = {
            start_date: startDate,
            end_date: endDate,
            store: storeId
        };

        // Only include item_ids if an item is selected
        if (itemId && itemId.trim() !== '') {
            payload.item_ids = [itemId];
        }

        fetch('/api/stock-detail-report', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(data => renderStockDetailReport(data))
            .catch(err => {
                console.error('Error fetching stock detail:', err);
                alert('Error loading data. Please check the console.');
            });
    }

    // ===== Render Report Table =====
    function renderStockDetailReport(data) {
        const container = document.getElementById('reportContent');
        container.innerHTML = '';

        if (!data || !data.length) {
            container.innerHTML = '<p class="text-center">No records found.</p>';
            return;
        }

        data.forEach(item => {
            const table = document.createElement('table');
            table.className = 'table table-bordered fixed-table mb-5';

            const thead = `
            <thead class="table-light">
                <tr><th colspan="2" class="text-center">${item.description || item.item_name || ''}</th></tr>
            </thead>`;

            // Separate debit and credit entries as objects with separate parts
            const debitEntries = [];
            const creditEntries = [];

            (item.transactions || []).forEach(tx => {
                if (tx.debit && tx.debit != 0) {
                    debitEntries.push({ ref: tx.reference_type, qty: tx.debit });
                }
                if (tx.credit && tx.credit != 0) {
                    creditEntries.push({ ref: tx.reference_type, qty: tx.credit });
                }
            });

            const totalDebit = item.transactions ? item.transactions.reduce((acc, tx) => acc + (tx.debit || 0), 0) : 0;
            const totalCredit = item.transactions ? item.transactions.reduce((acc, tx) => acc + (tx.credit || 0), 0) : 0;
            const balance = (totalDebit - totalCredit).toFixed(2);

            const maxRows = Math.max(debitEntries.length, creditEntries.length);

            let tbodyRows = '';
            for (let i = 0; i < maxRows; i++) {
                const debit = debitEntries[i];
                const credit = creditEntries[i];

                const debitCell = debit
                    ? `<td><span class="ref-type">${debit.ref}</span><span class="qty">${debit.qty}</span></td>`
                    : `<td></td>`;

                const creditCell = credit
                    ? `<td><span class="ref-type">${credit.ref}</span><span class="qty">${credit.qty}</span></td>`
                    : `<td></td>`;

                tbodyRows += `<tr>${debitCell}${creditCell}</tr>`;
            }

            // Total and Balance rows
            const totalRow = `
            <tr class="t-border">
                <td class="text-end fw-bold">Total Debit: ${totalDebit.toFixed(2)}</td>
                <td class="text-end fw-bold">Total Credit: ${totalCredit.toFixed(2)}</td>
            </tr>`;

            const balanceRow = `
            <tr class="t-border">
                <td colspan="2" class="text-center fw-bold">Balance: ${Math.abs(balance)}</td>
            </tr>`;

            const tfoot = `<tfoot>${totalRow}${balanceRow}</tfoot>`;

            table.innerHTML = thead + `<tbody>${tbodyRows}</tbody>` + tfoot;
            container.appendChild(table);
        });
    }

    // ===== Print Report =====
    function printReport() {
        window.print();
    }

    $(function () {
        $('#filter_date_range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('#filter_date_range').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            $(this).data('start', picker.startDate.format('YYYY-MM-DD'));
            $(this).data('end', picker.endDate.format('YYYY-MM-DD'));
        });

        $('#filter_date_range').on('cancel.daterangepicker', function () {
            $(this).val('');
            $(this).removeData('start').removeData('end');
        });
    });

    function resetFilters() {
        $('#cbo_store').val('');
        $('#store_id').val('');
        $('#store_ledger_code').val('');
        $('#item_name').val('');
        $('#item_id').val('');
        $('#filter_date_range').val('');
        $('#filter_date_range').removeData('start').removeData('end');

        $('#reportContent').html('');
    }

    // Enable Enter key navigation
    $(document).on('keydown', 'input, select, textarea', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            const focusables = $('input, select, textarea, button')
                .filter(':visible:not([readonly]):not([disabled]):not(.no-enter-nav)');

            const index = focusables.index(this);
            const next = focusables.eq(index + 1);

            if (next.length) {
                if (next.is('button')) {
                    next.click(); // auto-trigger buttons
                } else {
                    next.focus();
                }
            }
        }
    });

    $('form').on('submit', function (e) {
        e.preventDefault();
    });

</script>

<!-- Moment.js and Daterangepicker -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">

