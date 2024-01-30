<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Laravel Shop :: Administrative Panel</title>
		<!-- Google Font: Source Sans Pro -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="{{ asset('admin-assets/plugins/fontawesome-free/css/all.min.css') }} ">
		<!-- Theme style -->
		<link rel="stylesheet" href="{{ asset('admin-assets/css/adminlte.min.css')}}">
		<link rel="stylesheet" href="{{ asset('admin-assets/css/custom.css')}}">
	</head>
	<body class="hold-transition login-page">
		<div class="login-box">
			<!-- /.login-logo -->

            {{-- Session Message --}}
            @include('admin.message')
            {{-- Session Message --}}

			<div class="card card-outline card-primary">
			  	<div class="card-header text-center">
					<a href="#" class="h3">Administrative Panel</a>
			  	</div>
			  	<div class="card-body">
					<p class="login-box-msg">Sign in to start your session</p>
					<form action="{{ route('admin.authenticate') }}" method="post">
                        @csrf
				  		<div class="input-group mb-3">
							<input type="email" value="{{ old('email') }}" name="email" id="email" class="form-control @error('email') is-invalid  @enderror" placeholder="Email">
							<div class="input-group-append">
					  			<div class="input-group-text">
									<span class="fas fa-envelope"></span>
					  			</div>
							</div>
                            @error('email')
                            <p class="invalid-feedback">{{ $message }}</p>
                            @enderror
				  		</div>

				  		<div class="input-group mb-3">
							<input type="password" value="{{ old('password') }}" name="password" id="password" class="form-control @error('password') is-invalid  @enderror" placeholder="Password">
							<div class="input-group-append">
					  			<div class="input-group-text">
									<span class="fas fa-lock"></span>
					  			</div>
							</div>

                            @error('password')
                            <p class="invalid-feedback">{{ $message }}</p>
                            @enderror

				  		</div>
				  		<div class="row">
							<!-- <div class="col-8">
					  			<div class="icheck-primary">
									<input type="checkbox" id="remember">
									<label for="remember">
						  				Remember Me
									</label>
					  			</div>
							</div> -->
							<!-- /.col -->
							<div class="col-4">
					  			<button type="submit" class="btn btn-primary btn-block">Login</button>
							</div>
							<!-- /.col -->
				  		</div>
					</form>
		  			<p class="mb-1 mt-3">
				  		<a href="forgot-password.html">I forgot my password</a>
					</p>
			  	</div>
			  	<!-- /.card-body -->
			</div>
			<!-- /.card -->
		</div>
		<!-- ./wrapper -->
		<!-- jQuery -->
		<script src="plugins/jquery/jquery.min.js"></script>
		<!-- Bootstrap 4 -->
		<script src="{{ asset('admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }} "></script>
		<!-- AdminLTE App -->
		<script src="{{ asset('admin-assets/js/adminlte.min.js')}}  "></script>
		<!-- AdminLTE for demo purposes -->
		{{-- <script src="{{ asset('admin-assets/js/demo.js')}}"></script> --}}
	</body>
</html>
