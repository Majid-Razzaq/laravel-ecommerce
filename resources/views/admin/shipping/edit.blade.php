@extends('admin.layouts.app')


@section('content')


	<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Shipping Management</h1>
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
                                        <option {{ ($shippingCharge->country_id == $country->id) ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                        <option {{ ($shippingCharge->country_id == 'rest_of_world') ? 'selected' : '' }} value="rest_of_world">Rest of the world</option>
                                    @endif
                                </select>
                                <p></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="amount">Amount</label>
                            <input value="{{ $shippingCharge->amount }}" type="text" name="amount" id="amount" class="form-control" placeholder="Amount">
                            <p></p>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary float-right">Update</button>
                        </div>


                        </div>
                    </div>
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

        $("#shippingForm").submit(function (event) {
            event.preventDefault();
            var element = $(this);

            // Disabled the button
            $("button[type=submit]").prop('disabled',true);

            $.ajax({
                url: '{{  route("shipping.update",$shippingCharge->id)  }}',
                type: "put",
                data: element.serializeArray(),
                dataType: "json",
                success: function (response) {

                    // Disabled the button
                    $("button[type=submit]").prop('disabled',false);

                    if(response["status"] == true)
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

    </script>
@endsection
{{-- Custom JS End here --}}
