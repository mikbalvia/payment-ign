@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 p-4 mt-3">
                            @if($product[0]['image'])
                            <img src="<?php echo asset($product[0]['image']) ?>" class="img-fluid" alt="Responsive image" />
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
                            <div class="row">
                            </div>
                            <form method="POST" action="{{route('store-customer',$productId)}}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-row">
                                    <div class="col">
                                        <input type="text" name="firstname" class="form-control" placeholder="First name*" value="{{ old('firstname') }}">
                                        @error('firstname')
                                        <span class="text-danger"><i>{{ $message }}</i></span>
                                        @enderror
                                    </div>
                                    <div class="col">
                                        <input type="text" name="lastname" class="form-control" placeholder="Last name">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col">
                                        <input type="email" class="form-control" name="email" placeholder="Email*" value="{{ old('email') }}">
                                        @error('email')
                                        <span class="text-danger"><i>{{ $message }}</i></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row mt-3">
                                    <div class="col-4">
                                        <select class="form-control prefix-number" name="prefixNumber">
                                        </select>
                                        @error('prefix-number')
                                        <span class="text-danger"><i>{{ $message }}</i></span>
                                        @enderror
                                    </div>
                                    <div class="col-8">
                                        <input type="text" class="form-control" name="phone" id="phne" maxlength="15" placeholder="Phone*" value="{{ old('phone') }}">
                                        @error('phone')
                                        <span class="text-danger"><i>{{ $message }}</i></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-check mt-3">
                                    <div class="col">
                                        <input class="form-check-input" type="checkbox" value="" id="tncCheck">
                                        <label class="form-check-label" for="defaultCheck1">
                                            <p>Check here to indicate that you have read and agree to the <a target="_blank" rel="noopener noreferrer" href="{{$product[0]['tnc_url']}}">Term and Conditions</a></p>
                                        </label>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-danger w-100" disabled id="btnSbmit">
                                            <h5 class="font-italic pt-1"><i class="fa fa-angle-double-right" aria-hidden="true"></i> Next Step</h5>
                                        </button>
                                    </div>
                                </div>
                                <div class=" row mt-3">
                                    <div class="col">
                                        <p class="text-justify">{{$product[0]['desc']}}</p>
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
    .select2-selection__rendered {
        line-height: 36px !important;
    }

    .select2-container .select2-selection--single {
        height: 36px !important;
    }

    .select2-selection__arrow {
        height: 36px !important;
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
        height: 43px;
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

    /* Active */
    .steps>*.active {
        cursor: auto;
        background-color: #e3342f2e;
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
        background-color: #e3342f;
        color: #FFFFFF;
        width: 50%;
    }

    .steps>*.now:after {
        border-left-color: #e3342f;
    }
</style>
@endsection

@section('register-scriptcode')
<script>
    $(document).ready(function() {
        $('.prefix-number').select2({
            placeholder: "Country*",
            width: "100%",
            ajax: {
                url: '/getPrefixNumber',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: "(+" + item.phonecode + ") " + item.nicename,
                                id: item.phonecode + "|" + item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        $("#phne").inputFilter(function(value) {
            return /^\d*$/.test(value); // Allow digits only, using a RegExp
        });

        $('#tncCheck').change(function() {
            if (this.checked) {
                $("#btnSbmit").prop('disabled', false);
            } else {
                $("#btnSbmit").prop('disabled', true);
            }
        });
    })
</script>
@endsection