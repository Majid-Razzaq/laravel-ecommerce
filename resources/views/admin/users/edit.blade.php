@extends('admin.layouts.app')


@section('content')


	<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit User</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('users.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">

            <form action="" method="post" id="userForm" name="userForm">
            @csrf
            @method('POST')

                <div class="card">
                    <div class="card-body">
                        <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input value="{{ $user->name }}" type="text" name="name" id="name" class="form-control" placeholder="Name">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Slug">Email</label>
                                <input value="{{ $user->email }}" type="text" name="email" id="email" class="form-control" placeholder="Email">
                                <p></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Slug">Password</label>
                                <input  type="password" name="password" id="password" class="form-control" placeholder="Password">
                                <span>To change a password you have to enter a value, otherwise leave this field blank.</span>
                                <p></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Slug">Phone</label>
                                <input value="{{ $user->phone }}" type="text" name="phone" id="phone" class="form-control" placeholder="Phone">
                                <p></p>
                            </div>
                        </div>




                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option {{ ($user->status == 1) ? 'selected' : '' }} value="1">Active</option>
                                    <option {{ ($user->status == 0) ? 'selected' : '' }} value="0">Block</option>
                                </select>
                            </div>

                        </div>

                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
        </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->


@endsection

{{-- Custom JS --}}
@section('customJs')
    <script type="text/javascript">

        $("#userForm").submit(function (event) {
            event.preventDefault();
            var element = $(this);

            // Disabled the button
            $("button[type=submit]").prop('disabled',true);

            $.ajax({
                url: '{{  route("users.update",$user->id)  }}',
                type: "put",
                data: element.serializeArray(),
                dataType: "json",
                success: function (response) {

                    // Disabled the button
                    $("button[type=submit]").prop('disabled',false);

                    if(response["status"] == true)
                    {
                        $("#name").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback').html("");

                        $("#email").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback').html("");

                        $("#phone").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback').html("");

                        window.location.href="{{ route('users.index') }}";

                    }
                    else
                    {
                        var errors = response['errors'];
                        if(errors['name'])
                        {
                            $("#name").addClass('is-invalid')
                            .siblings('p').addClass('invalid-feedback').html(errors['name']);
                        }
                        else{
                            $("#name").removeClass('is-invalid')
                            .siblings('p').removeClass('invalid-feedback').html("");
                        }

                    // For Email
                        if(errors['email'])
                        {
                            $("#email").addClass('is-invalid')
                            .siblings('p').addClass('invalid-feedback').html(errors['email']);
                        }
                        else{
                            $("#email").removeClass('is-invalid')
                            .siblings('p').removeClass('invalid-feedback').html("");
                        }

                        // For Phone
                        if(errors['phone'])
                        {
                            $("#phone").addClass('is-invalid')
                            .siblings('p').addClass('invalid-feedback').html(errors['phone']);
                        }
                        else{
                            $("#phone").removeClass('is-invalid')
                            .siblings('p').removeClass('invalid-feedback').html("");
                        }

                    }

                }, error: function(jqHXR, exception){
                    console.log("something went wrong");
                }
            })
        });


    </script>
@endsection
{{-- Custom JS End here --}}
