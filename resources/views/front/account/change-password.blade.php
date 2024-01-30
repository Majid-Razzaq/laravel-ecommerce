@extends('front.layouts.app')

@section('content')


<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
                <li class="breadcrumb-item">Settings</li>
            </ol>
        </div>
    </div>
</section>

<section class=" section-11 ">
    <div class="container  mt-5">
        <div class="row">
            <div class="col-md-12">
            {{-- include message file --}}
                @include('front.account.common.message')
            {{-- include message file --}}
            </div>

            <div class="col-md-3">
                @include('front.account.common.sidebar')
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0 pt-2 pb-2">Change Password</h2>
                    </div>
                    <form action="" method="post" id="changePasswordForm" name="changePasswordForm">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="mb-3">
                                <label for="name">Old Password</label>
                                <input type="password" name="old_password" id="old_password" placeholder="Old Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-3">
                                <label for="name">New Password</label>
                                <input type="password" name="new_password" id="new_password" placeholder="New Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-3">
                                <label for="name">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="d-flex">
                                <button id="submit" name="submit" type="submit" class="btn btn-dark">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection



@section('customJS')
<script>
    $("#changePasswordForm").submit(function (e) {

        e.preventDefault();
        $("#submit").prop('disabled',true);

        $.ajax({
            type: "post",
            url: "{{ route('account.changePassword') }}",
            data: $(this).serializeArray(),
            dataType: "json",
            success: function (response) {
                $("#submit").prop('disabled',false);
                if(response.status == true)
                {
                    $("#old_password").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback');

                    $("#new_password").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback');

                    $("#confirm_password").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback');

                    window.location.href = '{{ route("account.showChangePasswordForm") }}';

                }else{
                    var errors = response.errors;
                    if(errors.old_password){
                        $("#old_password").addClass('is-invalid').siblings('p').html(errors.old_password).addClass('invalid-feedback');
                    }
                    else{
                        $("#old_password").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback');
                    }
                    if(errors.new_password){
                        $("#new_password").addClass('is-invalid').siblings('p').html(errors.new_password).addClass('invalid-feedback');
                    }else{
                        $("#new_password").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback');
                    }
                    if(errors.confirm_password){
                        $("#confirm_password").addClass('is-invalid').siblings('p').html(errors.confirm_password).addClass('invalid-feedback');
                    }else{
                        $("#confirm_password").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback');
                    }
                }
            }
        });
    });

</script>

@endsection
