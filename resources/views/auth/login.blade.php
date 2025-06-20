<!DOCTYPE html>
<html lang="zxx">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>LOGIN</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap1.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/themefy_icon/themify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/font_awesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/scroll/scrollable.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/metisMenu.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style1.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/colors/default.css') }}" id="colorSkinCSS">
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
                            <x-validation-errors class="mb-4" />
                            
                            @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ session('status') }}
                            </div>
                            @endif
                            
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <input id="email" type="email" name="email" :value="old('email')" class="form-control" placeholder="Enter your email">
                                </div>
                                <div class="mb-3">
                                    <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control" placeholder="Password">
                                </div>
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn_1 text-center">Log In</button>
                                </div>
                                
                                <div class="text-center">
                                    @if (Route::has('password.request'))
                                    <a class="pass_forget_btn" href="{{ route('password.request') }}">
                                        {{ __('Forgot your password?') }}
                                    </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <p class="small text-muted">2020 © Developed by
                            <a href="#">ECHO DATA</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery1-3.4.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap1.min.js') }}"></script>
    <script src="{{ asset('assets/js/metisMenu.js') }}"></script>
    <script src="{{ asset('assets/vendors/scroll/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/scroll/scrollable-custom.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    
</body>
</html>
