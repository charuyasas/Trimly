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
        @if(session('message'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        {{-- Form Card --}}
        <div class="card-modern border-0 shadow-sm p-4" style="border-radius: 20px; background-color: #fff;">
          <h3 class="mb-4 text-center text-dark" style="font-weight: 700;">Add New Customer</h3>

          <form action="{{ route('add.customer') }}" method="POST" autocomplete="off">
            @csrf

            {{-- Name --}}
            <div class="form-group mb-3">
              <label for="name" class="form-label">👤 Full Name</label>
              <input type="text" name="name" id="name" class="form-control styled-input @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
              @error('name')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            {{-- Email --}}
            <div class="form-group mb-3">
              <label for="email" class="form-label">📧 Email Address</label>
              <input type="email" name="email" id="email" class="form-control styled-input @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
              @error('email')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            {{-- Phone --}}
            <div class="form-group mb-3">
              <label for="phone" class="form-label">📱 Phone Number</label>
              <input type="text" name="phone" id="phone" class="form-control styled-input @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
              @error('phone')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            {{-- Address --}}
            <div class="form-group mb-4">
              <label for="address" class="form-label">🏠 Address</label>
              <textarea name="address" id="address" rows="3" class="form-control styled-input @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
              @error('address')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            {{-- Buttons --}}
            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-gradient-primary px-4 py-2 rounded-pill">Save</button>
              <a href="{{ route('view.customers') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">Cancel</a>
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

{{-- Scripts --}}
<script>
  setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
      alert.classList.remove('show');
      alert.classList.add('fade');
    });
  }, 5000);
</script>
