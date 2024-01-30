@extends('admin.layouts.app')


@section('content')


	<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Shipping Management</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('shipping.create') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">

            {{-- Message --}}
                @include('admin.message')
            {{-- Message --}}

            <form action="" method="post" id="shippingForm" name="shippingForm">
            @csrf
            @method('POST')

                <div class="card">
                    <div class="card-body">
                        <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Country Names</label>
                                <select name="country" id="country" class="form-control">
                                    <option value="">Select a Country</option>
                                    @if($countries->isNotEmpty())
                                        @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                        <option value="rest_of_world">Rest of the world</option>
                                    @endif
                                </select>
                                <p></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="amount">Amount</label>
                            <input type="text" name="amount" id="amount" class="form-control" placeholder="Amount">
                            <p></p>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary float-right">Create</button>
                        </div>


                        </div>
                    </div>
                </div>
        </form>

        <div class="card">
            <div class="card-body">
                <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                        @if($shippingCharges->isNotEmpty())
                            @foreach ($shippingCharges as $shippingCharge)
                                <tr>
                                    <td>{{ $shippingCharge->id }}</td>
                                    <td>{{ ($shippingCharge->country_id == 'rest_of_world') ? 'Rest of the World' : $shippingCharge->name }}</td>
                                    <td>PKR: {{ $shippingCharge->amount }}</td>
                                    <td>
                                        <a href="{{ route('shipping.edit',$shippingCharge->id)}}" class="btn btn-primary">Edit</a>
                                        <a href="javascript:void(0);" onclick="deleteRecord({{ $shippingCharge->id }});" class="btn btn-danger">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->


@endsection

{{-- Custom JS --}}
@section('customJs')
    <script type="text/javascript">

        $("#shippingForm").submit(function (event) {
            event.preventDefault();
            var element = $(this);

            // Disabled the button
            $("button[type=submit]").prop('disabled',true);

            $.ajax({
                url: '{{  route("shipping.store")  }}',
                type: "post",
                data: element.serializeArray(),
                dataType: "json",
                success: function (response) {

                    // Disabled the button
                    $("button[type=submit]").prop('disabled',false);

                    if(response['status'] == true)
                    {

                        window.location.href="{{ route('shipping.create') }}";

                        $("#amount").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback').html("");

                    }
                    else
                    {
                        var errors = response['errors'];

                        if(errors.country)
                        {
                            $("#country").addClass('is-invalid')
                            .siblings("p").addClass('invalid-feedback').html(errors['country']);
                        }
                        else{
                            $("#country").removeClass('is-invalid')
                            .siblings('p').removeClass('invalid-feedback').html("");
                        }


                        if(errors.amount)
                        {
                            $("#amount").addClass('is-invalid')
                            .siblings("p").addClass('invalid-feedback').html(errors['amount']);
                        }
                        else{
                            $("#amount").removeClass('is-invalid')
                            .siblings('p').removeClass('invalid-feedback').html("");
                        }
                    }

                }, error: function(jqHXR, exception){
                    console.log("something went wrong");
                }
            })
        });

        // Shipping Delete method
        function deleteRecord(id){

            var url = "{{ route('shipping.delete','ID') }}";
            var newUrl = url.replace("ID",id);

            if(confirm('Are you sure your want to delete?'))

            $.ajax({
                type: "delete",
                url: newUrl,
                data: {},
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    if(response["status"]) {
                        window.location.href="{{ route('shipping.create') }}";
                    }
                }
            });
        }

    </script>
@endsection
{{-- Custom JS End here --}}
