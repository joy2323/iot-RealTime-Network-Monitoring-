@extends('layout.app_login')

@section('content')

<div class="container" style="position:absolute;left: 50%;top: 50%;transform: translate(-50%, -50%);">
    <div class="row">
        <div class="col-md-6 col-sm-6" style="margin:auto;">
            <div class="alert alert-info alert-dismissible fade show" id="alerinfo">
                <strong>Info!</strong> Platform Update.<br>
                All login credentials on the previous platfrorm are valid
                <br> If having issues logging in<br>
                Please contact: <a href="mailto: iot@susejgroup.net">iot@susejgroup.net</a>
                <button type="button" class="close closeinfo" data-dismiss="alert">&times;</button>
            </div>
            <div class="card">
                <div class="card-header" style="text-align:center">
                    <h3><b>Adminstrative Login</b></h3>
                    <h4>RealTime Network Monitoring </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email"
                                class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password"
                                class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    required autocomplete="current-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <!-- <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label> -->
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" style="width:100px">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection