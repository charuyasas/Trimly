<!DOCTYPE html>
<html lang="zxx">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>LOGIN</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="crm_body_bg">

<section class="main_content dashboard_part large_header_bg" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;padding-left: 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="modal-content cs_modal shadow rounded">
                    <div class="modal-header justify-content-center theme_bg_1 rounded-top">
                        <h5 class="modal-title text_white">Log in</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email" value="{{ old('email') }}">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn_1 text-center">Log In</button>
                            </div>
                            <p class="text-center">Need an account?
                                <a href="#" data-toggle="modal" data-target="#sing_up" data-dismiss="modal">Sign Up</a>
                            </p>
                            <div class="text-center">
                                <a href="#" class="pass_forget_btn" data-toggle="modal" data-target="#forgot_password" data-dismiss="modal">Forget Password?</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <p class="small text-muted">{{ date('Y') }} © Developed by
                        <a href="#">ECHO DATA</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scripts -->
</body>
</html>
