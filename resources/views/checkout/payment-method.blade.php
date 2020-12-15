@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 p-4">
                            <img src="{{ asset('images/side-image.png') }}" class="img-fluid" alt="Responsive image">
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
                            <div class="row my-3">
                            </div>
                            <form method="POST" action="{{route('checkout-finish')}}" enctype="multipart/form-data">
                                @csrf
                                <div class=" row">
                                    <div class="col">
                                        <ul class="list-group">
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
                                                            <dd>7775877975</dd>
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
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col">
                                        <button type="submit" id="test" class="btn btn-danger w-100">
                                            <h5 class="font-italic"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Checkout</h5>
                                        </button>
                                    </div>
                                </div>
                                <div class=" row mt-3">
                                    <div class="col">
                                        <p class="text-justify">*Sell Like Crazy retails for $19.95 and is a #1 international best seller on Amazon,
                                            but today we've bought it for you! We just ask that you cover the shipping and handling costs
                                            in order for us to send it to you. Your information is secure and will not be shared.
                                        </p>
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
    .dropdown {
        display: none;
    }

    .steps>* {
        display: inline-block;
        position: relative;
        padding: 1em 2em 1em 3em;

        vertical-align: top;
        background-color: #FFFFFF;
        color: #888888;

        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        -ms-box-sizing: border-box;
        box-sizing: border-box;
    }

    .steps>*:after {
        position: absolute;
        z-index: 2;
        content: '';
        top: 0em;
        right: -1.45em;

        border-bottom: 1.5em solid transparent;
        border-left: 1.5em solid #FFFFFF;
        border-top: 1.5em solid transparent;

        width: 0em;
        height: 0em;
    }


    /*******************************
            Group
*******************************/
    .steps {
        /*font-size: 0em;*/
        letter-spacing: -0.31em;
        line-height: 1;

        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        -ms-box-sizing: border-box;
        box-sizing: border-box;

        -moz-border-radius: 0.3125rem;
        -webkit-border-radius: 0.3125rem;
        border-radius: 0.3125rem;
    }

    .steps>* {
        letter-spacing: normal;
        width: 150px;
        height: 43px; // i set the height from here

    }

    .steps>*:first-child {
        padding-left: 1.35em;
        -webkit-border-radius: 0.3125em 0em 0em 0.3125em;
        -moz-border-radius: 0.3125em 0em 0em 0.3125em;
        border-radius: 0.3125em 0em 0em 0.3125em;
    }

    .steps>*:last-child {
        -webkit-border-radius: 0em 0.3125em 0.3125em 0em;
        -moz-border-radius: 0em 0.3125em 0.3125em 0em;
        border-radius: 0em 0.3125em 0.3125em 0em;
        margin-right: 0;
    }

    .steps>*:only-child {
        -webkit-border-radius: 0.3125em;
        -moz-border-radius: 0.3125em;
        border-radius: 0.3125em;
    }

    .steps>*:last-child:after {
        display: none;
    }

    /*******************************
             States
*******************************/
    /* Hover */
    .steps>*:hover,
    .steps>*.hover {
        background-color: #F7F7F7;
        color: rgba(0, 0, 0, 0.8);
    }

    .steps>*.hover:after,
    .steps>*:hover:after {
        border-left-color: #F7F7F7;
    }

    /* Hover */
    .steps>*.down,
    .steps>*:active {
        background-color: #F0F0F0;
    }

    .steps>*.down:after,
    .steps>*:active:after {
        border-left-color: #F0F0F0;
    }

    /* Active */
    .steps>*.active {
        cursor: auto;
        background-color: #e3342f;
        width: 50%;
        color: #FFFFFF;
        padding: 14px 0 14px 35px;
    }

    .steps>*.active:after {
        border-left-color: #555555;
    }

    /* Now */
    .steps>*.now {
        cursor: auto;
        background-color: #e3342f2e;
        color: #FFFFFF;
        width: 50%;
    }

    .steps>*.now:after {
        border-left-color: #e3342f;

    }

    /* Done */
    .steps>*.done {
        cursor: auto;
        background-color: #46b98a;
        color: #FFFFFF;

    }

    .steps>*.done:after {
        border-left-color: #46b98a;
    }

    /* Disabled */
    .steps>*.disabled {
        cursor: auto;
        background-color: #FFFFFF;
        color: #CBCBCB;
    }

    .steps>*.disabled:after {
        border: none;
        background-color: #FFFFFF;
        top: 0.42em;
        right: -1em;

        width: 2.15em;
        height: 2.15em;

        -webkit-transform: rotate(-45deg);
        -moz-transform: rotate(-45deg);
        -o-transform: rotate(-45deg);
        -ms-transform: rotate(-45deg);
        transform: rotate(-45deg);

        -webkit-box-shadow: -1px -1px 0px 0px rgba(0, 0, 0, 0.1) inset;
        -moz-box-shadow: -1px -1px 0px 0px rgba(0, 0, 0, 0.1) inset;
        box-shadow: -1px -1px 0px 0px rgba(0, 0, 0, 0.1) inset;
    }
</style>
@endsection

@section('register-scriptcode')
<script>
    $(document).ready(function() {
        $(".perkdrop1").click(function() {
            $('.dp1').slideDown();
            $('.dp2').slideUp();
        });

        $(".perkdrop2").click(function() {
            $('.dp2').slideDown();
            $('.dp1').slideUp();
        });

        $("#mnth, #yr, #ccv, #ccnum").inputFilter(function(value) {
            return /^\d*$/.test(value); // Allow digits only, using a RegExp
        });
    })
</script>
@endsection