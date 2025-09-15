@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Supplier Payment'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Supplier Payment List </h3>
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
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="supplierPaymentTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <a href="/supplier-payment" class="btn btn-primary">Make Payments</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active ">
                                            <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Supplier Code</th>
                                                <th scope="col">Supplier Name</th>
                                                <th scope="col">Total Amount</th>
                                                <th scope="col">Balance</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="supplierPaymentTable">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

<script>
    const apiUrl = '/api/supplier-payments-list';
    loadSupplierPayment();

    function loadSupplierPayment() {
        $.get(apiUrl, function(data) {
            let table = $('.lms_table_active').DataTable();
            table.clear();

            let rowID = 1;
            data.forEach(supplierPayment => {
                table.row.add([
                    rowID,
                    supplierPayment.supplier_code,
                    supplierPayment.name,
                    supplierPayment.total_amount,
                    supplierPayment.balance,
                    `<a href="/supplier-payment?supplier_id=${supplierPayment.id}" target="_blank" class="btn btn-sm btn-primary">View</a>`

                ]);
                rowID++;
            });

            table.draw();
        });
    }
</script>
