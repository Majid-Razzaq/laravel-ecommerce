<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
         {{-- Csrf Token --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">
		<title>Laravel Shop :: Administrative Panel</title>
		<!-- Google Font: Source Sans Pro -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="{{ asset('admin-assets/plugins/fontawesome-free/css/all.min.css') }} ">
		<!-- Theme style -->
		<link rel="stylesheet" href="{{ asset('admin-assets/css/adminlte.min.css')}} ">
        {{-- DropZone file --}}
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/dropzone/min/dropzone.min.css')}}">
        {{-- custom css --}}
        <link rel="stylesheet" href="{{ asset('admin-assets/css/custom.css')}}">

        {{-- DateTime Picker css --}}
        <link rel="stylesheet" href="{{ asset('admin-assets/css/datetimepicker.css')}}">

        {{-- Summernote Link --}}
		<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
        {{-- Select2 Library --}}
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/select2/css/select2.min.css')}}">

    </head>
	<body class="hold-transition sidebar-mini">
		<!-- Site wrapper -->
		<div class="wrapper">
			<!-- Navbar -->
			<nav class="main-header navbar navbar-expand navbar-white navbar-light">
				<!-- Right navbar links -->
				<ul class="navbar-nav">
					<li class="nav-item">
					  	<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
					</li>
				</ul>
				<div class="navbar-nav pl-2">
					<!-- <ol class="breadcrumb p-0 m-0 bg-white">
						<li class="breadcrumb-item active">Dashboard</li>
					</ol> -->
				</div>

				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link" data-widget="fullscreen" href="#" role="button">
							<i class="fas fa-expand-arrows-alt"></i>
						</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link p-0 pr-3" data-toggle="dropdown" href="#">
							<img src="{{ asset('admin-assets/img/avatar5.png') }} " class='img-circle elevation-2' width="40" height="40" alt="">
						</a>
						<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-3">
							<h4 class="h4 mb-0"><strong>{{ Auth::guard('admin')->user()->name }}</strong></h4>
							<div class="mb-3">{{ Auth::guard('admin')->user()->email }}</div>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">
								<i class="fas fa-user-cog mr-2"></i> Settings
							</a>
							<div class="dropdown-divider"></div>
							<a href="{{ route('setting.showChangePasswordForm') }}" class="dropdown-item">
								<i class="fas fa-lock mr-2"></i> Change Password
							</a>
							<div class="dropdown-divider"></div>
							<a href="{{ route('admin.logout') }}" class="dropdown-item text-danger">
								<i class="fas fa-sign-out-alt mr-2"></i> Logout
							</a>
						</div>
					</li>
				</ul>
			</nav>
			<!-- /.navbar -->

            {{-- SideBar --}}
            @include('admin.layouts.sideBar')
            {{-- SideBar --}}

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">

                @yield('content')

			</div>
			<!-- /.content-wrapper -->
			<footer class="main-footer">

				<strong>Copyright &copy; 2014-2022 AmazingShop All rights reserved.
			</footer>

		</div>
		<!-- ./wrapper -->


        <!-- jQuery -->
		<script src="{{ asset('admin-assets/plugins/jquery/jquery.min.js') }} "></script>

        {{-- dateTime picker JS file --}}
		<script src="{{ asset('admin-assets/js/datetimepicker.js') }} "></script>


        {{-- Select2 JS file --}}
		<script src="{{ asset('admin-assets/plugins/select2/js/select2.min.js') }} "></script>

        {{-- Summernote --}}
        <script src="{{ asset('admin-assets/plugins/summernote/summernote.min.js')}}"></script>


        <!-- Dropzone js file -->
        <script src="{{ asset('admin-assets/plugins/dropzone/min/dropzone.min.js')}}"></script>
        <script src="{{ asset('admin-assets/plugins/dropzone/dropzone.js')}}"></script>

		<!-- Bootstrap 4 -->
		<script src="{{ asset('admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }} "></script>
		<!-- AdminLTE App -->
		<script src="{{ asset('admin-assets/js/adminlte.min.js') }} "></script>

		<!-- AdminLTE for demo purposes -->
        <script src="{{ asset('admin-assets/js/demo.js') }} "></script>

        <script type="text/javascript">
            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            });

			$(document).ready(function () {
				$(".summernote").summernote({
                    height:250,
                });
			});
        </script>

    {{-- add Custom Js --}}
    @yield('customJs')

	</body>
</html>
