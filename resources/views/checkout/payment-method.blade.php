@extends('layouts.app')

@section('content')
<div class="container">
    @include('sweet::alert')
    @include('checkout.modal')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 p-4">
                            @if($product[0]->image)
                            <img src="<?php echo asset($product[0]->image) ?>" class="img-fluid" alt="Responsive image" />
                            @else
                            <img src="<?php echo asset('/images/no-image.jpg') ?>" class="img-fluid" alt="Responsive image" />
                            @endif
                        </div>
                        <div class="col-lg-6 pt-4">
                            <div class="row">
                                <div class="steps col py-3">
                                    <div class="now">
                                        <h6><i class="fa fa-address-book" aria-hidden="true"></i>&nbsp; Your Info</h6>
                                    </div>
                                    <div class="active">
                                        <h6><i class="fa fa-credit-card" aria-hidden="true"></i>&nbsp; Payment</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <div class="col">
                                    <h4 class="mb-4">Your Order</h4>
                                    <div class="row">
                                        <div class="col">
                                            <p class="text-left"><b>Product</b></p>
                                        </div>
                                        <div class="col">
                                            <p class="text-right"><b>Subtotal</b></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col">
                                            <p class="text-left">{{$product[0]->name}}</p>
                                        </div>
                                        <div class="col">
                                            <p class="text-right"><b>Rp. {{number_format($product[0]->price,0)}}</b></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col">
                                            <p class="text-left"><b>Total</b></p>
                                        </div>
                                        <div class="col">
                                            <p class="text-right"><b>Rp. {{number_format($product[0]->price,0)}}</b></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-3">
                            </div>
                            <form method="POST" action="{{route('checkout-process')}}" enctype="multipart/form-data" id="paymentForm">
                                @csrf
                                <input type="hidden" value="{{$product[0]->id}}" name="productId">
                                <input type="hidden" value="{{$product[0]->code}}" name="productCode" id="productCode">
                                <div class=" row">
                                    <div class="col">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <div class="row el">
                                                    <div class="col ml-3">
                                                        <input class="form-check-input perkdrop2" type="radio" name="payment-type" value="credit-card">
                                                        <label class="form-check-label" for="gridRadios1"><i class="fa fa-credit-card" aria-hidden="true"></i> Credit Card</label>
                                                    </div>
                                                </div>
                                                <div class="row mt-3 pl-3 dropdown dp2">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <input type="text" name="card-number" maxlength="19" placeholder="Your card number" id="ccnum" class="form-control" required>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text text-muted">
                                                                    <i class="fa fa-cc-visa mx-1"></i>
                                                                    <i class="fa fa-cc-amex mx-1"></i>
                                                                    <i class="fa fa-cc-mastercard mx-1"></i>
                                                                </span>
                                                            </div>
                                                            @error('card-number')
                                                            <span class="text-danger"><i>{{ $message }}</i></span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <div class="form-group">
                                                                <label><span class="hidden-xs">Expiration</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" placeholder="MM" maxlength="2" name="month" id="mnth" class="form-control" required>
                                                                    <input type="text" placeholder="YY" maxlength="4" name="year" id="yr" class="form-control" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group mb-4">
                                                                <label data-toggle="tooltip" title="Three-digits code on the back of your card">CVV
                                                                    <i class="fa fa-question-circle"></i>
                                                                </label>
                                                                <input type="text" required name="ccv" id="ccv" maxlength="3" class="form-control">
                                                                @error('ccv')
                                                                <span class="text-danger"><i>{{ $message }}</i></span>
                                                                @enderror
                                                                <input type="text" id="mToken" name="mTokenId" value="" hidden>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row el">
                                                    <div class="col ml-3">
                                                        <input class="form-check-input perkdrop1" type="radio" name="payment-type" value="direct-transfer">
                                                        <label class="form-check-label" for="gridRadios1"><i class="fa fa-university" aria-hidden="true"></i> Bank Transfer</label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col ml-3">
                                                        <img src="{{ asset('images/logobank.png') }}" class="img-fluid my-2">
                                                    </div>
                                                </div>
                                                <div class="dropdown dp1">
                                                    <div>
                                                        <h5 class="text-center">Bank account details</h5>
                                                        <dl class="mt-2">
                                                            <dt>Bank</dt>
                                                            <dd> THE WORLD BANK</dd>
                                                        </dl>
                                                        <dl>
                                                            <dt>Account number</dt>
                                                            <dd><span id="accnum">7775877975</span> &nbsp;<button type="button" onclick="copy('#accnum')" class="btn btn-outline-secondary btn-sm" data-toggle="tooltip" title="Copy to Clipboard">
                                                                    <i class="fa fa-clipboard" aria-hidden="true"></i></button>
                                                            </dd>
                                                        </dl>
                                                        <dl>
                                                            <dt>IBAN</dt>
                                                            <dd>CZ7775877975656</dd>
                                                        </dl>
                                                        <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col">
                                        <button type="button" id="proceedCheckout" class="btn btn-danger w-100">
                                            <h5 class="font-italic"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Checkout</h5>
                                        </button>
                                    </div>
                                </div>
                                <div class=" row mt-3">
                                    <div class="col">
                                        <p class="text-justify">{{$product[0]->desc}}</p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('register-style')
