<nav class="sidebar dark_sidebar mini_sidebar">
    <div class="logo d-flex justify-content-between">
        <a class="large_logo" href="index-2.html"><img src="{{ asset('assets/img/logo.png') }}" alt=""></a>
        <a class="small_logo" href="index-2.html"><img src="{{ asset('assets/img/mini_logo.png') }}" alt=""></a>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>
    <ul id="sidebar_menu"></ul>
</nav>

<section class="main_content dashboard_part large_header_bg full_main_content">
    <div class="container-fluid g-0">
        <div class="row">
            <div class="col-lg-12 p-0 ">
                <div class="header_iner d-flex justify-content-between align-items-center">
                    <div class="sidebar_icon d-lg-none">
                        <i class="ti-menu"></i>
                    </div>
                    <div class="line_icon open_miniSide d-none d-lg-block">
                        <img src="{{ asset('assets/img/line_img.png') }}" alt="">
                    </div>

                    <div class="page_title_left d-flex align-items-center me-auto" style="padding-left: 50px;">
                        <h3 class="f_s_25 f_w_700 dark_text mr_30">{{ $pageTitle ?? '' }}</h3>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                            <li class="breadcrumb-item active">{{ $pageTitle ?? '' }}</li>
                        </ol>
                    </div>

                    <div class="header_right d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#shiftInCashInHandModal" id="shiftInBtn" style="display: none">
                            Shift In
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" onclick="showShiftEndModal()" data-bs-target="#dayEndCashInHandModal" id="shiftOutBtn" style="display: none">
                            Shift Out
                        </button> &nbsp;&nbsp;&nbsp;&nbsp;
                        <div id="datetime" style="font-size: 20px; font-weight: bold;"></div>&nbsp;&nbsp;
                        <div class="profile_info">
                            <img src="{{ asset('assets/img/admin.jpg') }}" alt="#">
                            <div class="profile_info_iner">
                                <div class="profile_author_name">
                                    <p>{{ auth()->user()->getRoleNames()->first() }}</p>
                                    <h5>{{ auth()->user()->name }}</h5>
                                </div>
                                <div class="profile_info_details">
                                    <a href="{{ route('profile.show') }}">My Profile </a>
                                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                        @csrf
                                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();sessionStorage.removeItem('cashier_popup_shown');">
                                            Log Out
                                        </a>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    $(function() {
        const token = $('meta[name="api-token"]').attr('content');

        function renderLinks(links) {
            let html = '';
            links.forEach(link => {
                if (link.children && link.children.length > 0) {
                    html += `<li><a class="has-arrow" href="#" aria-expanded="false">`;
                    if (link.icon_path) html += `<div class="nav_icon_small"><img src="${link.icon_path}" alt=""></div>`;
                    html += `<div class="nav_title"><span>${link.display_name}</span></div></a><ul>${renderLinks(link.children)}</ul></li>`;
                } else {
                    html += `<li><a href="${link.url}" aria-expanded="false">`;
                    if (link.icon_path) html += `<div class="nav_icon_small"><img src="${link.icon_path}" alt=""></div>`;
                    html += `<div class="nav_title"><span>${link.display_name}</span></div></a></li>`;
                }
            });
            return html;
        }

        $.ajax({
            url: '/api/sidebar-links',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(links) {
                $('#sidebar_menu').html(renderLinks(links));

                $('#sidebar_menu ul').hide();

                $('#sidebar_menu').on('click', 'a.has-arrow', function(e) {
                    e.preventDefault();
                    const $parentLi = $(this).parent('li');
                    const $submenu = $(this).next('ul');
                    const isVisible = $submenu.is(':visible');

                    if (isVisible) {
                        $submenu.slideUp(200);
                        $parentLi.removeClass('expanded');
                        $(this).attr('aria-expanded', 'false');
                    } else {
                        $('#sidebar_menu ul:visible').slideUp(200);
                        $('#sidebar_menu li.expanded').removeClass('expanded');
                        $('#sidebar_menu a.has-arrow[aria-expanded="true"]').attr('aria-expanded', 'false');

                        $submenu.slideDown(200);
                        $parentLi.addClass('expanded');
                        $(this).attr('aria-expanded', 'true');
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Failed to load sidebar links:', error);
            }
        });
    });

</script>
