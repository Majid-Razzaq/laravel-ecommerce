@extends('front.layouts.app')

@section('content')

<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                <li class="breadcrumb-item">Checkout</li>
            </ol>
        </div>
    </div>
</section>

<section class="section-9 pt-4">
    <div class="container">

        <form action="" method="post" id="orderForm" name="orderForm">

            <div class="row">
                <div class="col-md-8">
                    <div class="sub-title">
                        <h2>Shipping Address</h2>
                    </div>
                    <div class="card shadow-lg border-0">
                        <div class="card-body checkout-form">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="first_name" id="first_name" value="{{ (!empty($customerAddress)) ? $customerAddress->first_name : ''  }}" class="form-control" placeholder="First Name">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="last_name" id="last_name" value="{{ (!empty($customerAddress)) ? $customerAddress->last_name : ''  }}" class="form-control" placeholder="Last Name">
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="email" id="email" value="{{ (!empty($customerAddress)) ? $customerAddress->email : ''  }}" class="form-control" placeholder="Email">
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <select name="country" id="country" class="form-control">
                                            <option value="">Select a Country</option>
                                            @if($countries->isNotEmpty())
                                                @foreach ($countries as $country)
                                                <option {{ (!empty($customerAddress) && $customerAddress->country_id == $country->id) ? 'selected' : ''  }} value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <textarea name="address" id="address" cols="30" rows="3" placeholder="Address" class="form-control">{{ (!empty($customerAddress)) ? $customerAddress->address : ''  }}</textarea>
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="appartment" id="appartment" value="{{ (!empty($customerAddress)) ? $customerAddress->apartment : ''  }}" class="form-control" placeholder="Apartment, suite, unit, etc. (optional)">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <input type="text" name="city" id="city" class="form-control" value="{{ (!empty($customerAddress)) ? $customerAddress->city : ''  }}" placeholder="City">
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <input type="text" name="state" id="state" value="{{ (!empty($customerAddress)) ? $customerAddress->state : ''  }}" class="form-control" placeholder="State">
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <input type="text" name="zip" id="zip" value="{{ (!empty($customerAddress)) ? $customerAddress->zip : ''  }}" class="form-control" placeholder="Zip">
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="mobile" id="mobile" value="{{ (!empty($customerAddress)) ? $customerAddress->mobile : ''  }}" class="form-control" placeholder="Mobile No.">
                                        <p></p>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <textarea name="order_notes" id="order_notes" cols="30" rows="2" placeholder="Order Notes (optional)" class="form-control"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sub-title">
                        <h2>Order Summery</h3>
                    </div>
                    <div class="card cart-summery">
                        <div class="card-body">
                            @foreach (Cart::content() as $item)

                            <div class="d-flex justify-content-between pb-2">
                                <div class="h6">{{ $item->name }} X {{ $item->qty }}</div>
                                <div class="h6">Pkr:{{ $item->price * $item->qty }} </div>
                            </div>

                            @endforeach

                            <div class="d-flex justify-content-between summery-end">
                                <div class="h6"><strong>Subtotal</strong></div>
                                <div class="h6"><strong>PKR: {{ Cart::subtotal() }}</strong></div>
                            </div>

                            <div class="d-flex justify-content-between summery-end">
                                <div class="h6"><strong>Discount</strong></div>
                                <div class="h6"><strong id="discount_value">PKR: {{ $discount }}</strong></div>
                            </div>

                            <div class="d-flex justify-content-between mt-2">
                                <div class="h6"><strong>Shipping</strong></div>
                                <div class="h6"><strong id="shippingAmount">PKR: {{ number_format($totalShippingCharge,2)}}</strong></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2 summery-end">
                                <div class="h5"><strong>Total</strong></div>
                                <div class="h5"><strong id="grandTotal">PKR: {{ number_format($grandTotal,2) }}</strong></div>
                            </div>
                        </div>
                    </div>

                    <div class="input-group apply-coupon mt-4">
                        <input type="text" placeholder="Coupon Code" class="form-control" name="discount_code" id="discount_code">
                        <button class="btn btn-dark" type="button" id="apply-discount">Apply Coupon</button>
                    </div>

                    <div id="discount-response-wrapper">
                        @if (Session::has('code'))
                        <div class="mt-4" id="discount-response">
                            <strong>{{ session()->get('code')->code }}</strong>
                            <a class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>
                        </div>
                         @endif
                    </div>




                    <div class="card payment-form ">

                        <h3 class="card-title h5 mb-3">Payment Method</h3>

                        <div class="">
                            <input checked type="radio" name="payment_method" value="cod" id="payment_method_one">
                            <label for="payment_method_one" class="form-check-label">COD</label>
                        </div>

                        <div class="">
                            <input type="radio" name="payment_method" value="cod" id="payment_method_two">
                            <label for="payment_method_two" class="form-check-label">Stripe</label>
                        </div>


                        <div class="card-body p-0 d-none mt-3" id="cart-payment-form">
                            <div class="mb-3">
                                <label for="card_number" class="mb-2">Card Number</label>
                                <input type="text" name="card_number" id="card_number" placeholder="Valid Card Number" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="expiry_date" class="mb-2">Expiry Date</label>
                                    <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YYYY" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="expiry_date" class="mb-2">CVV Code</label>
                                    <input type="text" name="expiry_date" id="expiry_date" placeholder="123" class="form-control">
                                </div>
                            </div>

                        </div>

                        <div class="pt-4">
                            <button type="submit" class="btn-dark btn btn-block w-100">Pay Now</button>
                        </div>

                    </div>


                    <!-- CREDIT CARD FORM ENDS HERE -->

                </div>
            </div>

        </form>

    </div>
