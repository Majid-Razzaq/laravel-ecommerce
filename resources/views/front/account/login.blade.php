@extends('front.layouts.app')

@section('content')

<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                <li class="breadcrumb-item">Login</li>
            </ol>
        </div>
    </div>
</section>

<section class=" section-10">
    <div class="container">

        {{-- Success Message from session message --}}
        @if(Session::has('success'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('success') }}
            </div>
        @endif
       {{-- Success Message Code End Here --}}

        {{-- Success Message from session message --}}
            @if(Session::has('error'))
            <div class="alert alert-danger" role="alert">
                {{ Session::get('error') }}
            </div>
        @endif
       {{-- Success Message Code End Here --}}

        <div class="login-form">
            <form action="{{ route('account.authenticate') }}" method="post">
                @csrf
                <h4 class="modal-title">Login to Your Account</h4>
                <div class="form-group">
                    <input type="text" class="form-control @error('email') is-invalid @enderror" placeholder="Email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <input type="password" class="form-control @error('email') is-invalid @enderror" placeholder="Password" name="password">
                    @error('password')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group small">
                    <a href="{{ route('front.forgotPassword')}}" class="forgot-link">Forgot Password?</a>
                </div>
                <input type="submit" class="btn btn-dark btn-block btn-lg" value="Login">
            </form>
            <div class="text-center small">Don't have an account? <a href="{{ route('account.register') }}">Sign up</a></div>
        </div>
    </div>
</section>

@endsection

@section('customJS')
@endsection
