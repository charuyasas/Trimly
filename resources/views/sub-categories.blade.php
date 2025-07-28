@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Sub-Categories'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Sub-Category List</h3>
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
                                                    <form action="#">
                                                        <div class="search_field">
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="subCategoryTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#subCategoryModal" onclick="openAddSubCategoryModal()">
                                                    Add New
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Category</th>
                                                <th>Sub-Category</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="subCategoryTable">
                                            <!-- Dynamic rows -->
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="subCategoryModal" tabindex="-1" role="dialog" aria-labelledby="subCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Sub-Category</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="subCategoryForm">
                    <input type="hidden" id="sub_category_id">
                    <div class="white_card_body">
                        <div class="row">
                            <div class="col-lg-12 mb_15">
                                <div class="common_input mb_15">
                                    <input type="text" class="form-control" id="cbo_category" placeholder="Search category..." autocomplete="off">
                                    <input type="hidden" id="category_id">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="sub_category_name" placeholder="Sub-Category Name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveSubCategoryBtn" onclick="saveSubCategory()">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const apiUrl = '/api/sub-categories';
    const categoryApiUrl = '/api/categories';

    $(document).ready(function () {
        loadSubCategories();
    });

    function loadSubCategories() {
        $.get(apiUrl, function (data) {
            let table = $('.lms_table_active').DataTable();
            table.clear();

            let i = 1;
            data.forEach(item => {
                table.row.add([
                    i++,
                    item.category?.name ?? '—',
                    item.name,
                    `
                    <button class="btn btn-sm btn-primary" onclick="editSubCategory('${item.id}')">Edit</button>
                   <!-- <button class="btn btn-sm btn-danger" onclick="deleteSubCategory('${item.id}')">Delete</button>-->
                    `
                ]);
            });

            table.draw();
        });
    }

    function saveSubCategory() {
        const id = $('#sub_category_id').val();
        const categoryId = $('#category_id').val();
        const name = $('#sub_category_name').val().trim();

        // Client-side validation for null/empty
        if (!categoryId) {
            Swal.fire({
                icon: 'warning',
                title: 'Please select a category.',
                showConfirmButton: true
            });
            return;
        }
        if (!name) {
            Swal.fire({
                icon: 'warning',
                title: 'Sub-category name cannot be empty.',
                showConfirmButton: true
            });
            return;
        }

        const data = {
            id: id,
            category_id: categoryId,
            name: name
        };

        const method = id ? 'PUT' : 'POST';
        const url = id ? `${apiUrl}/${id}` : apiUrl;

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function () {
                Swal.fire({
                    icon: "success",
                    title: id ? "Updated Successfully" : "Saved Successfully",
                    showConfirmButton: false,
                    timer: 1500
                });
                loadSubCategories();
                closeSubCategoryModal();
            },
            error: function (xhr) {
                let message = "Error occurred!";
                if (xhr.status === 409) {
                    message = "Sub-category already exists. Please choose a different name.";
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: "error",
                    title: message,
                    showConfirmButton: true
                });
            }
        });
    }

    $("#cbo_category").autocomplete({
        source: function (request, response) {
            if (request.term.length < 1) return;
            $.ajax({
                url: '/api/categories-list',
                dataType: 'json',
                data: { search_key: request.term },
                success: function (data) {
                    response(data);
                    if (data.length === 1) {
                        $("#cbo_category").val(data[0].label);
                        $("#category_id").val(data[0].value);
                    }
                }
            });
        },
        minLength: 1,
        appendTo: "#subCategoryModal",
        focus: function (event, ui) {
            $("#cbo_category").val(ui.item.label);
            return false;
        },
        select: function (event, ui) {
            $("#cbo_category").val(ui.item.label);
            $("#category_id").val(ui.item.value);
            return false;
        }
    });

    function editSubCategory(id) {
        $.get(`${apiUrl}/${id}`, function (data) {
            $('#sub_category_id').val(data.id);
            $('#sub_category_name').val(data.name);
            $('#category_id').val(data.category_id);
            $('#cbo_category').val(data.category?.name || '');
            $('.modal-title').text('Edit Sub-Category');
            $('#saveSubCategoryBtn').text('Update');

            const modal = new bootstrap.Modal(document.getElementById('subCategoryModal'));
            modal.show();
        });
    }

    function deleteSubCategory(id) {
        if (confirm('Delete this sub-category?')) {
            $.ajax({
                url: `${apiUrl}/${id}`,
                method: 'DELETE',
                success: function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted Successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    loadSubCategories();
                }
            });
        }
    }

    function closeSubCategoryModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('subCategoryModal'));
        if (modal) modal.hide();

        $('#subCategoryForm')[0].reset();
        $('#sub_category_id').val('');
        $('.modal-title').text('Add Sub-Category');
        $('#saveSubCategoryBtn').text('Save');
    }

    function openAddSubCategoryModal() {
        $('#subCategoryForm')[0].reset();
        $('#sub_category_id').val('');
        $('#category_id').val('');
        $('#cbo_category').val('');
        $('.modal-title').text('Add Sub-Category');
        $('#saveSubCategoryBtn').text('Save');
    }

    // Enter key navigation
    $(document).on('keydown', 'input, select', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const form = $(this).closest('form');
            const focusables = form.find('input, select, textarea, button').filter(':visible:not([readonly]):not([disabled])');
            const index = focusables.index(this);
            if (index > -1 && index + 1 < focusables.length) {
                focusables.eq(index + 1).focus();
            } else {
                $('#saveSubCategoryBtn').click();
            }
        }
    });

    $('#subCategoryForm').on('submit', function (e) {
        e.preventDefault();
    });

</script>