<style>
    hr {
        margin-top: -10px;
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .dropdown {
        display: none;
    }
</style>
<link href="{{ asset('css/custom.css') }}" rel="stylesheet">
@endsection

@section('register-scriptcode')
<script id="midtrans-script" type="text/javascript" src="https://api.midtrans.com/v2/assets/js/midtrans-new-3ds.min.js" data-environment="sandbox" data-client-key="{{env('MIDTRANS_CLIENT_KEY')}}"></script>
<script>
    /**
     * number validation for input text
     */
    function copy(selector) {
        var $temp = $("<div>");
        $("body").append($temp);
        $temp.attr("contenteditable", true)
            .html($(selector).html()).select()
            .on("focus", function() {
                document.execCommand('selectAll', false, null);
            })
            .focus();
        document.execCommand("copy");
        $temp.remove();
    }

    $(document).ready(function() {

        /**
         * on change payment method direct transfer
         */
        $(".perkdrop1").click(function() {
            $('.dp1').slideDown();
            $('.dp2').slideUp();
            $("#mnth, #yr, #ccv, #ccnum").prop('disabled', true);
        });

        /**
         * on change payment method credit card
         */
        $(".perkdrop2").click(function() {
            $('.dp2').slideDown();
            $('.dp1').slideUp();
            $("#mnth, #yr, #ccv, #ccnum").prop('disabled', false);
        });

        $("#mnth, #yr, #ccv, #ccnum").inputFilter(function(value) {
            return /^\d*$/.test(value); // Allow digits only, using a RegExp
        });

        // proceed checkout form
        $('#proceedCheckout').click(function() {
            $('#cover-spin').show();

            //payment type direct transfer
            if ($("input[name=payment-type]:checked").val() === 'direct-transfer') {
                getDirectTransferResponse();
                return true;
            }

            // card data from customer input, for example
            var cardData = {
                "card_number": $("#ccnum").val(),
                "card_exp_month": $("#mnth").val(),
                "card_exp_year": $("#yr").val(),
                "card_cvv": $("#ccv").val(),
            };

            // callback functions
            var options = {
                onSuccess: function(response) {
                    // Success to get card token_id, implement as you wish here
                    var token_id = response.token_id;

                    $('#mToken').val(token_id);
                    getPaymentResponse();
                },
                onFailure: function(response) {
                    swal({
                        text: "Fail to get card token_id, response: " + response,
                        title: "Error",
                        icon: "error",
                        buttons: "OK"
                    });

                    $('#cover-spin').hide();
                }
            };

            // trigger `getCardToken` function
            MidtransNew3ds.getCardToken(cardData, options);
        });
    })

    /**
     * get midtrans payment response
     */
    function getPaymentResponse() {
        var data = $('#paymentForm').serializeArray();
        data.push({
            name: '_token',
            value: '<?php echo csrf_token() ?>'
        });

        $.ajax({
            type: 'POST',
            url: '{{route("checkout-process")}}',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.status_code === '201') {
                    showAuthentication(data.redirect_url);
                } else if (data.status_code === '200') {
                    swal({
                        text: data.status_message,
                        title: "Payment Success",
                        icon: "success",
                        buttons: "OK"
                    }).then((value) => {
                        window.location = "{{url('/checkout/finish/1')}}";
                    });
                } else if (data.status_code === '300') {
                    swal({
                        text: data.status_message,
                        title: "Error Data",
                        icon: "error",
                        buttons: "OK"
                    }).then((value) => {
                        window.location = "{{url('/checkout/step1')}}/" + $("#productCode").val();
                    });
                } else {
                    swal({
                        text: data.status_message,
                        title: "Error at processing request",
                        icon: "error",
                        buttons: "OK"
                    }).then((value) => {
                        window.location = "{{url('/checkout/step1')}}/" + $("#productCode").val();
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#cover-spin').hide();

                swal({
                    text: xhr.responseText,
                    title: "Error at processing request",
                    icon: "error",
                    buttons: "OK"
                })
            }
        });
    }

    // 3ds Aunthentication
    function showAuthentication(urlRedirect) {
        var redirect_url = urlRedirect;

        // callback functions
        var options = {
            performAuthentication: function(redirect_url) {
                // Implement how you will open iframe to display 3ds authentication redirect_url to customer
                $('#cover-spin').hide();
                $('#authView').attr('src', redirect_url);
                $('#pinModal').modal('show');
            },
            onSuccess: function(response) {
                // 3ds authentication success, implement payment success scenario
                $('#cover-spin').hide();
                $('#pinModal').modal('hide');
                swal({
                    text: response.status_message,
                    title: "Payment Success",
                    icon: "success",
                    buttons: "OK"
                }).then((value) => {
                    window.location = "{{url('/checkout/finish/1')}}";
                });
            },
            onFailure: function(response) {
                // 3ds authentication failure, implement payment failure scenario
                $('#cover-spin').hide();
                $('#pinModal').modal('hide');
                swal({
                    text: response.status_message,
                    title: "Error at processing request",
                    icon: "error",
                    buttons: "OK"
                });
            },
            onPending: function(response) {
                // transaction is pending, transaction result will be notified later via POST notification, implement as you wish here
                $('#cover-spin').hide();
                $('#pinModal').modal('hide');
                window.location.replace('{{url("/checkout/finish/1")}}');
            }
        };

        // trigger `authenticate` function
        MidtransNew3ds.authenticate(redirect_url, options);
    }

    /**
     * Process direct transfer payment
     */
    function getDirectTransferResponse() {
        var data = $('#paymentForm').serializeArray();
        data.push({
            name: '_token',
            value: '<?php echo csrf_token() ?>'
        });

        $.ajax({
            type: 'POST',
            url: '{{route("checkout-process")}}',
            data: data,
            dataType: 'json',
            success: function(data) {
                $('#cover-spin').hide();
                if (data.status_code === 300) {
                    swal({
                        text: data.status_message,
                        title: "Error Data",
                        icon: "error",
                        buttons: "OK"
                    }).then((value) => {
                        window.location = "{{url('/checkout/step1')}}/" + $("#productCode").val();
                    });
                } else {
                    swal({
                        text: data.status_message,
                        title: "Request Success",
                        icon: "success",
                        buttons: "OK"
                    }).then((value) => {
                        window.location = "{{url('/checkout/finish/2')}}";
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#cover-spin').hide();

                swal({
                    text: xhr.responseText,
                    title: "Error at processing request",
                    icon: "error",
                    buttons: "OK"
                })
            }
        });
    }
</script>
@endsection