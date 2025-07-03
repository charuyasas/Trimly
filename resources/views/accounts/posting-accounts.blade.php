@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Posting Accounts'])

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Posting Accounts</h3>
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
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="postingAccTable">
                                                        </div>
                                                        <button type="submit"> <i class="ti-search"></i> </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" onclick="showModal()" data-bs-target="#postingAccountModal">
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
                                                    <th scope="col">Main Code</th>
                                                    <th scope="col">Heading Code</th>
                                                    <th scope="col">Title Code</th>
                                                    <th scope="col">Posting Code</th>
                                                    <th scope="col">Posting Account</th>
                                                    <th scope="col">Ledger Code</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="accountTable">

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

            <div class="modal fade" id="postingAccountModal" tabindex="-1" role="dialog" aria-labelledby="postingAccountModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Add Posting Account</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="postingAccForm" onsubmit="savePostingAccount(); return false;">
                                <input type="hidden" id="posting_code">
                                <div class="white_card_body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" class="form-control" id="main_account" name="mainAccount" placeholder="Select main account..." tabindex="1">
                                                <input type="hidden" id="main_code">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" class="form-control" id="heading_account" name="headingAccount" placeholder="Select heading account..." tabindex="1">
                                                <input type="hidden" id="heading_code">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" class="form-control" id="title_account" name="titleAccount" placeholder="Select title account..." tabindex="1">
                                                <input type="hidden" id="title_code">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="common_input mb_15">
                                                <input type="text" id="posting_account" class="form-control" placeholder="Posting Account...">
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
                    const apiUrl = '/api/postingAccount';
                    loadPostingAccounts();

                    function loadPostingAccounts() {
                        $.get(apiUrl, function(data) {
                            let table = $('.lms_table_active').DataTable();
                            table.clear();

                            let rowID = 1;
                            data.forEach(postingAcc => {
                                table.row.add([
                                rowID,
                                postingAcc.main_account,
                                postingAcc.heading_account,
                                postingAcc.title_account,
                                postingAcc.posting_code,
                                postingAcc.posting_account,
                                postingAcc.ledger_code,
                                `
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#postingAccountModal" onclick="editPostingAccount('${postingAcc.posting_code}')">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deletePostingAccount('${postingAcc.posting_code}')">Delete</button>
                                    `
                                ]);
                                rowID++;
                            });

                            table.draw();
                        });
                    }

                    $(function () {

                        $("#main_account").autocomplete({
                            source: function (request, response) {
                                if (request.term.length < 1) return;

                                $.ajax({
                                    url: '/api/main_account_list',
                                    dataType: 'json',
                                    data: { q: request.term },
                                    success: function (data) {
                                        response(data);
                                        if (data.length === 1) {
                                            $("#main_account").val(data[0].label);
                                            $("#main_code").val(data[0].value);
                                            $("#heading_account").val("");
                                            $("#heading_code").val("");
                                            $("#title_account").val("");
                                            $("#title_code").val("");
                                        }
                                    }
                                });
                            },
                            minLength: 1,
                            appendTo: "#postingAccountModal",
                            select: function (event, ui) {
                                $("#main_account").val(ui.item.label);
                                $("#main_code").val(ui.item.value);
                                $("#heading_account").val("");
                                $("#heading_code").val("");
                                $("#title_account").val("");
                                $("#title_code").val("");
                                return false;
                            }
                        });

                        $("#heading_account").autocomplete({
                            source: function (request, response) {
                                const main_code = $('#main_code').val();
                                 if (!main_code) {
                                    $("#heading_account").val("");
                                    $("#heading_code").val("");
                                    alert("Please select a Main Account first.");
                                    return;
                                }

                                if (request.term.length < 1) return;

                                $.ajax({
                                    url: `/api/heading_account_list/${main_code}`,
                                    dataType: 'json',
                                    data: { q: request.term },
                                    success: function (data) {
                                        response(data);
                                        if (data.length === 1) {
                                            $("#heading_account").val(data[0].label);
                                            $("#heading_code").val(data[0].value);
                                            $("#title_account").val("");
                                            $("#title_code").val("");
                                        }
                                    }
                                });
                            },
                            minLength: 1,
                            appendTo: "#postingAccountModal",
                            select: function (event, ui) {
                                $("#heading_account").val(ui.item.label);
                                $("#heading_code").val(ui.item.value);
                                $("#title_account").val("");
                                $("#title_code").val("");
                                return false;
                            }
                        });

                        $("#title_account").autocomplete({
                            source: function (request, response) {
                                const main_code = $('#main_code').val();
                                 if (!main_code) {
                                    $("#title_account").val("");
                                    $("#title_code").val("");
                                    alert("Please select a Main Account first.");
                                    return;
                                }

                                const heading_code = $('#heading_code').val();
                                 if (!heading_code) {
                                    $("#title_account").val("");
                                    $("#title_code").val("");
                                    alert("Please select a Heading Account first.");
                                    return;
                                }

                                if (request.term.length < 1) return;

                                $.ajax({
                                    url: `/api/title_account_list/${main_code}/${heading_code}`,
                                    dataType: 'json',
                                    data: { q: request.term },
                                    success: function (data) {
                                        response(data);
                                        if (data.length === 1) {
                                            $("#title_account").val(data[0].label);
                                            $("#title_code").val(data[0].value);
                                        }
                                    }
                                });
                            },
                            minLength: 1,
                            appendTo: "#postingAccountModal",
                            select: function (event, ui) {
                                $("#title_account").val(ui.item.label);
                                $("#title_code").val(ui.item.value);
                                return false;
                            }
                        });
                    });

                    function savePostingAccount() {
                        const posting_code = $('#posting_code').val();
                        const data = {
                            posting_code: $('#posting_code').val(),
                            posting_account: $('#posting_account').val(),
                            main_code: $('#main_code').val(),
                            heading_code: $('#heading_code').val(),
                            title_code: $('#title_code').val()
                        };

                        if (posting_code) {
                            $.ajax({
                                url: `${apiUrl}/${posting_code}`,
                                method: 'PUT',
                                data: data,
                                success: function() {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Updated Successfully",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    loadPostingAccounts();
                                    closeModal();
                                    $('#postingAccountCode').val('');
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
                                    loadPostingAccounts();
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
                        const modalElement = document.getElementById('postingAccountModal');
                        const modal = bootstrap.Modal.getInstance(modalElement);

                        if (modal) {
                            modal.hide();
                            $('#postingAccForm')[0].reset();
                        }
                    }

                    function showModal() {
                        $('#postingAccForm')[0].reset();
                        $('#posting_code').val("");
                        $('#exampleModalLongTitle').text('Create Posting Account');
                        $('#saveBtn').text('Save');
                    }

                    function editPostingAccount(posting_code) {
                        $.get(`${apiUrl}/${posting_code}`, function(postingAcc) {
                            $('#posting_code').val(postingAcc.posting_code);
                            $('#posting_account').val(postingAcc.posting_account);
                            $('#main_code').val(postingAcc.main_code);
                            $('#main_account').val(postingAcc.main_account);
                            $('#heading_code').val(postingAcc.heading_code);
                            $('#heading_account').val(postingAcc.heading_account);
                            $('#title_code').val(postingAcc.title_code);
                            $('#title_account').val(postingAcc.title_account);

                            $('#exampleModalLongTitle').text('Edit Posting Account');
                            $('#saveBtn').text('Update');
                        });
                    }

                    function deletePostingAccount(posting_code) {
                        if (confirm('Delete this postingAcc?')) {
                            $.ajax({
                                url: `${apiUrl}/${posting_code}`,
                                method: 'DELETE',
                                success: function() {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted Successfully",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    loadPostingAccounts();
                                }
                            });
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
                                savePostingAccount();
                            }
                        }
                    });

                    $(document).on('click', 'button', function () {
                        if ($(this).text().trim() === 'Save' || $(this).text().trim() === 'Update') {
                            savePostingAccount();
                        }
                    });



                    $('#postingAcc_contactno').on('keydown', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            savePostingAccount();
                        }
                    });


                </script>


