@include('includes.header')
@include('includes.sidebar')

<section class="main_content dashboard_part large_header_bg">
  <div class="container-fluid g-0">

    {{-- Header Bar with Notifications and Profile --}}
    <div class="row">
      <div class="col-lg-12 p-0">
        <div class="header_iner d-flex justify-content-between align-items-center">
          {{-- Sidebar toggle (mobile) --}}
          <div class="sidebar_icon d-lg-none"><i class="ti-menu"></i></div>
          {{-- Sidebar toggle (desktop) --}}
          <div class="line_icon open_miniSide d-none d-lg-block">
            <img src="{{ asset('assets/img/line_img.png') }}" alt="">
          </div>

          {{-- Global Search Bar --}}
          <div class="serach_field-area d-flex align-items-center">
            <div class="search_inner">
              <form action="#">
                <div class="search_field">
                  <input type="text" placeholder="Search">
                </div>
                <button type="submit">
                  <img src="{{ asset('assets/img/icon/icon_search.svg') }}" alt="">
                </button>
              </form>
            </div>
          </div>

          {{-- Notification + User Profile Area --}}
          <div class="header_right d-flex justify-content-between align-items-center">
            <div class="header_notification_warp d-flex align-items-center">
              {{-- Notification Bell --}}
              <li>
                <a class="bell_notification_clicker" href="#">
                  <img src="{{ asset('assets/img/icon/bell.svg') }}" alt="">
                  <span>2</span>
                </a>
                {{-- Notification Dropdown --}}
                <div class="Menu_NOtification_Wrap">
                  <div class="notification_Header"><h4>Notifications</h4></div>
                  <div class="Notification_body">
                    @for($i = 1; $i <= 3; $i++)
                      <div class="single_notify d-flex align-items-center">
                        <div class="notify_thumb">
                          <a href="#"><img src="{{ asset('assets/img/staf/' . $i . '.png') }}" alt=""></a>
                        </div>
                        <div class="notify_content">
                          <a href="#"><h5>Notification Title</h5></a>
                          <p>Lorem ipsum dolor sit amet</p>
                        </div>
                      </div>
                    @endfor
                  </div>
                  <div class="nofity_footer">
                    <div class="submit_button text-center pt_20">
                      <a href="#" class="btn_1">See More</a>
                    </div>
                  </div>
                </div>
              </li>
              {{-- Chat Icon --}}
              <li>
                <a class="CHATBOX_open" href="#">
                  <img src="{{ asset('assets/img/icon/msg.svg') }}" alt="">
                  <span>2</span>
                </a>
              </li>
            </div>

            {{-- Profile Dropdown --}}
            <div class="profile_info">
              <img src="{{ asset('assets/img/client_img.png') }}" alt="#">
              <div class="profile_info_iner">
                <div class="profile_author_name">
                  <p>Neurologist</p>
                  <h5>Dr. Robar Smith</h5>
                </div>
                <div class="profile_info_details">
                  <a href="#">My Profile</a>
                  <a href="#">Settings</a>
                  <a href="#">Log Out</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Main Content Card --}}
    <div class="row justify-content-center mt-4">
      <div class="col-md-10">
        <div class="card shadow-sm">
          <div class="card-body">

            {{-- Flash message on customer add/update/delete --}}
            @if(session('message'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
              </div>
            @endif

            {{-- Customer Listing Table --}}
            <div class="white_card_body">
              <div class="QA_section">
                <div class="white_box_tittle list_header">
                  <h4>Customer List</h4>

                  {{-- Search and Add New Button --}}
                  <div class="box_right d-flex lms_block">
                    <div class="serach_field_2">
                      <div class="search_inner">
                        <form id="searchForm">
                          <div class="search_field">
                            <input type="text" id="searchInput" placeholder="Search customers...">
                          </div>
                          <button type="submit"><i class="ti-search"></i></button>
                        </form>
                      </div>
                    </div>
                    <div class="add_button ms-2">
                      <a href="{{ route('add.customer.form') }}" class="btn_1">Add New</a>
                    </div>
                  </div>
                </div>

                <div id="flash-message" class="mt-3"></div>

                {{-- Table with Customer Data --}}
                <div class="QA_table mb_30">
                  <table class="table lms_table_active3">
                    <thead>
                      <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody id="customerTableBody">
                      @forelse($customers as $customer)
                        <tr>
                          <td><i class="mdi mdi-account text-primary"></i> {{ $customer->name }}</td>
                          <td>{{ $customer->email }}</td>
                          <td>{{ $customer->phone }}</td>
                          <td>{{ $customer->address }}</td>
                          <td>
                            <a href="{{ route('edit.customer.form', $customer->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            {{-- Delete Form --}}
                            <form class="delete-form" data-id="{{ $customer->id }}" style="display:inline-block;">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn btn-danger btn-sm delete-btn">Delete</button>
                            </form>
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="5" class="text-center">No customers found.</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    {{-- Optional Chat Popup Box --}}
    <div class="CHAT_MESSAGE_POPUPBOX"></div>

    {{-- Back to Top Button --}}
    <div id="back-top" style="display: none;">
      <a title="Go to Top" href="#"><i class="ti-angle-up"></i></a>
    </div>

  </div>
</section>

@include('includes.footer')

{{-- AJAX Delete Functionality --}}
<script>
  $(document).ready(function () {
    $('.delete-btn').on('click', function (e) {
      e.preventDefault();

      if (!confirm('Are you sure you want to delete this customer?')) return;

      const form = $(this).closest('form');
      const customerId = form.data('id');
      const token = form.find('input[name="_token"]').val();

      $.ajax({
        url: `/delete-customer/${customerId}`,
        type: 'DELETE',
        data: { _token: token },
        success: function (res) {
          // Show success message on top of screen
          $('body').prepend(`
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;">
              ${res.message}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
            </div>
          `);

          // Refresh customer list page after 2 seconds
          setTimeout(() => {
            window.location.href = "{{ route('view.customers') }}";
          }, 2000);
        },
        error: function () {
          alert('Something went wrong.');
        }
      });
    });
  });
</script>
