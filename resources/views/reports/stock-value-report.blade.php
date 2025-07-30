@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Stock Value Report'])

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printableTable, #printableTable * {
            visibility: visible;
        }
        #printableTable {
            background: white;
            position: static;
            width: 100%;
            padding: 0;
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
            display: table-header-group;
        }
        tfoot {
            display: table-footer-group;
        }
        @page {
            size: A4 portrait;
            margin: 5mm 10mm 10mm 10mm;
        }
        #printTitle {
            display: none;
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        @media print {
            #printTitle {
                display: block;
            }
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
                        <h2 id="printTitle" class="text-center d-none d-print-block">Stock Value Report</h2>
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                            <tr>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th class="text-end">Unit Cost</th>
                                <th class="text-end">Stock Balance</th>
                                <th class="text-end">Total Stock Value</th>
                            </tr>
                            </thead>
                            <tbody id="itemTable"></tbody>
                            <tfoot>
                            <tr>
                                <th colspan="4" class="text-end" style="color: #ed0303; font-size: 1.25rem;">Total:</th>
                                <th class="text-end" id="totalValueCell" style="color: #ed0303; font-size: 1.25rem;">0.00</th>
                            </tr>
                            </tfoot>
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

    // Load data, then display first page
    function loadStockValueReport() {
        fetch('/api/stock-value-report')
            .then(response => response.json())
            .then(data => {
                itemsData = data;
                filteredData = [...itemsData];
                displayPage(currentPage);
            })
            .catch(error => console.error('Error loading report:', error));
    }

    // Display table rows for the current page
    function displayPage(page) {
        const tbody = document.getElementById('itemTable');
        tbody.innerHTML = '';

        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedItems = filteredData.slice(start, end);

        let totalValue = 0;

        paginatedItems.forEach(item => {
            const averageCost = Number(item.average_cost) || 0;
            const stockBalance = Number(item.stock_balance) || 0;
            const totalStockValue = Number(item.total_stock_value) || 0;

            totalValue += totalStockValue;

            const tr = `
            <tr>
                <td>${item.code}</td>
                <td>${item.description}</td>
                <td class="text-end">${averageCost.toFixed(2)}</td>
                <td class="text-end">${stockBalance.toFixed(2)}</td>
                <td class="text-end">${totalStockValue.toFixed(2)}</td>
            </tr>
        `;
            tbody.insertAdjacentHTML('beforeend', tr);
        });

        // Update total in tfoot
        document.getElementById('totalValueCell').textContent = totalValue.toFixed(2);

        renderPagination();
    }

    // Render pagination buttons
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

    // Filter items by search query
    function applySearchFilter(query) {
        const lowerQuery = query.toLowerCase();

        filteredData = itemsData.filter(item =>
            item.code?.toLowerCase().includes(lowerQuery) ||
            item.description?.toLowerCase().includes(lowerQuery)
        );

        currentPage = 1;
        displayPage(currentPage);
    }

    // Print all filtered items (ignoring pagination)
    function printAllItems() {
        const tbody = document.getElementById('itemTable');
        tbody.innerHTML = '';

        let totalValue = 0;

        filteredData.forEach(item => {
            const averageCost = Number(item.average_cost) || 0;
            const stockBalance = Number(item.stock_balance) || 0;
            const totalStockValue = Number(item.total_stock_value) || 0;

            totalValue += totalStockValue;

            const tr = `
            <tr>
                <td>${item.code}</td>
                <td>${item.description}</td>
                <td class="text-end">${averageCost.toFixed(2)}</td>
                <td class="text-end">${stockBalance.toFixed(2)}</td>
                <td class="text-end">${totalStockValue.toFixed(2)}</td>
            </tr>
        `;
            tbody.insertAdjacentHTML('beforeend', tr);
        });

        document.getElementById('totalValueCell').textContent = totalValue.toFixed(2);

        setTimeout(() => {
            window.print();
            displayPage(currentPage);
        }, 200);
    }

    // PDF Export
    document.getElementById('exportPdfBtn').addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'pt', 'a4');

        doc.setFontSize(18);
        doc.text('Stock Value Report', 40, 40);

        const headers = [['Item Code', 'Description', 'Average Cost', 'Stock Balance', 'Total Stock Value']];
        const rows = filteredData.map(item => [
            item.code,
            item.description,
            parseFloat(item.average_cost).toFixed(2),
            parseFloat(item.stock_balance).toFixed(2),
            parseFloat(item.total_stock_value).toFixed(2)
        ]);

        // Calculate Total Stock Value
        const totalValue = filteredData.reduce((sum, item) => sum + parseFloat(item.total_stock_value), 0);

        // Add Total row
        rows.push([
            '', '', '', 'Total:',
            { content: totalValue.toFixed(2), styles: { fontStyle: 'bold', textColor: '#e74c3c' } }
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

        doc.save('stock_value_report.pdf');
    });

    // Init listeners
    document.addEventListener('DOMContentLoaded', () => {
        loadStockValueReport();

        document.getElementById('searchInput').addEventListener('input', function () {
            applySearchFilter(this.value);
        });
    });
</script>
