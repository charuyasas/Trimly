@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Categories'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Category List</h3>
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
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="categoryTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openAddCategoryModal()">
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
                                                <th>Category Name</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="categoryTable">
                                            <!-- Dynamic Rows -->
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

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="category_id">
                    <div class="white_card_body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="category_name" placeholder="Category Name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCategoryModal()">Close</button>
                    <button type="button" class="btn btn-primary" id="saveCategoryBtn" onclick="saveCategory()">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const categoryApiUrl = '/api/categories';
    loadCategories();

    function loadCategories() {
        $.get(categoryApiUrl, function(data) {
            let table = $('.lms_table_active').DataTable();
            table.clear();

            let i = 1;
            data.forEach(category => {
                table.row.add([
                    i++,
                    category.name,
                    `
                    <button class="btn btn-sm btn-primary" onclick="editCategory('${category.id}')">Edit</button>
                   <!-- <button class="btn btn-sm btn-danger" onclick="deleteCategory('${category.id}')">Delete</button>-->
                    `
                ]);
            });

            table.draw();
        });
    }

    function saveCategory() {
        const category_id = $('#category_id').val();
        const data = {
            id: category_id,
            name: $('#category_name').val()
        };

        const method = category_id ? 'PUT' : 'POST';
        const url = category_id ? `${categoryApiUrl}/${category_id}` : categoryApiUrl;

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function () {
                Swal.fire({
                    icon: "success",
                    title: category_id ? "Updated Successfully" : "Saved Successfully",
                    showConfirmButton: false,
                    timer: 1500
                });
                loadCategories();
                closeCategoryModal();
            },
            error: function (xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    icon: "error",
                    title: response?.message || "Something went wrong",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }

    function editCategory(id) {
        $.get(`${categoryApiUrl}/${id}`, function(category) {
            $('#category_id').val(category.id);
            $('#category_name').val(category.name);
            $('.modal-title').text('Edit Category');
            $('#saveCategoryBtn').text('Update');
            const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
            modal.show();
        });
    }

    function deleteCategory(id) {
        if (confirm('Delete this category?')) {
            $.ajax({
                url: `${categoryApiUrl}/${id}`,
                method: 'DELETE',
                success: function () {
                    loadCategories();
                    Swal.fire({
                        icon: 'success',
                        title: 'Category deleted successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Something went wrong!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        }
    }

    function closeCategoryModal() {
        const modalElement = document.getElementById('categoryModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('.modal-title').text('Add Category');
        $('#saveCategoryBtn').text('Save');
    }

    function openAddCategoryModal() {
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('.modal-title').text('Add Category');
        $('#saveCategoryBtn').text('Save');
    }

    // Tab on Enter Key
    $(document).on('keydown', 'input, select, textarea', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const form = $(this).closest('form');
            const focusables = form.find('input, select, textarea, button').filter(':visible:not([readonly]):not([disabled])');
            const index = focusables.index(this);
            if (index > -1 && index + 1 < focusables.length) {
                focusables.eq(index + 1).focus();
            } else {
                $('#saveCategoryBtn').click();
            }
        }
    });

    $('#categoryForm').on('submit', function (e) {
        e.preventDefault();
    });
</script>
