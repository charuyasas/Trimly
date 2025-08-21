@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Sales Summary Report'])


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
                                        <div class="row align-items-end g-3 col-md-12">

                                            <!-- Date Range -->
                                            <div class="col-md-3">
                                                <label class="form-label">Date Range <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="filter_date_range" placeholder="Select Date Range">
                                            </div>

                                            <!-- Item Type -->
                                            <div class="col-md-2">
                                                <label class="form-label" for="cbo_itemType">Item Type <code>*</code></label>
                                                <select class="form-select common_input" id="cbo_itemType" onchange="fetchInvoiceDetails(this.value)">
                                                    <option value="service">Service</option>
                                                    <option value="item">Items</option>
                                                </select>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="col-md-4 d-flex gap-2">
                                                <button type="button" class="btn btn-primary" onclick="loadSalesSummary()">Load Report</button>
                                                <button type="button" class="btn btn-secondary" onclick="printSalesSummary()">Print</button>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active" id="salesSummaryTableExport">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Invoice No.</th>
                                                <th>Salesman</th>
                                                <th>Total Sales</th>
                                                <th>Total Discount</th>
                                                <th>Employee Commission</th>
                                                <th>Profit</th>
                                            </tr>
                                            </thead>
                                            <tbody id="salesSummaryTable"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- col-lg-12 -->
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<script>
    const apiUrl = '/api/salesman-summary';
    let startDate = null;
    let endDate = null;
    let itemType = "Service";

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

    function loadSalesSummary() {
         startDate = $('#filter_date_range').data('start');
         endDate = $('#filter_date_range').data('end');
         itemType = $('#cbo_itemType').val();

        if (!startDate || !endDate) {
            Swal.fire({
                icon: "warning",
                title: "Date Range Required",
                text: "Please select a date range first"
            });
            return;
        }

        $.get(apiUrl, { start_date: startDate, end_date: endDate, item_type: itemType, report_type: 'summary' }, function(data) {
            let table = $('.lms_table_active').DataTable();
            table.clear();

            let dynamicColumn = itemType === "service" ? "Employee Commission" : "GRN Value";
            $("#salesSummaryTableExport thead").html(`
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Invoice No.</th>
                <th>Salesman</th>
                <th>Total Sales</th>
                <th>Total Discount</th>
                <th>${dynamicColumn}</th>
                <th>Profit</th>
            </tr>
        `);


            let rowID = 1;
            data.data.forEach(row => {
                table.row.add([
                    rowID,
                    row.date,
                    row.invoice_no,
                    row.employee_name,
                    row.total_sales,
                    row.total_discount,
                    row.total_commission,
                    row.profit
                ]);
                rowID++;
            });

            table.draw();
        }).fail(function(xhr) {
            Swal.fire({
                icon: "error",
                title: "Failed to load report",
                text: xhr.responseJSON?.message || "Something went wrong",
                showConfirmButton: true
            });
        });
    }


    function printSalesSummary() {
        let printContent = document.getElementById("salesSummaryTableExport").outerHTML;

        let newWin = window.open("");
        newWin.document.write(`
        <html>
            <head>
                <title>Sales Summary Report</title>
                <style>
                    @page {
                        size: A4 portrait;    /* Force A4 portrait */
                        margin: 10mm;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 10px;
                        font-size: 11px;
                    }
                    h2 {
                        text-align: center;
                        margin-bottom: 15px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        table-layout: fixed;   /* lock column widths */
                        max-width: 100%;
                        word-wrap: break-word;
                    }
                    th, td {
                        border: none;
                        padding: 6px;
                        text-align: center;
                        font-size: 11px;
                        vertical-align: middle;
                        white-space: normal;
                        word-break: break-word;
                    }
                    th {
                        background-color: #f2f2f2;
                    }

                    /* Column widths tuned for 8 columns */
                    th:nth-child(1), td:nth-child(1) { width: 5%; }   /* # */
                    th:nth-child(2), td:nth-child(2) { width: 12%; }  /* Date */
                    th:nth-child(3), td:nth-child(3) { width: 12%; }  /* Invoice No. */
                    th:nth-child(4), td:nth-child(4) { width: 26%; }  /* Salesman (long text) */
                    th:nth-child(5), td:nth-child(5) { width: 11%; }  /* Total Sales */
                    th:nth-child(6), td:nth-child(6) { width: 11%; }  /* Discount */
                    th:nth-child(7), td:nth-child(7) { width: 11%; }  /* Commission */
                    th:nth-child(8), td:nth-child(8) { width: 12%; }  /* Profit */
                </style>
            </head>
            <body>
                <h2>Sales Summary Report (${startDate} to ${endDate}) | ${itemType}</h2>
                ${printContent}
            </body>
        </html>
    `);

        newWin.document.close();
        newWin.focus();
        newWin.print();
        newWin.close();
    }

</script>

<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
{{--<script src="{{ asset('assets/js/moment.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>--}}
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
{{--<link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">--}}
