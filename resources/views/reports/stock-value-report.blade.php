@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Stock Value Report'])

<style>
    @media print {
        body * {
            visibility: hidden !important;
        }

        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
            overflow: visible !important;
        }

        #printableTable, #printableTable * {
            visibility: visible !important;
        }

        #printableTable {
            position: static !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 5mm !important;
            background: white !important;
            box-sizing: border-box !important;
            page-break-before: auto !important;
            page-break-after: auto !important;
            page-break-inside: avoid !important;
        }

        #printTitle {
            display: block !important;
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            margin: 0 0 10pt 0 !important;
            padding: 0 !important;
            page-break-after: avoid !important;
            page-break-before: avoid !important;
        }

        table, thead, tbody, tr, th, td {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }

        table {
            border-collapse: collapse !important;
            width: 100% !important;
            font-size: 11pt;
            color: #000;
            table-layout: fixed;
            page-break-inside: auto;
            page-break-after: auto;
        }

        th, td {
            padding: 4pt 6pt !important;
            vertical-align: top !important;
            word-wrap: break-word !important;
        }

        thead {
            display: table-header-group !important;
        }

        th:nth-child(2),
        th:nth-child(3),
        th:nth-child(4),
        td:nth-child(2),
        td:nth-child(3),
        td:nth-child(4) {
            width: 20mm !important;
            text-align: right !important;
        }

        /* Hierarchical levels styling */
        #treeBody tr[data-level="1"] td:first-child {
            font-weight: 700;
            padding-left: 0 !important;
            background-color: #e8f0fe !important;
        }
        #treeBody tr[data-level="2"] td:first-child {
            font-weight: 600;
            padding-left: 15mm !important;
            background-color: #f1f8ff !important;
        }
        #treeBody tr[data-level="3"] td:first-child {
            font-weight: normal;
            padding-left: 30mm !important;
            background-color: transparent !important;
        }

        /* Prevent breaking totals */
        tfoot tr, tfoot td, tfoot th {
            page-break-inside: avoid !important;
        }

        /* Show only one grand total */
        tr.grand-total:not(:last-of-type) {
            display: none !important;
        }

        /* Hide UI-only elements */
        .no-print {
            display: none !important;
        }

        @page {
            size: A4 portrait;
            margin: 5mm 10mm 10mm 10mm;
        }
    }

    /* Screen styles for hierarchy toggle */
    .tree-toggle {
        cursor: pointer;
        user-select: none;
    }

    [data-level="1"] .tree-toggle::before,
    [data-level="2"] .tree-toggle::before {
        content: "▶";
        display: inline-block;
        margin-right: 8px;
        transition: transform 0.2s;
    }

    [data-level="1"].open .tree-toggle::before,
    [data-level="2"].open .tree-toggle::before {
        transform: rotate(90deg);
        content: "▼";
    }

    [data-level="1"] td {
        font-weight: 700;
        background-color: #e8f0fe;
    }

    [data-level="2"] td {
        padding-left: 15mm;
        background-color: #f1f8ff;
        font-weight: 600;
    }

    [data-level="3"] td {
        padding-left: 30mm;
    }
</style>

