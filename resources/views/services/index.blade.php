@include('includes.header')
@include('includes.sidebar')

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                    <div class="page_title_left d-flex align-items-center">
                        <h3 class="f_s_25 f_w_700 dark_text mr_30" >Services</h3>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                            <li class="breadcrumb-item active">Services</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Add New Services </h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
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
                                                <input type="number" step="0.01" id="price" placeholder="Price">
                                            </div>
                                        </div>
                                        
                                        <div class="col-6">
                                            <div class="create_report_btn mt_30">
                                                <button class="btn_1 radius_btn d-block text-center">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                            </div>
                        </div>     
                    </div>
                
                <div class="col-lg-12">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_body">
                            <div class="QA_section">
                                <div class="white_box_tittle list_header">
                                    <h4>List</h4>
                                    <div class="box_right d-flex lms_block">
                                        <div class="serach_field_2">
                                            <div class="search_inner">
                                                <form Active="#">
                                                    <div class="search_field">
                                                        <input type="text" placeholder="Search content here...">
                                                    </div>
                                                    <button type="submit"> <i class="ti-search"></i> </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="add_button ms-2">
                                            <a href="#" data-toggle="modal" data-target="#addcategory" class="btn_1">Add New</a>
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
    
    <script>
        const apiUrl = '/api/services';
        
        function loadServices() {
            $.get(apiUrl, function(data) {
                let rows = '';
                data.forEach(service => {
                    rows += `
                <tr>
                    <td>${service.id}</td>
                    <td>${service.code}</td>
                    <td>${service.description}</td>
                    <td>${service.price}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editService(${service.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteService(${service.id})">Delete</button>
                    </td>
                </tr>
            `;
                });
                $('#serviceTable').html(rows);
            });
        }
        
        $('#serviceForm').submit(function(e) {
            e.preventDefault();
            const id = $('#service_id').val();
            const data = {
                code: $('#code').val(),
                description: $('#description').val(),
                price: $('#price').val()
            };
            
            if (id) {
                $.ajax({
                    url: `${apiUrl}/${id}`,
                    method: 'PUT',
                    data: data,
                    success: function() {
                        loadServices();
                        $('#serviceForm')[0].reset();
                        $('#service_id').val('');
                    }
                });
            } else {
                $.post(apiUrl, data, function() {
                    loadServices();
                    $('#serviceForm')[0].reset();
                });
            }
        });
        
        function editService(id) {
            $.get(`${apiUrl}/${id}`, function(service) {
                $('#service_id').val(service.id);
                $('#code').val(service.code);
                $('#description').val(service.description);
                $('#price').val(service.price);
            });
        }
        
        function deleteService(id) {
            if (confirm('Delete this service?')) {
                $.ajax({
                    url: `${apiUrl}/${id}`,
                    method: 'DELETE',
                    success: loadServices
                });
            }
        }
        
        loadServices();
    </script>
    
    
    @include('includes.footer')