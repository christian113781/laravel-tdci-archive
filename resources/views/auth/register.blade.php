<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>TDCHI - Register</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" href="assets/img/kaiadmin/favicon.ico" type="image/x-icon"/>

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {"families":["Public Sans:300,400,500,600,700"]},
            custom: {"families":["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['assets/css/fonts.min.css']},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins.min.css">
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css">
</head>
<body class="login bg-primary">
    <div class="wrapper wrapper-login">
        <div class="container container-login animated">
            <h3 class="text-center">Sign Up</h3>
            <form method="POST" action="{{ route('register') }}" class="login-form">
                @csrf

                <!-- First Name -->
                <div class="form-group">
                    <label for="firstname"><b>Firstname</b></label>
                    <input id="firstname" name="first_name" type="text" class="form-control" value="{{ old('first_name') }}" required>
                    @error('first_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="lastname"><b>Lastname</b></label>
                    <input id="lastname" name="last_name" type="text" class="form-control" value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email"><b>Email</b></label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password"><b>Password</b></label>
                    <div class="position-relative">
                        <input id="password" name="password" type="password" class="form-control" required>
                        <div class="show-password" onclick="togglePassword('password', this)">
                            <i class="icon-eye"></i>
                        </div>
                    </div>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation"><b>Confirm Password</b></label>
                    <div class="position-relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                        <div class="show-password" onclick="togglePassword('password_confirmation', this)">
                            <i class="icon-eye"></i>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row form-action">
                    <div class="col-md-6">
                        <a href="{{ route('login') }}" class="btn btn-danger btn-link w-100 fw-bold">Cancel</a>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Sign Up</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- JS Files -->
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/kaiadmin.min.js"></script>

    <script>
        // Toggle show/hide password
        function togglePassword(fieldId, iconElem) {
            const input = document.getElementById(fieldId);
            if(input.type === "password") {
                input.type = "text";
                iconElem.innerHTML = '<i class="icon-eye-off"></i>';
            } else {
                input.type = "password";
                iconElem.innerHTML = '<i class="icon-eye"></i>';
            }
        }

        
    </script>

    @if (session('status'))
    <div class="alert alert-warning">
        {{ session('status') }}
    </div>
@endif
</body>
</html>