<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30 m-3 p-4">
                    <!-- Search + Buttons -->
                    <div class="d-flex justify-content-end align-items-center mb-3 no-print flex-wrap gap-2">

                        <button id="toggleExpandBtn" class="btn btn-outline-secondary me-2" title="Expand/Collapse All">
                            <i class="fa fa-expand" aria-hidden="true"></i>
                        </button>

                        <div class="box_right d-flex lms_block me-2">
                            <div class="serach_field_2">
                                <div class="search_inner">
                                    <form action="#" onsubmit="event.preventDefault();">
                                        <div class="search_field">
                                            <input type="text" id="searchInput" class="searchBox" placeholder="Search by code or description...">
                                        </div>
                                        <button type="submit"><i class="ti-search"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary" onclick="printAllItems()">Print Report</button>
                        <button class="btn btn-danger ms-2" id="exportPdfBtn">PDF</button>
                    </div>

                    <div id="printableTable" class="table-responsive">
                        <h2 id="printTitle" class="text-center d-none d-print-block">Stock Value Report</h2>
                        <table class="table table-bordered table-hover align-middle tree-table">
                            <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th class="text-end">Unit Cost</th>
                                <th class="text-end">Stock Balance</th>
                                <th class="text-end">Total Stock Value</th>
                            </tr>
                            </thead>
                            <tbody id="treeBody"></tbody>
                            <tfoot>
                            <tr>
                                <th colspan="3" class="text-end" style="color: #e74c3c; font-size: 1.25rem;">Grand Total:</th>
                                <th class="text-end" id="grandTotal" style="color: #e74c3c; font-size: 1.25rem;">0.00</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>

    let itemsData = [];
    let filteredData = [];

    // Load data, then display first page
    function loadStockValueReport() {
        fetch('/api/stock-value-report')
            .then(response => response.json())
            .then(data => {
                itemsData = data;
                filteredData = [...itemsData];
                renderTreeGroupedReport(filteredData);
                //renderTreeGroupedReport(filteredData, true); // always auto-expand
            })
            .catch(error => console.error('Error loading report:', error));
    }

    // Filter items by search query
    function applySearchFilter(query) {
        const lowerQuery = query.toLowerCase();

        // Step 1: Filter matching items
        filteredData = itemsData.filter(item =>
            item.code?.toLowerCase().includes(lowerQuery) ||
            item.description?.toLowerCase().includes(lowerQuery)
        );

        // Step 2: Re-render the table with only matching items + their hierarchy
        renderTreeGroupedReport(filteredData, true); // Pass true to auto-expand
    }


    // Print all filtered items (ignoring pagination)
    function printAllItems() {
        // Expand all rows before print
        document.querySelectorAll('.tree-parent').forEach(row => row.classList.add('open'));
        document.querySelectorAll('[data-level="2"], [data-level="3"]').forEach(row => row.classList.remove('d-none'));

        setTimeout(() => {
            window.print();
        }, 200);
    }

    // PDF Export
    document.getElementById('exportPdfBtn').addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'pt', 'a4');
        doc.setFontSize(18);
        doc.text('Stock Value Report', 40, 40);

        const body = [];

        const grouped = {}; // rebuild grouping
        filteredData.forEach(item => {
            if (!grouped[item.category_id]) {
                grouped[item.category_id] = {
                    name: item.category_name,
                    subcategories: {},
                    total: 0
                };
            }

            if (!grouped[item.category_id].subcategories[item.sub_category_id]) {
                grouped[item.category_id].subcategories[item.sub_category_id] = {
                    name: item.sub_category_name,
                    items: [],
                    total: 0
                };
            }

            grouped[item.category_id].subcategories[item.sub_category_id].items.push(item);
            grouped[item.category_id].subcategories[item.sub_category_id].total += parseFloat(item.total_stock_value);
            grouped[item.category_id].total += parseFloat(item.total_stock_value);
        });

        let grandTotal = 0;

        for (const catId in grouped) {
            const category = grouped[catId];
            body.push([
                { content: category.name, colSpan: 4, styles: { fontStyle: 'bold', fillColor: [232, 240, 254] } },
                { content: category.total.toFixed(2), styles: { halign: 'right', fontStyle: 'bold', textColor: [231, 76, 60] } }
            ]);

            for (const subCatId in category.subcategories) {
                const subCat = category.subcategories[subCatId];
                body.push([
                    { content: '  ' + subCat.name, colSpan: 4, styles: { fontStyle: 'bold', fillColor: [241, 248, 255] } },
                    { content: subCat.total.toFixed(2), styles: { halign: 'right', textColor: [231, 76, 60] } }
                ]);

                subCat.items.forEach(item => {
                    body.push([
                        { content: '    ' + item.code + ' - ' + item.description },
                        { content: parseFloat(item.average_cost).toFixed(2), styles: { halign: 'right' } },
                        { content: parseFloat(item.stock_balance).toFixed(2), styles: { halign: 'right' } },
                        { content: '', styles: { halign: 'right' } },
                        { content: parseFloat(item.total_stock_value).toFixed(2), styles: { halign: 'right' } }
                    ]);
                    grandTotal += parseFloat(item.total_stock_value);
                });
            }
        }

        // Grand Total row
        body.push([
            { content: 'Grand Total', colSpan: 4, styles: { fontStyle: 'bold', textColor: [231, 76, 60] } },
            { content: grandTotal.toFixed(2), styles: { halign: 'right', fontStyle: 'bold', textColor: [231, 76, 60] } }
        ]);

        doc.autoTable({
            startY: 60,
            head: [['Name', 'Unit Cost', 'Stock Balance', '', 'Total Stock Value']],
            body: body,
            theme: 'grid',
            styles: { fontSize: 9 },
            headStyles: { fillColor: [9, 132, 227], textColor: 255 },
            columnStyles: {
                1: { halign: 'right' },
                2: { halign: 'right' },
                4: { halign: 'right' }
            }
        });

        doc.save('stock_value_report.pdf');
    });

    // Init listeners
    document.addEventListener('DOMContentLoaded', () => {
        loadStockValueReport();

        document.getElementById('searchInput').addEventListener('input', function () {
            applySearchFilter(this.value);
        });
    });

    function renderTreeGroupedReport(data, autoExpand = false) {
        const tbody = document.getElementById('treeBody');
        tbody.innerHTML = '';
        let grandTotal = 0;

        const grouped = {};

        data.forEach(item => {
            if (!grouped[item.category_id]) {
                grouped[item.category_id] = {
                    name: item.category_name,
                    subcategories: {},
                    total: 0
                };
            }

            if (!grouped[item.category_id].subcategories[item.sub_category_id]) {
                grouped[item.category_id].subcategories[item.sub_category_id] = {
                    name: item.sub_category_name,
                    items: [],
                    total: 0
                };
            }

            grouped[item.category_id].subcategories[item.sub_category_id].items.push(item);
            grouped[item.category_id].subcategories[item.sub_category_id].total += parseFloat(item.total_stock_value);
            grouped[item.category_id].total += parseFloat(item.total_stock_value);
            grandTotal += parseFloat(item.total_stock_value);
        });

        for (const catId in grouped) {
            const category = grouped[catId];
            const catRowId = `cat-${catId}`;
            const catTr = document.createElement('tr');
            catTr.setAttribute('data-level', '1');
            catTr.setAttribute('data-id', catRowId);
            catTr.classList.add('tree-parent');
            if (autoExpand) catTr.classList.add('open');

            catTr.innerHTML = `
            <td class="tree-toggle">${category.name}</td>
            <td></td><td></td>
            <td class="text-end text-danger fw-bold">${category.total.toFixed(2)}</td>
        `;
            tbody.appendChild(catTr);

            for (const subCatId in category.subcategories) {
                const subCat = category.subcategories[subCatId];
                const subCatRowId = `sub-${subCatId}`;
                const subTr = document.createElement('tr');
                subTr.setAttribute('data-level', '2');
                subTr.setAttribute('data-id', subCatRowId);
                subTr.setAttribute('data-parent', catRowId);
                subTr.classList.add('tree-parent');
                if (!autoExpand) subTr.classList.add('d-none');
                else subTr.classList.add('open');

                subTr.innerHTML = `
                <td class="tree-toggle">${subCat.name}</td>
                <td></td><td></td>
                <td class="text-end text-danger">${subCat.total.toFixed(2)}</td>
            `;
                tbody.appendChild(subTr);

                subCat.items.forEach(item => {
                    const itemTr = document.createElement('tr');
                    itemTr.setAttribute('data-level', '3');
                    itemTr.setAttribute('data-parent', subCatRowId);
                    if (!autoExpand) itemTr.classList.add('d-none');

                    itemTr.innerHTML = `
                    <td>${item.code} - ${item.description}</td>
                    <td class="text-end">${parseFloat(item.average_cost).toFixed(2)}</td>
                    <td class="text-end">${parseFloat(item.stock_balance).toFixed(2)}</td>
                    <td class="text-end">${parseFloat(item.total_stock_value).toFixed(2)}</td>
                `;
                    tbody.appendChild(itemTr);
                });
            }
        }

        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);

        tbody.querySelectorAll('.tree-parent .tree-toggle').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const parentRow = toggle.closest('tr');
                const id = parentRow.getAttribute('data-id');
                const nextLevel = parseInt(parentRow.getAttribute('data-level')) + 1;

                const isOpen = parentRow.classList.toggle('open');

                tbody.querySelectorAll(`[data-parent="${id}"]`).forEach(row => {
                    row.classList.toggle('d-none', !isOpen);

                    if (!isOpen) {
                        row.classList.remove('open');
                        const childId = row.getAttribute('data-id');
                        if (childId) {
                            tbody.querySelectorAll(`[data-parent="${childId}"]`).forEach(sub => {
                                sub.classList.add('d-none');
                            });
                        }
                    }
                });
            });
        });
    }

    const toggleExpandBtn = document.getElementById('toggleExpandBtn');
    let isExpanded = false;

    toggleExpandBtn.addEventListener('click', () => {
        const tbody = document.getElementById('treeBody');
        if (!isExpanded) {
            // Expand all
            tbody.querySelectorAll('.tree-parent').forEach(row => row.classList.add('open'));
            tbody.querySelectorAll('[data-level="2"], [data-level="3"]').forEach(row => row.classList.remove('d-none'));
            // Change icon to "compress"
            toggleExpandBtn.querySelector('i').classList.remove('fa-expand');
            toggleExpandBtn.querySelector('i').classList.add('fa-compress');
            isExpanded = true;
        } else {
            // Collapse all to only level 1 visible
            tbody.querySelectorAll('.tree-parent').forEach(row => row.classList.remove('open'));
            tbody.querySelectorAll('[data-level="2"], [data-level="3"]').forEach(row => row.classList.add('d-none'));
            // Change icon to "expand"
            toggleExpandBtn.querySelector('i').classList.remove('fa-compress');
            toggleExpandBtn.querySelector('i').classList.add('fa-expand');
            isExpanded = false;
        }
    });

</script>
