@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Services'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Service List </h3>
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
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="serviceTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" onclick="showModal()" data-bs-target="#exampleModalCenter">
                                                    Add New
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active ">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Code</th>
                                                    <th scope="col">Description</th>
                                                    <th scope="col">Price</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="serviceTable">

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

            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Add Service</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="serviceForm" onsubmit="saveService(); return false;">
                                <input type="hidden" id="service_id">
                                <div class="white_card_body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <label class="form-label">Service Code</label>
                                                <input type="text" id="code" placeholder="Enter ...">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <label class="form-label">Description</label>
                                                <input type="text" id="description" placeholder="Enter ...">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <label class="form-label">Price</label>
                                                <input type="number" class="decimal-input" step="0.01" id="price" placeholder="Enter ...">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <label class="form-label">Commission (%)</label>
                                                <input type="number" id="commission"  placeholder="Enter ..." max="100" min="0" onkeyup="validateCommission()">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_fixed_price" data-parsley-multiple="groups" data-parsley-mincheck="2" checked/>
                                                <label class="form-label form-check-label" for="horizontalCheckbox">Fixed Price</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="saveBtn">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    const apiUrl = '/api/services';
                    loadServices();

                    function loadServices() {
                        $.get(apiUrl, function(data) {
                            let table = $('.lms_table_active').DataTable();
                            table.clear();

                            let rowID = 1;
                            data.forEach(service => {
                                table.row.add([
                                rowID,
                                service.code,
                                service.description,
                                service.price,
                                `
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" onclick="editService('${service.id}')">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteService('${service.id}')">Delete</button>
                                    `
                                ]);
                                rowID++;
                            });

                            table.draw();
                        });
                    }

                    function saveService() {
                        const service_id = $('#service_id').val();
                        const data = {
                            id: $('#service_id').val(),
                            code: $('#code').val(),
                            description: $('#description').val(),
                            price: $('#price').val(),
                            commission: $('#commission').val(),
                            is_fixed_price: $('#is_fixed_price').is(':checked') ? 1 : 0
                        };

                        console.log(data);

                        if (service_id) {
                            $.ajax({
                                url: `${apiUrl}/${service_id}`,
                                method: 'PUT',
                                data: data,
                                success: function() {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Updated Successfully",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    loadServices();
                                    closeModal();
                                    $('#service_id').val('');
                                },
                                error: function (xhr) {
                                    if (xhr.status === 422) {
                                        const response = xhr.responseJSON;
                                        Swal.fire({
                                            icon: "error",
                                            title: response.message,
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Something went wrong",
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                    }
                                }
                            });
                        } else {
                            $.ajax({
                                url: `${apiUrl}`,
                                method: 'POST',
                                data: data,
                                success: function() {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Saved Successfully",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    loadServices();
                                    closeModal();
                                },
                                error: function (xhr) {
                                    if (xhr.status === 422) {
                                        const response = xhr.responseJSON;
                                        Swal.fire({
                                            icon: "error",
                                            title: response.message,
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Something went wrong",
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                    }
                                }
                            });
                        }
                    }

                    function closeModal() {
                        const modalElement = document.getElementById('exampleModalCenter');
                        const modal = bootstrap.Modal.getInstance(modalElement);

                        if (modal) {
                            modal.hide();
                            $('#serviceForm')[0].reset();
                        }
                    }

                    function showModal() {
                        $('#serviceForm')[0].reset();
                        $('#service_id').val("");
                        $('#exampleModalLongTitle').text('Add Service');
                        $('#saveBtn').text('Save');
                    }

                    function editService(service_id) {
                        $.get(`${apiUrl}/${service_id}`, function(service) {
                            $('#service_id').val(service.id);
                            $('#code').val(service.code);
                            $('#description').val(service.description);
                            $('#price').val(service.price);
                            $('#commission').val(service.commission);
                            $('#is_fixed_price').prop('checked', service.is_fixed_price == 1);
                            $('#exampleModalLongTitle').text('Edit Service');
                            $('#saveBtn').text('Update');
                        });
                    }

                    function deleteService(service_id) {
                        if (confirm('Delete this service?')) {
                            $.ajax({
                                url: `${apiUrl}/${service_id}`,
                                method: 'DELETE',
                                success: function() {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted Successfully",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    loadServices();
                                }
                            });
                        }
                    }

                    function validateCommission(){
                        let commission = parseFloat($('#commission').val()) || 0;
                        if(commission < 0 || commission > 100) {
                            alert(`Commission percentage must be between 0 and 100.`);
                            $("#commission").val('');
                        }
                    }


                    $(document).on('keydown', 'input, select, textarea, button', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();

                            const focusables = $('input, select, textarea, button')
                            .filter(':visible:not([readonly]):not([disabled])');

                            const index = focusables.index(this);

                            if (index > -1 && index + 1 < focusables.length) {
                                const next = focusables.eq(index + 1);
                                next.focus();

                                if (next.is('button') && next.text().trim() === 'Save' || next.is('button') && next.text().trim() === 'Update') {
                                    next.click();
                                }
                            } else {
                                saveService();
                            }
                        }
                    });

                    $(document).on('click', 'button', function () {
                        if ($(this).text().trim() === 'Save' || $(this).text().trim() === 'Update') {
                            saveService();
                        }
                    });

                </script>


