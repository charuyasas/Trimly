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

{{-- Customer Registration Form --}}
<div class="row justify-content-center mt-4">
  <div class="col-md-8">

    {{-- Flash Messages --}}
    <div id="flash-message"></div>

    {{-- Styled Form Card --}}
    <div class="white_card card_height_100 mb_30">
      <div class="white_card_header">
        <div class="box_header m-0">
          <div class="main-title">
            <h3 class="m-0" style="color: #415094; font-weight: 700;">Add New Customer</h3>
          </div>
        </div>
      </div>

      <div class="white_card_body">
        <form id="addCustomerForm" autocomplete="off">
          @csrf

          <div class="row">

            {{-- Name --}}
            <div class="col-lg-12">
              <div class="common_input mb_15">
                <label for="name" style="font-weight: 600; color: #415094;">Full Name</label>
                <input type="text" name="name" id="name" placeholder="Enter full name" required>
                <small class="text-danger error-name"></small>
              </div>
            </div>

            {{-- Email --}}
            <div class="col-lg-12">
              <div class="common_input mb_15">
                <label for="email" style="font-weight: 600; color: #415094;">Email Address</label>
                <input type="email" name="email" id="email" placeholder="Enter email" required>
                <small class="text-danger error-email"></small>
              </div>
            </div>

            {{-- Phone --}}
            <div class="col-lg-12">
              <div class="common_input mb_15">
                <label for="phone" style="font-weight: 600; color: #415094;">Phone Number</label>
                <input type="text" name="phone" id="phone" placeholder="Enter phone number" required>
                <small class="text-danger error-phone"></small>
              </div>
            </div>

            {{-- Address --}}
            <div class="col-lg-12">
              <div class="common_input mb_15">
                <label for="address" style="font-weight: 600; color: #415094;">Address</label>
                <input type="text" name="address" id="address" rows="3" placeholder="Enter address" required>
                <small class="text-danger error-address"></small>
              </div>
            </div>

            {{-- Buttons --}}
            <div class="col-12">
              <div class="create_report_btn mt_30 d-flex justify-content-between">
                <button type="submit" class="btn_1 radius_btn">Save</button>
                <a href="{{ route('view.customers') }}" class="btn_1 radius_btn" style="background: #fff; color: #415094; border: 1px solid #e4e8f0;">Cancel</a>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>

  </div>
</div>



  {{-- Chat Popup --}}
  <div class="CHAT_MESSAGE_POPUPBOX">
    {{-- Keep your existing chat content --}}
  </div>
</section>

@include('includes.footer')

{{-- Styles --}}
<style>
  .card-modern {
    background: #ffffff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  }

  .form-label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #343a40;
  }

  .styled-input {
    border-radius: 12px;
    padding: 12px;
    font-size: 15px;
    border: 1px solid #ced4da;
    transition: all 0.3s ease-in-out;
  }

  .styled-input:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 10px rgba(74, 144, 226, 0.2);
  }

  .btn-gradient-primary {
    background: linear-gradient(to right, #6a11cb, #2575fc);
    border: none;
    color: #fff;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-gradient-primary:hover {
    background: linear-gradient(to right, #5a00e0, #1c5ed9);
  }

  .btn-outline-secondary {
    font-weight: 500;
    color: #6c757d;
    border: 1px solid #ced4da;
  }

  .alert {
    border-radius: 10px;
  }

  .text-danger {
    font-size: 0.85rem;
    margin-top: 4px;
    display: block;
  }
</style>

{{-- jQuery + AJAX Script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function () {
    $('#addCustomerForm').on('submit', function (e) {
      e.preventDefault();

      // Clear previous messages
      $('.text-danger').html('');
      $('#flash-message').html('');

      $.ajax({
        url: "{{ route('add.customer') }}",
        method: "POST",
        data: $(this).serialize(),
        success: function (res) {
          $('#flash-message').html(`
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              ${res.message}
              <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
          `);

          // Reset form
          $('#addCustomerForm')[0].reset();

          // ✅ Delay then redirect
          setTimeout(() => {
            window.location.href = res.redirect;
          }, 2000); // 2 seconds delay
        },
        error: function (xhr) {
          const errors = xhr.responseJSON.errors;
          if (errors) {
            $.each(errors, function (key, val) {
              $('.error-' + key).html(val[0]);
            });
          }
        }
      });
    });

    // Auto dismiss flash alerts if needed
    setTimeout(() => {
      $('.alert').alert('close');
    }, 5000);
  });
</script>


