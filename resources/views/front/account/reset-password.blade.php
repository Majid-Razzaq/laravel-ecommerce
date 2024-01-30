@extends('front.layouts.app')

@section('content')

<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                <li class="breadcrumb-item">Reset Password</li>
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
            <form action="{{ route('front.ProcessResetPassword') }}" method="post">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <h4 class="modal-title">Reset Password</h4>

                <div class="form-group">
                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" placeholder="New Password" id="new_password" name="new_password">
                    @error('new_password')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" placeholder="Confirm Password" id="confirm_password" name="confirm_password">
                    @error('confirm_password')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>

                <input type="submit" class="btn btn-dark btn-block btn-lg" value="Update Password">
            </form>
            <div class="text-center small"><a class="float-end p-2" href="{{ route('account.login') }}">Click here to Login</a></div>
        </div>
    </div>
</section>

@endsection

@section('customJS')
@endsection
