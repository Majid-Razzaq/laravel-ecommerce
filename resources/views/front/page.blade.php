@extends('front.layouts.app')

@section('content')

<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                <li class="breadcrumb-item">{{ $page->name }}</li>
            </ol>
        </div>
    </div>
</section>

    @if ($page->slug == 'contact-us')
    <section class=" section-10">
        <div class="container">
            <div class="section-title mt-5 ">
                <h2>{{ $page->name }}</h2>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row">
                {{-- Success sessions message --}}
                <div class="col-md-12">
                    @if (Session::has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success'); }}
                        </div>
                    @endif
                </div>
               {{-- Success sessions message --}}
                <div class="col-md-6 mt-3 pe-lg-5">
                    {!! $page->content !!}
                </div>

                <div class="col-md-6">
                    <form class="shake" role="form" method="post" id="contactForm" name="contactForm">
                        <div class="mb-3">
                            <label class="mb-2" for="name">Name</label>
                            <input class="form-control" id="name" type="text" name="name" data-error="Please enter your name">
                            <p class="help-block with-errors"></p>
                        </div>

                        <div class="mb-3">
                            <label class="mb-2" for="email">Email</label>
                            <input class="form-control" id="email" type="email" name="email" data-error="Please enter your Email">
                            <p class="help-block with-errors"></p>
                        </div>

                        <div class="mb-3">
                            <label class="mb-2">Subject</label>
                            <input class="form-control" id="subject" type="text" name="subject" data-error="Please enter your message subject">
                            <p class="help-block with-errors"></p>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="mb-2">Message</label>
                            <textarea class="form-control" rows="3" id="message" name="message" data-error="Write your message"></textarea>
                            <p class="help-block with-errors"></p>
                        </div>

                        <div class="form-submit">
                            <button class="btn btn-dark" type="submit" name="submit" id="submit"><i class="material-icons mdi mdi-message-outline"></i> Send Message</button>
                            <div id="msgSubmit" class="h3 text-center hidden"></div>
                            <div class="clearfix"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @else

    <section class=" section-10">
        <div class="container">
            <h1 class="my-3">{{ $page->name }}</h1>

            {!! $page->content !!}

        </div>
    </section>

    @endif

@endsection

@section('customJS')

    <script>
        $("#contactForm").submit(function (e) {
            e.preventDefault();

            $("#submit").prop("disabled",true);

            $.ajax({
                type: "post",
                url: "{{ route('front.sendContactEmail') }}",
                data: $(this).serializeArray(),
                dataType: "json",

                success: function (response) {
                    $("#submit").prop("disabled",false);
                    if(response.status == true){
                        $("#name").removeClass("is-invalid").siblings("p").html('').removeClass("invalid-feedback");
                        $("#email").removeClass("is-invalid").siblings("p").html('').removeClass("invalid-feedback");
                        $("#subject").removeClass("is-invalid").siblings("p").html('').removeClass("invalid-feedback");

                        window.location.href = '{{ route("front.page",$page->slug) }}';
                    }
                    else{
                        var errors = response.errors;
                        //For name
                        if(errors.name){
                            $("#name").addClass("is-invalid").siblings("p").html(errors.name).addClass("invalid-feedback");
                        }else{
                            $("#name").removeClass("is-invalid").siblings("p").html('').removeClass("invalid-feedback");
                        }

                        // for email
                        if(errors.email){
                            $("#email").addClass("is-invalid").siblings("p").html(errors.email).addClass("invalid-feedback");
                        }else{
                            $("#email").removeClass("is-invalid").siblings("p").html('').removeClass("invalid-feedback");
                        }


                        // for email
                        if(errors.subject){
                            $("#subject").addClass("is-invalid").siblings("p").html(errors.subject).addClass("invalid-feedback");
                        }else{
                            $("#subject").removeClass("is-invalid").siblings("p").html('').removeClass("invalid-feedback");
                        }

                    }
                }
            });

        });

    </script>

@endsection
