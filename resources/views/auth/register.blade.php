<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Bootstrap 4 Admin &amp; Dashboard Template">
    <meta name="author" content="RentEasy">
    <title>{{ env('APP_NAME') }}</title>
    <link href="{{ asset('ui-kit/css/modern.css') }}" rel="stylesheet">
    <script src="{{ asset('ui-kit/js/settings.js') }}"></script>
    <!-- Favicon -->
    {{--    <link rel="shortcut icon" href="{{ asset('ui-kit/assets/images/icon-dark.ico') }}" />--}}
</head>

<body class="theme-blue">
<div class="splash active">
    <div class="splash-icon"></div>
</div>

<main class="main h-100 w-100">
    <div class="container h-100">
        <div class="row h-100">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                <div class="d-table-cell align-middle">

                    <div class="text-center mt-4">
                        <h1 class="h2">Welcome Back</h1>
                        <p class="lead">
                            Sign up to create an account
                        </p>
                        @include('layouts.flash-message')
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="m-sm-4">
                                <div class="text-center">
                                    <img height="200" width="80%" src="https://www.renttrack.com/hubfs/RentTrack/website-assets/images/logo.svg" alt="rent_real_estate" class="rounded" />
                                </div>
                                <form action="{{ route('onboard.create') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="user_type">Sign Up As</label>
                                        <select name="user_type" id="user_type" class="form-control">
                                            <option value="1">Landlord/ Owner</option>
                                            <option value="2">Agent</option>
                                            <option value="3">Tenant</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Enter Name</label>
                                        <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" required/>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="msisdn">Enter Phone/ Msisdn</label>
                                        <input id="msisdn" class="form-control @error('msisdn') is-invalid @enderror" type="text" name="msisdn" value="{{ old('msisdn') }}" required/>
                                        @error('msisdn')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Enter Email</label>
                                        <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required/>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="physical_address">Enter Physical Address</label>
                                        <input id="physical_address" class="form-control @error('physical_address') is-invalid @enderror" type="text" name="physical_address" value="{{ old('physical_address') }}" required/>
                                        @error('physical_address')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="national_id_no">Enter National ID/ Passport Number</label>
                                        <input id="national_id_no" class="form-control @error('national_id_no') is-invalid @enderror" type="text" name="national_id_no" value="{{ old('national_id_no') }}" required/>
                                        @error('national_id_no')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div>
                                        <div class="custom-control custom-checkbox align-items-center">
                                            <input id="customControlInline" type="checkbox" class="custom-control-input" value="remember-me" required>
                                            <label class="custom-control-label text-small" for="customControlInline">I agree with the
                                                <a href="javascript: void(0);">terms & conditions</a>
                                            </label>
                                        </div>
                                    </div>
                                    <small>
                                        <a class="text-success" href="javascript: void(0);">Temporary password will be sent to your email upon account approval</a>
                                    </small>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-lg btn-primary btn-block">Sign Up</button>
                                    </div>
                                    <div class="text-center mt-3">
                                        <small>
                                            <a href="{{ route('login') }}">{{ __('Already Registered? Sign In Now') }}</a>
                                        </small>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<svg width="0" height="0" style="position:absolute">
    <defs>
        <symbol viewBox="0 0 512 512" id="ion-ios-pulse-strong">
            <path
                d="M448 273.001c-21.27 0-39.296 13.999-45.596 32.999h-38.857l-28.361-85.417a15.999 15.999 0 0 0-15.183-10.956c-.112 0-.224 0-.335.004a15.997 15.997 0 0 0-15.049 11.588l-44.484 155.262-52.353-314.108C206.535 54.893 200.333 48 192 48s-13.693 5.776-15.525 13.135L115.496 306H16v31.999h112c7.348 0 13.75-5.003 15.525-12.134l45.368-182.177 51.324 307.94c1.229 7.377 7.397 11.92 14.864 12.344.308.018.614.028.919.028 7.097 0 13.406-3.701 15.381-10.594l49.744-173.617 15.689 47.252A16.001 16.001 0 0 0 352 337.999h51.108C409.973 355.999 427.477 369 448 369c26.511 0 48-22.492 48-49 0-26.509-21.489-46.999-48-46.999z">
            </path>
        </symbol>
    </defs>
</svg>
<script src="{{ asset('ui-kit/js/app.js') }}"></script>

</body>

</html>
