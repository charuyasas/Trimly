<nav class="sidebar dark_sidebar">
    <div class="logo d-flex justify-content-between">
        <a class="large_logo" href="index-2.html"><img src="{{ asset('assets/img/logo.png') }}" alt=""></a>
        <a class="small_logo" href="index-2.html"><img src="{{ asset('assets/img/mini_logo.png') }}" alt=""></a>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>
    <ul id="sidebar_menu">
        <li class="">
            <a  href="/services" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/11.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Services</span>
            </div>
            </a>
        </li>
        <li class="">
            <a  href="/customers" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/5.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Customers</span>
            </div>
            </a>
        </li>
         <li class="">
            <a  href="/employee" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/4.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Employees</span>
            </div>
            </a>
        </li>
         <li class="">
            <a  href="/bookings" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/15.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Bookings</span>
            </div>
            </a>
        </li>
         <li class="">
            <a  href="/supplier" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/3.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Suppliers</span>
            </div>
            </a>
        </li>
        <li class="">
            <a  href="/grn" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="{{ asset('assets/img/menu-icon/14.svg') }}" alt="">
                </div>
                <div class="nav_title">
                    <span>GRN</span>
                </div>
            </a>
        </li>
        <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="{{ asset('assets/img/menu-icon/16.svg') }}" alt="">
                </div>
                <div class="nav_title">
                    <span>Item Master</span>
                </div>
            </a>
            <ul>
                <li><a href="/categories">Categories</a></li>
                <li><a href="/sub-categories">Sub Categories</a></li>
                <li><a href="/items">Items</a></li>
            </ul>
        </li>
        <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/20.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Sales Invoice</span>
            </div>
            </a>
            <ul>
              <li><a href="/invoice">Add New</a></li>
              <li><a href="/invoiceList">List</a></li>
            </ul>
        </li>
        <li class="">
            <a  href="/stockIssue" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="{{ asset('assets/img/menu-icon/13.svg') }}" alt="">
                </div>
                <div class="nav_title">
                    <span>Stock Issuing</span>
                </div>
            </a>
        </li>
        <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/21.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Accounts</span>
            </div>
            </a>
            <ul>
              <li><a href="/postingAccount">Posting Accounts</a></li>
            </ul>
        </li>
      </ul>
</nav>

<section class="main_content dashboard_part large_header_bg">
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
                        <h3 class="f_s_25 f_w_700 dark_text mr_30">{{ $pageTitle ?? 'Default Title' }}</h3>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                            <li class="breadcrumb-item active">{{ $pageTitle ?? 'Page' }}</li>
                        </ol>
                    </div>

                    <div class="header_right d-flex justify-content-between align-items-center">
                        <div id="datetime" style="font-size: 20px; font-weight: bold;"></div>
                        <div class="serach_field-area d-flex align-items-center">
                        <div class="search_inner">
                            <form action="#">
                                <div class="search_field">
                                    <input type="text" placeholder="Search">
                                </div>
                                <button type="submit"> <img src="{{ asset('assets/img/icon/icon_search.svg') }}" alt=""> </button>
                            </form>
                        </div>
                    </div>
                        <div class="header_notification_warp d-flex align-items-center">
                            <li>
                                <a class="bell_notification_clicker" href="#"> <img src="{{ asset('assets/img/icon/bell.svg') }}" alt="">
                                    <span>2</span>
                                </a>
                                <!-- Menu_NOtification_Wrap  -->
                            <div class="Menu_NOtification_Wrap">
                                <div class="notification_Header">
                                    <h4>Notifications</h4>
                                </div>
                                <div class="Notification_body">
                                    <!-- single_notify  -->
                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('assets/img/staf/2.png') }}" alt=""></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#"><h5>Cool Marketing </h5></a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>
                                    <!-- single_notify  -->
                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('assets/img/staf/4.png') }}" alt=""></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#"><h5>Awesome packages</h5></a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>
                                    <!-- single_notify  -->
                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('assets/img/staf/3.png') }}" alt=""></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#"><h5>what a packages</h5></a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>
                                    <!-- single_notify  -->
                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('assets/img/staf/2.png') }}" alt=""></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#"><h5>Cool Marketing </h5></a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>
                                    <!-- single_notify  -->
                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('assets/img/staf/4.png') }}" alt=""></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#"><h5>Awesome packages</h5></a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>
                                    <!-- single_notify  -->
                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('assets/img/staf/3.png') }}" alt=""></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#"><h5>what a packages</h5></a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="nofity_footer">
                                    <div class="submit_button text-center pt_20">
                                        <a href="#" class="btn_1">See More</a>
                                    </div>
                                </div>
                            </div>
                            <!--/ Menu_NOtification_Wrap  -->
                            </li>
                            <li>
                                <a class="CHATBOX_open" href="#"> <img src="{{ asset('assets/img/icon/msg.svg') }}" alt=""> <span>2</span>  </a>
                            </li>
                        </div>
                        <div class="profile_info">
                            <img src="{{ asset('assets/img/client_img.png') }}" alt="#">
                            <div class="profile_info_iner">
                                <div class="profile_author_name">
                                    <p>Neurologist </p>
                                    <h5>Dr. Robar Smith</h5>
                                </div>
                                <div class="profile_info_details">
                                    <a href="#">My Profile </a>
                                    <a href="#">Settings</a>
                                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                        @csrf
                                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