</section>

@endsection

@section('customJS')

    <script>
        $("#payment_method_one").click(function(){
            if($(this).is(":checked") == true){
                $("#cart-payment-form").addClass('d-none');
            }
        });

        $("#payment_method_two").click(function(){
            if($(this).is(":checked") == true){
                $("#cart-payment-form").removeClass('d-none');
            }
        });

        $("#orderForm").submit(function(event)
        {
            event.preventDefault();

            $('button[type="submit"]').prop('disabled', true);


            $.ajax({
                type: "post",
                url: "{{route('front.processCheckout') }}",
                data: $(this).serializeArray(),
                dataType: "json",
                success: function (response) {
                    var errors = response.errors;

                    $('button[type="submit"]').prop('disabled',false);

                    // Thank you in reponse
                    if(response.status == false){

                        // For first name
                        if(errors.first_name){
                            $("#first_name").addClass('is-invalid')
                            .siblings("p")
                            .addClass('invalid-feedback')
                            .html(errors.first_name);
                        }
                        else{
                            $("#first_name").removeClass('is-invalid')
                            .siblings("p")
                            .removeClass('invalid-feedback')
                            .html('');
                        }

                        // For last name
                        if(errors.last_name){
                            $("#last_name").addClass('is-invalid')
                            .siblings("p")
                            .addClass('invalid-feedback')
                            .html(errors.last_name);
                        }
                        else{
                            $("#last_name").removeClass('is-invalid')
                            .siblings("p")
                            .removeClass('invalid-feedback')
                            .html('');
                        }


                        // For last name
                        if(errors.email){
                            $("#email").addClass('is-invalid')
                            .siblings("p")
                            .addClass('invalid-feedback')
                            .html(errors.email);
                        }
                        else{
                            $("#email").removeClass('is-invalid')
                            .siblings("p")
                            .removeClass('invalid-feedback')
                            .html('');
                        }

                        // For country
                        if(errors.country){
                            $("#country").addClass('is-invalid')
                            .siblings("p")
                            .addClass('invalid-feedback')
                            .html(errors.country);
                        }
                        else{
                            $("#country").removeClass('is-invalid')
                            .siblings("p")
                            .removeClass('invalid-feedback')
                            .html('');
                        }

                        // For address
                        if(errors.address){
                            $("#address").addClass('is-invalid')
                            .siblings("p")
                            .addClass('invalid-feedback')
                            .html(errors.address);
                        }
                        else{
                            $("#address").removeClass('is-invalid')
                            .siblings("p")
                            .removeClass('invalid-feedback')
                            .html('');
                        }

                        // For City
                        if(errors.city){
                        $("#city").addClass('is-invalid')
                        .siblings("p")
                        .addClass('invalid-feedback')
                        .html(errors.city);
                        }
                        else{
                            $("#city").removeClass('is-invalid')
                            .siblings("p")
                            .removeClass('invalid-feedback')
                            .html('');
                        }

                    // For state
                        if(errors.state){
                            $("#state").addClass('is-invalid')
                            .siblings("p")
                            .addClass('invalid-feedback')
                            .html(errors.state);
                        }
                        else{
                            $("#state").removeClass('is-invalid')
                            .siblings("p")
                            .removeClass('invalid-feedback')
                            .html('');
                        }


                        // For Zip
                        if(errors.zip){
                        $("#zip").addClass('is-invalid')
                            .siblings("p")
                            .addClass('invalid-feedback')
                            .html(errors.zip);
                        }
                        else{
                            $("#zip").removeClass('is-invalid')
                            .siblings("p")
                            .removeClass('invalid-feedback')
                            .html('');
                        }


                        // For Mobile
                        if(errors.mobile){
                        $("#mobile").addClass('is-invalid')
                            .siblings("p")
                            .addClass('invalid-feedback')
                            .html(errors.mobile);
                        }
                        else{
                            $("#mobile").removeClass('is-invalid')
                            .siblings("p")
                            .removeClass('invalid-feedback')
                            .html('');
                        }
                }
                else
                {
                    window.location.href = "{{ url('thanks/') }}/"+response.orderId;
                }

                }
            });
        });


        // In this Ajax When user change its country location shipping charges will be update on the spot

        $("#country").change(function () {
            $.ajax({
            type: "post",
            url: "{{ route('front.getOrderSummery') }}",
            data: { country_id: $(this).val() },
            dataType: "json",
            success: function (response) {
                // Handle the success response here
            if(response.status == true){
                $("#shippingAmount").html('PKR: '+response.shippingCharge);
                $("#grandTotal").html('PKR: '+response.grandTotal);
            }

        }
    });
});


    // When discount button will be click
    $("#apply-discount").click(function(){
        $.ajax({
            type: "post",
            url: "{{ route('front.applyDiscount') }}",
            data: {code: $("#discount_code").val(), country_id: $("#country").val() },
            dataType: "json",
            success: function (response) {
                if(response.status == true)
                {
                    $("#shippingAmount").html('PKR: '+response.shippingCharge);
                    $("#grandTotal").html('PKR: '+response.grandTotal);
                    $("#discount_value").html('PKR: '+response.discount);
                    $("#discount-response-wrapper").html(response.discontString);
                }
                else{
                    $("#discount-response-wrapper").html("<span class='text-danger'>"+ response.message+"</span>");
                }
            }
        });
    });

    $('body').on('click',"#remove-discount",function() {
        $.ajax({
            type: "post",
            url: "{{ route('front.removeCoupon') }}",
            data: { country_id: $("#country").val() },
            dataType: "json",
            success: function (response) {
                if(response.status == true)
                {
                    $("#shippingAmount").html('PKR: '+response.shippingCharge);
                    $("#grandTotal").html('PKR: '+response.grandTotal);
                    $("#discount_value").html('PKR: '+response.discount);
                    $("#discount-response").html('');
                    $("#discount_code").val('');
                }
            }
        });
      });

    </script>

@endsection


