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
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter">
                                                    Add New
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active3 ">
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
                            <form id="serviceForm">
                                <input type="hidden" id="service_id">
                                <div class="white_card_body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" id="code" placeholder="Service Code">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" id="description" placeholder="Description">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="number" class="decimal-input" step="0.01" id="price" placeholder="Price">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                                <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                                <button type="button" class="btn btn-primary" onclick="saveService()">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script>
                    const apiUrl = '/api/services';
                    loadServices();
                    
                    function loadServices() {
                        $.get(apiUrl, function(data) {
                            let rows = '';
                            let rowID = 1;
                            data.forEach(service => {
                                rows += `
                                <tr>
                                    <td>${rowID}</td>
                                    <td>${service.code}</td>
                                    <td>${service.description}</td>
                                    <td>${service.price}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" onclick="editService(${service.id})">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteService(${service.id})">Delete</button>
                                    </td>
                                </tr>
                            `;
                                rowID++;
                            });
                            $('#serviceTable').html(rows);
                        });
                    }
                    
                    function saveService() {
                        const service_id = $('#service_id').val();
                        const data = {
                            code: $('#code').val(),
                            description: $('#description').val(),
                            price: $('#price').val()
                        };
                        
                        if (service_id) {
                            $.ajax({
                                url: `${apiUrl}/${service_id}`,
                                method: 'PUT',
                                data: data,
                                success: function() {
                                    loadServices();
                                    closeModal();
                                    $('#service_id').val('');
                                },
                                error: function (xhr) {
                                    if (xhr.status === 422) {
                                        const response = xhr.responseJSON;
                                        alert(response.message);
                                    } else {
                                        alert('Something went wrong.');
                                    }
                                }
                            });
                        } else {
                            $.ajax({
                                url: `${apiUrl}`,
                                method: 'POST',
                                data: data,
                                success: function() {
                                    loadServices();
                                    closeModal();
                                },
                                error: function (xhr) {
                                    if (xhr.status === 422) {
                                        const response = xhr.responseJSON;
                                        alert(response.message);
                                    } else {
                                        alert('Something went wrong.');
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
                    
                    function editService(service_id) {
                        $.get(`${apiUrl}/${service_id}`, function(service) {
                            $('#service_id').val(service.id);
                            $('#code').val(service.code);
                            $('#description').val(service.description);
                            $('#price').val(service.price);
                        });
                    }
                    
                    function deleteService(service_id) {
                        if (confirm('Delete this service?')) {
                            $.ajax({
                                url: `${apiUrl}/${service_id}`,
                                method: 'DELETE',
                                success: loadServices
                            });
                        }
                    }

                    
                    
                </script>
                
                
                