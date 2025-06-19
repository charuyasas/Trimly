@include('includes.header')
@include('includes.sidebar')

<section class="main_content dashboard_part large_header_bg">
  <div class="container-fluid g-0">
    {{-- Top Bar --}}
    <div class="row">
      <div class="col-lg-12 p-0">
        <div class="header_iner d-flex justify-content-between align-items-center">
          {{-- Left --}}
          <div class="sidebar_icon d-lg-none"><i class="ti-menu"></i></div>
          <div class="line_icon open_miniSide d-none d-lg-block">
            <img src="{{ asset('assets/img/line_img.png') }}" alt="">
          </div>
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

          {{-- Right --}}
          <div class="header_right d-flex justify-content-between align-items-center">
            <div class="header_notification_warp d-flex align-items-center">
              <li>
                <a class="bell_notification_clicker" href="#">
                  <img src="{{ asset('assets/img/icon/bell.svg') }}" alt="">
                  <span>2</span>
                </a>
                <div class="Menu_NOtification_Wrap">
                  <div class="notification_Header"><h4>Notifications</h4></div>
                  <div class="Notification_body">
                    @for($i=1; $i<=3; $i++)
                      <div class="single_notify d-flex align-items-center">
                        <div class="notify_thumb"><a href="#"><img src="{{ asset('assets/img/staf/' . $i . '.png') }}" alt=""></a></div>
                        <div class="notify_content"><a href="#"><h5>Notification Title</h5></a><p>Lorem ipsum dolor sit amet</p></div>
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
              <li><a class="CHATBOX_open" href="#"><img src="{{ asset('assets/img/icon/msg.svg') }}" alt=""><span>2</span></a></li>
            </div>

            <div class="profile_info">
              <img src="{{ asset('assets/img/client_img.png') }}" alt="#">
              <div class="profile_info_iner">
                <div class="profile_author_name">
                  <p>Neurologist</p><h5>Dr. Robar Smith</h5>
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

    {{-- ✅ Middle: Customer List Section --}}
    <div class="row justify-content-center mt-4">
      <div class="col-md-10">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">Customer List</h4>
              <a href="{{ route('add.customer.form') }}" class="btn btn-primary">Add New Customer</a>
            </div>

            {{-- Flash Message --}}
            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
               {{ session('message') }}
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span>&times;</span>
               </button>
            </div>

            @endif

            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead class="thead-light">
                  <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($customers as $customer)
                    <tr>
                      <td><i class="mdi mdi-account text-primary"></i> {{ $customer->name }}</td>
                      <td>{{ $customer->email }}</td>
                      <td>{{ $customer->phone }}</td>
                      <td>{{ $customer->address }}</td>
                      <td>
                        <a href="{{ route('edit.customer.form', $customer->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('delete.customer', $customer->id) }}" method="POST" style="display:inline-block;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this customer?')">Delete</button>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="5" class="text-center">No customers found.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>

    {{-- Chat Component --}}
    <div class="CHAT_MESSAGE_POPUPBOX">
      {{-- ... your full chat popup code remains unchanged ... --}}
    </div>

    {{-- Back to Top --}}
    <div id="back-top" style="display: none;">
      <a title="Go to Top" href="#"><i class="ti-angle-up"></i></a>
    </div>

  </div>
</section>

@include('includes.footer')
