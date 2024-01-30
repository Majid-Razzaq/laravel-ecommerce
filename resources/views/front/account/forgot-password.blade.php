@extends('front.layouts.app')

@section('content')

<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                <li class="breadcrumb-item">Forgot Password</li>
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
            <form action="{{ route('front.processForgotPassword') }}" method="post">
                @csrf
                <h4 class="modal-title">Forgot Password</h4>

                <div class="form-group">
                    <input type="text" class="form-control @error('email') is-invalid @enderror" placeholder="Email" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>

                <input type="submit" class="btn btn-dark btn-block btn-lg" value="Submit">
            </form>
            <div class="text-center small"><a class="float-end p-2" href="{{ route('account.login') }}">Click here to Login</a></div>
        </div>
    </div>
</section>

@endsection

@section('customJS')
@endsection
