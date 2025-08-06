@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Stock Summary Report'])

<style>
    @media print {
        body * {
            visibility: hidden !important;
        }

        #printableTable, #printableTable * {
            visibility: visible !important;
        }

        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
            overflow: visible !important;
        }

        #printableTable {
            position: static !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
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
            page-break-inside: avoid !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 11pt;
            color: #000;
        }

        th, td {
            padding: 4pt 6pt !important;
            text-align: left !important;
        }

        thead {
            display: table-header-group !important;
        }

        .no-print {
            display: none !important;
        }

        .main_content_iner,
        .container-fluid,
        .row,
        .col-12,
        .white_card {
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        @page {
            size: A4 portrait;
            margin: 10mm 15mm 10mm 15mm;
        }
    }

</style>

<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30 m-3 p-4">
                    <!-- Search + Buttons -->
                    <div class="d-flex justify-content-end align-items-center mb-3 no-print flex-wrap gap-2">
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

                    <!-- Printable Table -->
                    <div id="printableTable" class="table-responsive">
                        <h2 id="printTitle" class="text-center d-none d-print-block">Stock Summary Report</h2>
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                            <tr>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                                <th class="text-end">Stock Balance</th>
                            </tr>
                            </thead>
                            <tbody id="itemTable"></tbody>
                        </table>
                    </div>

                    <!-- Pagination Controls -->
                    <nav aria-label="Page navigation" class="mt-3 no-print">
                        <ul class="pagination justify-content-end" id="pagination"></ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
    const rowsPerPage = 14;
    let currentPage = 1;
    let itemsData = [];
    let filteredData = [];

    function loadStockSummaryReport() {
        fetch('/api/stock-value-report')
            .then(response => response.json())
            .then(data => {
                itemsData = data.filter(item => Number(item.stock_balance) > 0);
                filteredData = [...itemsData];
                displayPage(currentPage);
            })
            .catch(error => console.error('Error loading report:', error));
    }

    function displayPage(page) {
        const tbody = document.getElementById('itemTable');
        tbody.innerHTML = '';

        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedItems = filteredData.slice(start, end);

        paginatedItems.forEach(item => {
            const debit = Number(item.debit) || 0;
            const credit = Number(item.credit) || 0;
            const stockBalance = Number(item.stock_balance) || 0;

            const tr = `
            <tr>
                <td>${item.code}</td>
                <td>${item.description}</td>
                <td class="text-end">${debit.toFixed(2)}</td>
                <td class="text-end">${credit.toFixed(2)}</td>
                <td class="text-end">${stockBalance.toFixed(2)}</td>
            </tr>
        `;
            tbody.insertAdjacentHTML('beforeend', tr);
        });

        renderPagination();
    }

    function renderPagination() {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        const pageCount = Math.ceil(filteredData.length / rowsPerPage);
        if (pageCount <= 1) return;

        for (let i = 1; i <= pageCount; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click', e => {
                e.preventDefault();
                currentPage = i;
                displayPage(currentPage);
            });
            pagination.appendChild(li);
        }
    }

    function applySearchFilter(query) {
        const lowerQuery = query.toLowerCase();

        filteredData = itemsData.filter(item =>
            item.code?.toLowerCase().includes(lowerQuery) ||
            item.description?.toLowerCase().includes(lowerQuery)
        );

        currentPage = 1;
        displayPage(currentPage);
    }

    function printAllItems() {
        const tbody = document.getElementById('itemTable');
        tbody.innerHTML = '';

        filteredData.forEach(item => {
            const debit = Number(item.debit) || 0;
            const credit = Number(item.credit) || 0;
            const stockBalance = Number(item.stock_balance) || 0;

            const tr = `
                <tr>
                    <td>${item.code}</td>
                    <td>${item.description}</td>
                    <td class="text-end">${debit.toFixed(2)}</td>
                    <td class="text-end">${credit.toFixed(2)}</td>
                    <td class="text-end">${stockBalance.toFixed(2)}</td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', tr);
        });

        setTimeout(() => {
            window.print();
            displayPage(currentPage);
        }, 200);
    }

    document.getElementById('exportPdfBtn').addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'pt', 'a4');

        doc.setFontSize(18);
        doc.text('Stock Summary Report', 40, 40);

        const headers = [['Item Code', 'Description', 'Debit', 'Credit', 'Stock Balance']];
        const rows = filteredData.map(item => [
            item.code,
            item.description,
            parseFloat(item.debit).toFixed(2),
            parseFloat(item.credit).toFixed(2),
            parseFloat(item.stock_balance).toFixed(2)
        ]);

        doc.autoTable({
            head: headers,
            body: rows,
            startY: 60,
            theme: 'grid',
            headStyles: { fillColor: '#0984e3', textColor: '#ffffff', fontStyle: 'bold' },
            columnStyles: {
                2: { halign: 'right' },
                3: { halign: 'right' },
                4: { halign: 'right' },
            },
            margin: { left: 40, right: 40 },
            didDrawPage: (data) => {
                const pageCount = doc.internal.getNumberOfPages();
                doc.setFontSize(10);
                doc.setTextColor('#636e72');
                doc.text(`Page ${data.pageNumber} of ${pageCount}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
            }
        });

        doc.save('stock_summary_report.pdf');
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadStockSummaryReport();

        document.getElementById('searchInput').addEventListener('input', function () {
            applySearchFilter(this.value);
        });
    });
</script>
