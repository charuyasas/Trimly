@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Invoice'])

<style>
    #invoiceTable tr{
        border-width: 1px !important;
    }

    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 70%;
            margin: 1.75rem auto;
        }
    }
</style>

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Invoice List </h3>
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
                                                    <form Active="#">
                                                        <div class="search_field">
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="invoiceTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active ">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Invoice No</th>
                                                    <th scope="col">Employee</th>
                                                    <th scope="col">Customer</th>
                                                    <th scope="col">Discount (%)</th>
                                                    <th scope="col">Discount Amount</th>
                                                    <th scope="col">Grand Total</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="invoiceTable">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('includes.footer')

            <div class="modal fade" id="invoiceDetailModal" tabindex="-1" role="dialog" aria-labelledby="invoiceDetailModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Invoice Details</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="itemTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Item Code</th>
                                            <th scope="col">Item Description</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Qty</th>
                                            <th scope="col">Discount Percentage (%)</th>
                                            <th scope="col">Discount Amount</th>
                                            <th scope="col">Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="7" class="text-end fw-bold">Grand Total</td>
                                            <td id="grandTotal" class="text-end fw-bold">0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="closeModal()">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                const apiUrl = '/api/invoices';
                loadInvoice();

                function loadInvoice() {
                    $.get(apiUrl, function(data) {
                        let table = $('.lms_table_active').DataTable();
                        table.clear();

                        let rowID = 1;
                        data.forEach(invoice => {
                            table.row.add([
                            rowID,
                            invoice.invoice_no,
                            invoice.employee_name,
                            invoice.customer_name,
                            invoice.discount_percentage,
                            invoice.discount_amount,
                            invoice.grand_total,
                            `
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#invoiceDetailModal" onclick="loadInvoiceDetails('${invoice.id}','${invoice.invoice_no}')">View</button>
                            `
                            ]);
                            rowID++;
                        });

                        table.draw();
                    });
                }

                function closeModal() {
                    const modalElement = document.getElementById('invoiceDetailModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);

                    if (modal) {
                        modal.hide();
                    }
                }

                function loadInvoiceDetails(invoice_id) {
                    $.get(`${apiUrl}/${invoice_id}`, function(data) {
                        const tbody = $('#itemTable tbody');
                        tbody.empty(); // Clear old rows

                        let rowID = 1;
                        let grandTotal = 0;

                        data.forEach(invoice => {
                            const subTotal = parseFloat(invoice.sub_total ?? 0);
                            grandTotal += subTotal;

                            const row = `
                <tr>
                    <td>${rowID}</td>
                    <td>${invoice.item_code ?? ''}</td>
                    <td>${invoice.item_description ?? ''}</td>
                    <td  class="text-end">${invoice.amount ?? 0}</td>
                    <td>${invoice.quantity ?? 0}</td>
                    <td class="text-end">${invoice.discount_percentage ?? 0}</td>
                    <td class="text-end">${invoice.discount_amount ?? 0}</td>
                    <td class="text-end">${subTotal.toFixed(2)}</td>
                </tr>
            `;
                            tbody.append(row);
                            rowID++;
                        });

                        $('#grandTotal').text(grandTotal.toFixed(2));
                    });
                }



            </script>
