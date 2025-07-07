@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'GRN'])

<style>
    .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        background: #f8f9fa;
        border-bottom: 1px solid #eaeaea;
        border-radius: 16px 16px 0 0;
        padding: 1.5rem;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .step-tabs {
        background: #f8f9fa;
        border-radius: 50px;
        padding: 0.5rem;
        gap: 0.5rem;
        display: inline-flex;
    }

    .step-tabs .nav-link {
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .step-tabs .nav-link strong {
        background: #e9ecef;
        color: #6c757d;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        transition: all 0.3s ease;
    }

    .step-tabs .nav-link.active {
        background: #0d6efd;
        color: white;
    }

    .step-tabs .nav-link.active strong {
        background: white;
        color: #0d6efd;
    }

    .table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    }

    .table thead th {
        background: #f8f9fa;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #dee2e6;
        padding: 1rem;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .btn {
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .border {
        border-radius: 12px;
        border: 1px solid #dee2e6 !important;
    }

    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        margin-top: 0.25em;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .totals-section p {
        margin-bottom: 0.5rem;
        color: #6c757d;
    }

    .text-danger {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

</style>

<!-- Main Content -->
<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12 mb-3 d-flex justify-content-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#grnModal" onclick="openAddGRNModal()">Add New GRN</button>
            </div>
        </div>
    </div>
</div>

<!-- GRN Modal -->
<div class="modal fade" id="grnModal" tabindex="-1" aria-labelledby="grnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90%; height: 90vh;">
        <div class="modal-content" style="height: 100%; overflow-y: auto;">
            <form id="grnForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add GRN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Step Tabs -->
                    <div class="mb-4 d-flex justify-content-center">
                        <ul class="nav nav-pills step-tabs" id="grnTabs">
                            <li class="nav-item">
                                <a class="nav-link active disabled" data-target="#grnStep1" onclick="event.preventDefault();">
                                    <strong>1</strong> Header Section
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" data-target="#grnStep2" onclick="event.preventDefault();">
                                    <strong>2</strong> Item Entry Section
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" data-target="#grnStep3" onclick="event.preventDefault();">
                                    <strong>3</strong> Finalization Section
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content px-3">
                        <!-- STEP 1 - HEADER -->
                        <div class="tab-pane fade show active" id="grnStep1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>GRN Number</label>
                                    <input type="text" class="form-control" id="grn_number" >
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>GRN Date</label>
                                    <input type="date" class="form-control" id="grn_date" value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Supplier</label>
                                    <select id="supplier_id" class="form-control"></select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Supplier Invoice Number</label>
                                    <input type="text" class="form-control" id="supplier_invoice_number">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label>GRN Type</label>
                                    <div class="d-flex gap-3 mt-1">
                                        <div class="form-check form-check-inline border rounded p-2 px-4">
                                            <input class="form-check-input" type="radio" name="grn_type" id="type_margin" value="Profit Margin" checked>
                                            <label class="form-check-label fw-bold" for="type_margin">Profit Margin</label>
                                        </div>
                                        <div class="form-check form-check-inline border rounded p-2 px-4">
                                            <input class="form-check-input" type="radio" name="grn_type" id="type_discount" value="Discount Based">
                                            <label class="form-check-label fw-bold" for="type_discount">Discount Based</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary" onclick="goToStep(2)">NEXT</button>
                            </div>
                        </div>

                        <!-- STEP 2 – ITEM ENTRY SECTION -->
                        <div class="tab-pane fade" id="grnStep2">
                            <!-- Item Inputs -->
                            <div class="border p-3 rounded mb-3">
                                <div class="row g-2 align-items-start">
                                    <div class="col-md-2">
                                        <input type="text" id="item_name" class="form-control" placeholder="Item Name">
                                    </div>
                                    <div class="col-md-1">
                                        <input type="number" id="qty" class="form-control" placeholder="Quantity">
                                        <small class="text-danger d-none" id="err_qty">Quantity is required</small>
                                    </div>
                                    <div class="col-md-1">
                                        <input type="number" id="foc" class="form-control" placeholder="FOC">
                                        <small class="text-danger d-none" id="err_foc">Free Of Charge Quantity is required</small>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" id="price" class="form-control" placeholder="Purchase Price">
                                        <small class="text-danger d-none" id="err_price">Purchase Price is required</small>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" id="margin" class="form-control" placeholder="Margin">
                                        <small class="text-danger d-none" id="err_margin">Margin is required</small>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" id="discount" class="form-control" placeholder="Discount">
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <button type="button" class="btn btn-outline-secondary" onclick="addItem()">+</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Item Table -->
                            <table class="table table-bordered text-center mb-4">
                                <thead class="table-light">
                                <tr>
                                    <th>Action</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>FOC</th>
                                    <th>Purchase Price</th>
                                    <th>Margin</th>
                                    <th>Discount</th>
                                    <th>Final Retail Price</th>
                                    <th>Sub Total</th>
                                </tr>
                                </thead>
                                <tbody id="itemTable">
                                <!-- Dynamic rows -->
                                </tbody>
                            </table>

                            <!-- Bill Discount Section -->
                            <div id="discountSection" class="mt-4 border rounded p-3 position-relative">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <strong>Add discount for bill value</strong>
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0"
                                            onclick="toggleDiscountSection()" title="Hide Section">
                                        &minus;
                                    </button>
                                </div>

                                <div class="form-check form-switch form-switch-lg d-flex align-items-center gap-2 mb-3">
                                    <input class="form-check-input custom-toggle" type="checkbox" id="is_percentage">
                                    <label class="form-check-label fs-6 fw-semibold" for="is_percentage">Is Percentage based discount</label>
                                </div>

                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <input type="number" class="form-control" id="discount_amount" placeholder="Discount Amount">
                                    <span id="discountUnitLabel">LKR</span>
                                </div>

                                <span id="err_discount" class="text-danger small d-none">Total Discount For Bill Value is required</span>

                                <div class="d-flex gap-2 mt-2">
                                    <button type="button" class="btn btn-success" onclick="applyDiscount()">✔</button>
                                    <button type="button" class="btn btn-danger" onclick="clearDiscount()">✖</button>
                                </div>
                            </div>

                            <!-- Total Summary -->
                            <div class="text-end pe-2">
                                <p>Total Before Discount: LKR <span id="totalBefore">0.00</span></p>
                                <p>Total FOC: LKR <span id="totalFOC">0.00</span></p>
                                <h5 class="text-danger fw-bold">Grand Total: LKR <span id="grandTotal">0.00</span></h5>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary" onclick="goToStep(3)">NEXT</button>
                            </div>
                        </div>

                        <!-- STEP 3 - FINALIZATION -->
                        <div class="tab-pane fade" id="grnStep3">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Store Location</label>
                                    <input type="text" id="store_location" class="form-control">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label>Note</label>
                                    <input type="text" id="note" class="form-control" placeholder="Optional notes">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" onclick="goToStep(2)">Back</button>
                                <button type="submit" class="btn btn-dark">Finalize GRN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('includes.footer')

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let items = [];
    let currentDiscount = { amount: 0, isPercentage: false };
    // Add this variable at the top with other state variables
    let editingItemIndex = null;

    // Initialize when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeForm();
        loadSuppliers();
    });

    function initializeForm() {
        // Initialize form submission
        document.getElementById('grnForm').addEventListener('submit', handleFormSubmit);

        // Initialize radio button listeners
        document.querySelectorAll('input[name="grn_type"]').forEach(radio => {
            radio.addEventListener('change', handleGrnTypeChange);
        });
    }

    function handleGrnTypeChange(event) {
        const isMarginBased = event.target.value === 'Profit Margin';
        document.getElementById('margin').disabled = !isMarginBased;
        document.getElementById('discount').disabled = isMarginBased;
    }

    async function loadSuppliers() {
        try {
            const response = await axios.get('/api/suppliers');
            const suppliers = response.data;
            const select = document.getElementById('supplier_id');

            suppliers.forEach(supplier => {
                const option = document.createElement('option');
                option.value = supplier.id;
                option.textContent = supplier.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading suppliers:', error);
            alert('Failed to load suppliers');
        }
    }

    function goToStep(step) {
        if (step === 2) {
            if (!validateStep1()) return;
            switchTab(2);
        } else if (step === 3) {
            if (!validateStep2()) return;
            switchTab(3);
        } else if (step === 1) {
            switchTab(1);
        }
    }

    function switchTab(step) {
        const tabs = document.querySelectorAll('#grnTabs .nav-link');
        const contents = document.querySelectorAll('.tab-pane');

        tabs.forEach((tab, index) => {
            if (index === step - 1) {
                tab.classList.remove('disabled');
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });

        contents.forEach((pane, index) => {
            if (index === step - 1) {
                pane.classList.add('show', 'active');
            } else {
                pane.classList.remove('show', 'active');
            }
        });
    }

    function validateStep1() {
        const required = ['grn_number', 'grn_date', 'supplier_id', 'supplier_invoice_number'];
        let valid = true;

        required.forEach(id => {
            const input = document.getElementById(id);
            if (!input.value) {
                input.classList.add('is-invalid');
                valid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        return valid;
    }

    function validateStep2() {
        if (items.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please add at least one item to continue.'
            });
            return false;
        }
        return true;
    }

    function validateItemInputs() {
        let isValid = true;
        const fields = ['qty', 'foc', 'price', 'margin'];

        fields.forEach(field => {
            const input = document.getElementById(field);
            const error = document.getElementById(`err_${field}`);

            if (!input.value) {
                error.classList.remove('d-none');
                isValid = false;
            } else {
                error.classList.add('d-none');
            }
        });

        return isValid;
    }

    function addItem() {
        if (!validateItemInputs()) return;

        const item = {
            name: document.getElementById('item_name').value,
            qty: parseFloat(document.getElementById('qty').value),
            foc: parseFloat(document.getElementById('foc').value),
            price: parseFloat(document.getElementById('price').value),
            margin: parseFloat(document.getElementById('margin').value),
            discount: parseFloat(document.getElementById('discount').value) || 0
        };

        // Calculate final price based on GRN type
        const isMarginBased = document.getElementById('type_margin').checked;
        if (isMarginBased) {
            item.finalPrice = item.price * (1 + item.margin / 100);
        } else {
            item.finalPrice = item.price * (1 - item.discount / 100);
        }

        item.subtotal = item.finalPrice * item.qty;
        items.push(item);

        updateItemsTable();
        clearItemInputs();
        calculateTotals();
    }

    function updateItemsTable() {
        const tbody = document.getElementById('itemTable');
        tbody.innerHTML = '';

        items.forEach((item, index) => {
            const row = tbody.insertRow();
            row.innerHTML = `
            <td>
                <a href="javascript:void(0)" onclick="editItem(${index})" class="text-primary">Edit</a>
                <span> | </span>
                <a href="javascript:void(0)" onclick="removeItem(${index})" class="text-danger">Delete</a>
            </td>
            <td>${item.name}</td>
            <td>${item.qty}</td>
            <td>${item.foc}</td>
            <td>${item.price.toFixed(2)}</td>
            <td>${item.margin}</td>
            <td>${item.discount}</td>
            <td>${item.finalPrice.toFixed(2)}</td>
            <td>${item.subtotal.toFixed(2)}</td>
        `;
        });
    }

    function calculateTotals() {
        const totalBefore = items.reduce((sum, item) => sum + item.subtotal, 0);
        const totalFOC = items.reduce((sum, item) => sum + (item.finalPrice * item.foc), 0);
        let grandTotal = totalBefore;

        if (currentDiscount.amount > 0) {
            if (currentDiscount.isPercentage) {
                grandTotal = totalBefore * (1 - currentDiscount.amount / 100);
            } else {
                grandTotal = totalBefore - currentDiscount.amount;
            }
        }

        document.getElementById('totalBefore').textContent = totalBefore.toFixed(2);
        document.getElementById('totalFOC').textContent = totalFOC.toFixed(2);
        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
    }

    function removeItem(index) {
        items.splice(index, 1);
        updateItemsTable();
        calculateTotals();
    }

    function applyDiscount() {
        const amount = parseFloat(document.getElementById('discount_amount').value);
        if (isNaN(amount) || amount < 0) {
            document.getElementById('err_discount').classList.remove('d-none');
            return;
        }

        currentDiscount = {
            amount: amount,
            isPercentage: document.getElementById('is_percentage').checked
        };

        calculateTotals();
        document.getElementById('err_discount').classList.add('d-none');
    }

    function clearDiscount() {
        document.getElementById('discount_amount').value = '';
        document.getElementById('is_percentage').checked = false;
        currentDiscount = { amount: 0, isPercentage: false };
        calculateTotals();
    }

    async function handleFormSubmit(event) {
        event.preventDefault();

        if (items.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please add at least one item to the GRN'
            });
            return;
        }

        const formData = {
            grn_number: document.getElementById('grn_number').value,
            grn_date: document.getElementById('grn_date').value,
            supplier_id: document.getElementById('supplier_id').value,
            supplier_invoice_number: document.getElementById('supplier_invoice_number').value,
            grn_type: document.querySelector('input[name="grn_type"]:checked').value,
            store_location: document.getElementById('store_location').value,
            note: document.getElementById('note').value,
            items: items,
            discount: currentDiscount,
        };

        // Validate required fields
        const requiredFields = {
            grn_number: 'GRN Number',
            grn_date: 'GRN Date',
            supplier_id: 'Supplier',
            supplier_invoice_number: 'Supplier Invoice Number',
            store_location: 'Store Location'
        };

        const emptyFields = [];
        for (const [field, label] of Object.entries(requiredFields)) {
            if (!formData[field]) {
                emptyFields.push(label);
            }
        }

        if (emptyFields.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Required Fields Missing',
                text: `Please fill in the following fields: ${emptyFields.join(', ')}`
            });
            return;
        }

        try {
            const response = await axios.post('/api/grn', formData);
            if (response.status === 201) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'GRN created successfully',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            }
        } catch (error) {
            console.error('Error creating GRN:', error);

            let errorMessage = 'Failed to create GRN. Please try again.';

            if (error.response) {
                if (error.response.status === 422) {
                    const validationErrors = error.response.data.errors;
                    errorMessage = Object.values(validationErrors)
                        .flat()
                        .join('\n');
                } else if (error.response.data.message) {
                    errorMessage = error.response.data.message;
                }
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        }
    }

    function editItem(index) {
        // Get the item to edit
        const item = items[index];
        editingItemIndex = index;

        // Populate form fields with item data
        document.getElementById('item_name').value = item.name;
        document.getElementById('qty').value = item.qty;
        document.getElementById('foc').value = item.foc;
        document.getElementById('price').value = item.price;
        document.getElementById('margin').value = item.margin;
        document.getElementById('discount').value = item.discount || '';

        // Change the add button to update
        const addButton = document.querySelector('.btn.btn-outline-secondary');
        addButton.textContent = 'Update';
        addButton.classList.remove('btn-outline-secondary');
        addButton.classList.add('btn-outline-primary');

        // Add cancel button if it doesn't exist
        if (!document.getElementById('cancelEditBtn')) {
            const cancelBtn = document.createElement('button');
            cancelBtn.id = 'cancelEditBtn';
            cancelBtn.type = 'button';
            cancelBtn.className = 'btn btn-outline-danger ms-2';
            cancelBtn.textContent = 'Cancel';
            cancelBtn.onclick = cancelEdit;
            addButton.parentNode.appendChild(cancelBtn);
        }

        // Update the onclick handler of the add button
        addButton.onclick = updateItem;
    }

    function updateItem() {
        if (!validateItemInputs()) return;

        // Create updated item object
        const updatedItem = {
            name: document.getElementById('item_name').value,
            qty: parseFloat(document.getElementById('qty').value),
            foc: parseFloat(document.getElementById('foc').value),
            price: parseFloat(document.getElementById('price').value),
            margin: parseFloat(document.getElementById('margin').value),
            discount: parseFloat(document.getElementById('discount').value) || 0
        };

        // Calculate final price based on GRN type
        const isMarginBased = document.getElementById('type_margin').checked;
        if (isMarginBased) {
            updatedItem.finalPrice = updatedItem.price * (1 + updatedItem.margin / 100);
        } else {
            updatedItem.finalPrice = updatedItem.price * (1 - updatedItem.discount / 100);
        }

        updatedItem.subtotal = updatedItem.finalPrice * updatedItem.qty;

        // Update the item in the items array
        items[editingItemIndex] = updatedItem;

        // Reset the form and update the table
        resetItemForm();
        updateItemsTable();
        calculateTotals();
    }

    function cancelEdit() {
        resetItemForm();
    }

    function resetItemForm() {
        // Clear inputs
        clearItemInputs();

        // Reset add button
        const addButton = document.querySelector('.btn.btn-outline-primary, .btn.btn-outline-secondary');
        addButton.textContent = '+';
        addButton.classList.remove('btn-outline-primary');
        addButton.classList.add('btn-outline-secondary');
        addButton.onclick = addItem;

        // Remove cancel button
        const cancelBtn = document.getElementById('cancelEditBtn');
        if (cancelBtn) {
            cancelBtn.remove();
        }

        // Reset editing state
        editingItemIndex = null;
    }

    // Modify the existing clearItemInputs function to also handle the form reset
    function clearItemInputs() {
        ['item_name', 'qty', 'foc', 'price', 'margin', 'discount'].forEach(id => {
            const input = document.getElementById(id);
            input.value = '';
            // Clear validation errors
            const error = document.getElementById(`err_${id}`);
            if (error) {
                error.classList.add('d-none');
            }
        });
    }

    function openAddGRNModal() {
        // Reset form state
        items = [];
        currentDiscount = { amount: 0, isPercentage: false };
        editingItemIndex = null;

        // Clear all inputs
        clearItemInputs();

        // Reset the table
        updateItemsTable();
        calculateTotals();

        // Go to first step
        goToStep(1);
    }

    function toggleDiscountSection() {
        const section = document.getElementById('discountSection');
        if (section.style.display === 'none') {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    }
</script>
