@include('includes.header')
@include('includes.sidebar')

<section class="main_content dashboard_part large_header_bg">
  <div class="container-fluid g-0">

    {{-- Header --}}
    <div class="row">
      <div class="col-lg-12 p-0">
        <div class="header_iner d-flex justify-content-between align-items-center">
          {{-- Header content (left/right panels) --}}
        </div>
      </div>
    </div>

    {{-- Customer Edit Form --}}
    <div class="row justify-content-center mt-4">
      <div class="col-md-8">
        <div class="white_card card_height_100 mb_30">

          {{-- Title --}}
          <div class="white_card_header">
            <div class="box_header m-0">
              <div class="main-title">
                <h3 class="m-0">Edit Customer</h3>
              </div>
            </div>
          </div>

          <div class="white_card_body">
            <form id="editCustomerForm" autocomplete="off">
              @csrf
              @method('PUT')

              <div class="row">

                {{-- Full Name --}}
                <div class="col-lg-12">
                  <div class="common_input mb_15">
                    <label class="form-label" style="font-weight: 600; color: #415094;">Full Name</label>
                    <input type="text" name="name" value="{{ $customer->name }}" placeholder="Enter full name" required>
                    <small class="text-danger error-name"></small>
                  </div>
                </div>

                {{-- Email --}}
                <div class="col-lg-12">
                  <div class="common_input mb_15">
                    <label class="form-label" style="font-weight: 600; color: #415094;">Email Address</label>
                    <input type="email" name="email" value="{{ $customer->email }}" placeholder="Enter email" required>
                    <small class="text-danger error-email"></small>
                  </div>
                </div>

                {{-- Phone --}}
                <div class="col-lg-12">
                  <div class="common_input mb_15">
                    <label class="form-label" style="font-weight: 600; color: #415094;">Phone Number</label>
                    <input type="text" name="phone" value="{{ $customer->phone }}" placeholder="Enter phone number" required>
                    <small class="text-danger error-phone"></small>
                  </div>
                </div>

                {{-- Address --}}
                <div class="col-lg-12">
                  <div class="common_input mb_15">
                    <label class="form-label" style="font-weight: 600; color: #415094;">Address</label>
                    <input type="text" name="address" id="address" rows="3" placeholder="Enter address" required>
                    <small class="text-danger error-address"></small>
                  </div>
                </div>

                {{-- Buttons --}}
                <div class="col-12">
                  <div class="create_report_btn mt_30 d-flex justify-content-between">
                    <button type="submit" class="btn_1 radius_btn">Update</button>
                    <a href="{{ route('view.customers') }}" class="btn_1 radius_btn" style="background: #fff; color: #415094; border: 1px solid #e4e8f0;">Cancel</a>
                  </div>
                </div>

              </div>
            </form>

            {{-- Flash Message --}}
            <div id="flash-message" class="mt-3"></div>

          </div>
        </div>
      </div>
    </div>

  </div>
</section>

@include('includes.footer')

<script>
  $(document).ready(function () {
    $('#editCustomerForm').on('submit', function (e) {
      e.preventDefault();
      $('.text-danger').html('');
      $('#flash-message').html('');

      $.ajax({
        url: "{{ route('update.customer', $customer->id) }}",
        method: 'POST',
        data: $(this).serialize(),
        success: function (res) {
          $('#flash-message').html(`
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              ${res.message}
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
          `);

          setTimeout(() => {
            window.location.href = res.redirect;
          }, 2000);
        },
        error: function (xhr) {
          if (xhr.responseJSON && xhr.responseJSON.errors) {
            $.each(xhr.responseJSON.errors, function (key, val) {
              $('.error-' + key).html(val[0]);
            });
          }
        }
      });
    });

    setTimeout(() => {
      $('.alert').alert('close');
    }, 5000);
  });
</script>
