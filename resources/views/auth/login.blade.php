@extends('layouts.app')
@section('content')
<div class="container d-flex justify-content-center align-items-center login-container" style="min-height: 100vh;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <!-- <div class="card-header text-center"></div> -->

                <div class="card-body">
                    <h1 class='text-center'>{{ config('app.name', 'Laravel') }}</h1>
                    <p class='text-center'><b>{{ config('app.fullname', 'Laravel') }}</b></p><hr>
                    <p class='text-center'>Login</p>
                    <div class="row w-100 justify-content-center">
                            <form method="POST" action="{{ route('login') }}">
                            @csrf
                                <input id="email" type="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                </br>
                                <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                </br>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror


                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>
                                </div>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
