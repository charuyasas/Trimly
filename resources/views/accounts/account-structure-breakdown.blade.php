@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Account Structure Breakdown'])

<style>
    @media print {
        body * { visibility: hidden !important; }
        #printableTable, #printableTable * { visibility: visible !important; }
        #printableTable { position: static !important; width: 100% !important; }
        #printTitle { display: block !important; text-align: center; font-size: 20pt; font-weight: bold; margin-bottom: 10pt; }
        table { border-collapse: collapse !important; width: 100% !important; font-size: 11pt; color: #000; table-layout: fixed; }
        th, td { padding: 4pt 6pt !important; vertical-align: top; }
        thead { display: table-header-group !important; }
    }

    .tree-toggle {
        cursor: pointer;
        user-select: none;
        display: inline-flex;
        align-items: center;
    }
    .tree-toggle::before {
        content: "▶";
        display: inline-block;
        margin-right: 8px;
        transition: transform 0.2s;
    }
    tr.open > td > .tree-toggle::before {
        transform: rotate(90deg);
        content: "▼";
    }

    /* Level styling */
    [data-level="1"] td { font-weight: 700; background-color: #e8f0fe; }
    [data-level="2"] td:nth-child(1),
    [data-level="2"] td:nth-child(2) { padding-left: 15mm; font-weight: 600; background-color: #f1f8ff; }
    [data-level="3"] td:nth-child(1),
    [data-level="3"] td:nth-child(2) { padding-left: 30mm; font-weight: normal; }
    [data-level="4"] td:nth-child(1),
    [data-level="4"] td:nth-child(2) { padding-left: 45mm; font-weight: normal; }

    /* Fixed column widths */
    .tree-table {
        table-layout: fixed;
        width: 100%;
    }
    .tree-table th:nth-child(1), .tree-table td:nth-child(1) { width: 50%; }
    .tree-table th:nth-child(2), .tree-table td:nth-child(2) { width: 30%; }
    .tree-table th:nth-child(3), .tree-table td:nth-child(3) {
        width: 20%;
        padding-left: 0 !important; /* No indent for Ledger Code column */
    }
</style>

<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-8">
                <div class="white_card card_height_100 mb_30 m-3 p-4">

                    <div class="d-flex justify-content-end mb-3 no-print flex-wrap gap-2">
                        <button id="toggleExpandBtn" class="btn btn-outline-secondary me-2" title="Expand/Collapse All">
                            <i class="fa fa-expand"></i>
                        </button>
                        <div class="search_inner">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by code or name...">
                        </div>
                        <button class="btn btn-primary" onclick="printAllItems()">Print Report</button>
                    </div>

                    <div id="printableTable" class="table-responsive">
                        <h2 id="printTitle" class="text-center d-none d-print-block">Account Structure Breakdown</h2>
                        <table class="table table-bordered table-hover align-middle tree-table">
                            <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Ledger Code</th>
                            </tr>
                            </thead>
                            <tbody id="treeBody"></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<script>
    let accountData = [];

    function loadAccountStructure() {
        fetch('/api/get-account-structure-breakdown')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    accountData = res.data;
                    renderTree(accountData);
                } else {
                    console.error('Error fetching account structure:', res.message);
                }
            }).catch(err => console.error('Error fetching account structure:', err));
    }

    function renderTree(data, autoExpand = false) {
        const tbody = document.getElementById('treeBody');
        tbody.innerHTML = '';

        data.forEach(main => {
            const mainTr = makeRow(1, 'main-' + main.main_code, '', main.main_account, main.main_code, '', true, autoExpand);
            tbody.appendChild(mainTr);

            (main.heading_accounts || []).forEach(heading => {
                const headingTr = makeRow(2, 'heading-' + heading.heading_code, mainTr.dataset.id, heading.heading_account, heading.heading_code, '', true, autoExpand);
                tbody.appendChild(headingTr);

                (heading.title_accounts || []).forEach(title => {
                    const titleTr = makeRow(3, 'title-' + title.title_code, headingTr.dataset.id, title.title_account, title.title_code, '', true, autoExpand);
                    tbody.appendChild(titleTr);

                    (title.posting_accounts || []).forEach(posting => {
                        const postingTr = makeRow(4, '', titleTr.dataset.id, posting.posting_account, posting.posting_code, posting.ledger_code, false, autoExpand);
                        tbody.appendChild(postingTr);
                    });
                });
            });
        });

        tbody.querySelectorAll('.tree-toggle').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const row = toggle.closest('tr');
                const isOpen = row.classList.toggle('open');
                toggleChildrenRecursive(row.dataset.id, isOpen);
            });
        });
    }

    function makeRow(level, id, parentId, name, code, ledger, hasToggle, autoExpand) {
        const tr = document.createElement('tr');
        tr.dataset.level = level;
        if (id) tr.dataset.id = id;
        if (parentId) tr.dataset.parent = parentId;
        if (!autoExpand && level > 1) tr.classList.add('d-none');
        if (autoExpand && hasToggle) tr.classList.add('open');
        if (hasToggle) tr.classList.add('tree-parent');

        tr.innerHTML = `
        <td>${hasToggle ? `<span class="tree-toggle">${name || ''}</span>` : name || ''}</td>
        <td>${code || ''}</td>
        <td>${ledger || ''}</td>
    `;
        return tr;
    }

    function toggleChildrenRecursive(parentId, show) {
        const children = document.querySelectorAll(`[data-parent="${parentId}"]`);
        children.forEach(child => {
            child.classList.toggle('d-none', !show);
            if (!show) {
                child.classList.remove('open');
            }
            if (child.classList.contains('tree-parent')) {
                toggleChildrenRecursive(child.dataset.id, show && child.classList.contains('open'));
            }
        });
    }

    document.getElementById('toggleExpandBtn').addEventListener('click', () => {
        const tbody = document.getElementById('treeBody');
        const isExpanded = tbody.querySelector('[data-level="2"]:not(.d-none)') !== null;
        if (!isExpanded) {
            tbody.querySelectorAll('.tree-parent').forEach(row => row.classList.add('open'));
            tbody.querySelectorAll('tr').forEach(row => row.classList.remove('d-none'));
            toggleExpandBtn.querySelector('i').classList.replace('fa-expand', 'fa-compress');
        } else {
            tbody.querySelectorAll('.tree-parent').forEach(row => row.classList.remove('open'));
            tbody.querySelectorAll('tr').forEach(row => {
                if (row.dataset.level > 1) row.classList.add('d-none');
            });
            toggleExpandBtn.querySelector('i').classList.replace('fa-compress', 'fa-expand');
        }
    });

    document.getElementById('searchInput').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        if (!query) {
            renderTree(accountData, false);
            return;
        }

        const filtered = accountData.map(main => {
            const m = { ...main };
            m.heading_accounts = (main.heading_accounts || []).map(heading => {
                const h = { ...heading };
                h.title_accounts = (heading.title_accounts || []).map(title => {
                    const t = { ...title };
                    t.posting_accounts = (title.posting_accounts || []).filter(p =>
                        (p.posting_account || '').toLowerCase().includes(query) ||
                        (p.posting_code || '').toString().toLowerCase().includes(query) ||
                        (p.ledger_code || '').toLowerCase().includes(query)
                    );
                    return t;
                }).filter(t => t.posting_accounts.length > 0);
                return h;
            }).filter(h => h.title_accounts.length > 0);
            return m;
        }).filter(m => m.heading_accounts.length > 0);

        renderTree(filtered, true);
    });

    function printAllItems() {
        document.querySelectorAll('.tree-parent').forEach(row => row.classList.add('open'));
        document.querySelectorAll('tr').forEach(row => row.classList.remove('d-none'));
        setTimeout(() => window.print(), 200);
    }

    document.addEventListener('DOMContentLoaded', loadAccountStructure);
</script>
