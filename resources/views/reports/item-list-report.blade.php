@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Item List Report'])

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
            left: 18px !important;
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

        tr {
            page-break-inside: avoid !important;
        }

        .no-print {
            display: none !important;
        }

        #printableTable table tr th:nth-child(3),
        #printableTable table tr th:nth-child(4),
        #printableTable table tr th:nth-child(5),
        #printableTable table tr td:nth-child(3),
        #printableTable table tr td:nth-child(4),
        #printableTable table tr td:nth-child(5) {
            text-align: center !important;
        }

        @page {
            size: A4 portrait;
            margin: 10mm 15mm 10mm 15mm;
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
    }
</style>

<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30 m-3 p-4">
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

                        <button class="btn btn-primary" onclick="printAllItems()">
                            Print Report
                        </button>

                        <button class="btn btn-danger" id="exportPdfBtn" style="margin-left: 10px;">
                            PDF
                        </button>
                    </div>

                    <!-- Printable Table with Pagination -->
                    <div id="printableTable" class="table-responsive">
                        <h2 id="printTitle" class="text-center d-none d-print-block">Item List Report</h2>
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                            <tr>
                                <th scope="col">Code</th>
                                <th scope="col">Description</th>
                                <th>List Price</th>
                                <th>Retail Price</th>
                                <th>Wholesale Price</th>
                            </tr>
                            </thead>
                            <tbody id="itemTable">
                            <!-- Dynamic rows will be injected here -->
                            </tbody>
                        </table>

                        <!-- Pagination Controls -->
                        <nav aria-label="Page navigation" class="mt-3 no-print">
                            <ul class="pagination justify-content-end" id="pagination">
                                <!-- Pagination buttons will be generated here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<script src="{{ asset('assets/js/jspdf.umd.min.js') }}"></script>
<script src="{{ asset('assets/js/jspdf.plugin.autotable.min.js') }}"></script>

<script>
    const rowsPerPage = 14;
    let currentPage = 1;
    let itemsData = [];
    let filteredData = [];

    function displayPage(page) {
        const tbody = document.getElementById('itemTable');
        tbody.innerHTML = '';

        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedItems = filteredData.slice(start, end);

        paginatedItems.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.code}</td>
                <td>${item.description}</td>
                <td>${item.list_price ?? '0.00'}</td>
                <td>${item.retail_price ?? '0.00'}</td>
                <td>${item.wholesale_price ?? '0.00'}</td>
            `;
            tbody.appendChild(tr);
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
            li.addEventListener('click', function(e) {
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

    function loadReportItems() {
        fetch('/api/items')
            .then(response => response.json())
            .then(data => {
                itemsData = data;
                filteredData = [...itemsData]; // Initialize filtered data
                displayPage(currentPage);
            })
            .catch(err => console.error('Error loading items:', err));
    }

    //Print
    function printAllItems() {
        const tbody = document.getElementById('itemTable');
        tbody.innerHTML = '';

        itemsData.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.code}</td>
                <td>${item.description}</td>
                <td>${item.list_price ?? '0.00'}</td>
                <td>${item.retail_price ?? '0.00'}</td>
                <td>${item.wholesale_price ?? '0.00'}</td>
            `;
            tbody.appendChild(tr);
        });

        setTimeout(() => {
            window.print();
            displayPage(currentPage); // Restore paginated view after printing
        }, 200);
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadReportItems();

        document.getElementById('searchInput').addEventListener('input', function () {
            applySearchFilter(this.value);
        });
    });

    //PDF
    document.getElementById('exportPdfBtn').addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'pt', 'a4');

        // Title styling
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(18);
        doc.setTextColor('#2d3436');
        doc.text('Item List Report', 40, 40);

        // Prepare table data
        const headers = [['Code', 'Description', 'List Price', 'Retail Price', 'Wholesale Price']];
        const data = itemsData.map(item => [
            item.code || '',
            item.description || '',
            item.list_price ?? '0.00',
            item.retail_price ?? '0.00',
            item.wholesale_price ?? '0.00'
        ]);

        // Draw table
        doc.autoTable({
            head: headers,
            body: data,
            startY: 60,
            styles: {
                font: 'helvetica',
                fontSize: 10,
                textColor: '#2d3436',
                cellPadding: 5,
            },
            headStyles: {
                fillColor: '#6c5ce7', // purple header background
                textColor: '#ffffff',
                fontStyle: 'bold',
                halign: 'center',
            },
            bodyStyles: {
                fillColor: [245, 246, 250],
            },
            alternateRowStyles: {
                fillColor: [255, 255, 255],
            },
            columnStyles: {
                0: { halign: 'center' },
                2: { halign: 'right' },
                3: { halign: 'right' },
                4: { halign: 'right' },
            },
            margin: { left: 40, right: 40 },
            didDrawPage: (data) => {
                // Footer with page number
                let pageCount = doc.internal.getNumberOfPages();
                doc.setFontSize(10);
                doc.setTextColor('#636e72');
                doc.text(`Page ${data.pageNumber} of ${pageCount}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
            }
        });

        doc.save('item_list_report.pdf');
    });

</script>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
