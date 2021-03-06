@extends('layouts.app')

@section('content')
<div class="container">
    @include('sweet::alert')
    @include('checkout.modal')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        {{-- <div class="col-lg-6 p-4">
                            @if($product[0]->image)
                            <img src="<?php echo asset($product[0]->image) ?>" class="img-fluid" alt="Responsive image" />
                            @else
                            <img src="<?php echo asset('/images/no-image.jpg') ?>" class="img-fluid" alt="Responsive image" />
                            @endif
                            <div class="d-none d-sm-block">
                                <p>Informasi Pembayaran:</p>
                                <ol>
                                    <li>Silahkan Isi data Anda dengan lengkap dan benar.</li>
                                    <li>Setelah melakukan pembayaran silahkan kirimkan bukti pembayaran Anda ke Whatsapp +62895396903642 dengan format Nama_Bukti Pembayaran</li>
                                    <li>Pembayaran akan kami cek paling lama 2x24 jam (Hari kerja)</li>
                                    <li>Informasi Resi akan dikirimkan setelah pembayaran terkonfirmasi</li>
                                    <li>Butuh bantuan? Klik tombol WA dibawah ini dan tim kami akan membantu Anda</li>
                                    
                                </ol>
                                <div class="col text-center">
                                    <a href="https://wa.me/+62895396903642" target="_blank" type="" class="btn btn-success w-50">
                                        <h5 class="font-italic pt-1"><i class="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp</h5>
                                    </a>
                                </div>

                            </div>
                        </div> --}}
                        <div class="col-lg-12 pt-4">
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
                                    <h4 class="mb-4">Order Anda</h4>
                                    <div class="row">
                                        <div class="col">
                                            <p class="text-left"><b>Produk</b></p>
                                        </div>
                                        <div class="col">
                                            <p class="text-right"><b>Subtotal</b></p>
                                        </div>
                                    </div>
                                    
                                    <hr>

                                    @if ($product[0]->price)
                                    <div class="row">
                                        <div class="col">
                                            <p class="text-left">{{$product[0]->name}}</p>
                                        </div>
                                        <div class="col">
                                            <p class="text-right"><b> USD {{number_format($product[0]->price / env('USD_RATE'),2)}} / IDR {{number_format($product[0]->price,0,',','.')}}</b></p>
                                        </div>
                                    </div>
                                        
                                    @endif
                                    
                                    @foreach ($product[0]->additionalProduct as $item)
                                    @php
                                        $qty=1;
                                        if (isset($cart)) {
                                            foreach($cart as $value){
                                                if($value->id==$item->id){
                                                $qty=$value->qty; 
                                                break;
                                                }else{
                                                    $qty=1;
                                                }
                                            }
                                        }

                                    @endphp
                                    <div class="row table-additional" id="table-additional-{{$item->id}}">
                                        <div class="col">
                                            <p class="text-left">{{$item->name}} x {{$qty}}</p>
                                        </div>
                                        <div class="col">
                                            <b><p class="text-right subtotal-add" data-id="{{$item->id}}" data-price="{{$item->price}}">USD {{number_format($item->price / env('USD_RATE'),2)}} / IDR {{number_format($item->price*$qty,0,',','.')}}</p></b>
                                        </div>
                                    </div>
                                    @endforeach
                                    
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col">
                                            <p class="text-left"><b>Total</b></p>
                                        </div>
                                        <div class="col">
                                            <p class="text-right font-weight-bold" id="total">USD {{number_format($product[0]->price / env('USD_RATE'),0)}} / IDR {{number_format($product[0]->price,0,',','.')}}</p>
                                        </div>
                                    </div>
                                    <div class=" row mt-3">
                                        <div class="col">
                                            <h5>Description :</h5>
                                            <p class="text-justify">{{$product[0]->desc}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form id="paymentForm" enctype="multipart/form-data">
                                @csrf

                            @foreach ($product[0]->additionalProduct as $item)

                            <?php
                                if (isset($cart)) {
                                    foreach($cart as $value){
                                        if($value->id==$item->id){

                            ?>
                            
                            <div class="row p-3">
                                <div class="row card p-2" style="flex-direction: row">
                                    <div class="col-md-12">
                                        <img src="<?php echo asset($item->image) ?>" alt="" width="100%">
                                    </div>
                                    <div class="col-md-12">
                                        <p class="font-weight-bold">{{$item->name}}</p>
                                        <p class="font-weight-bold">USD {{number_format($item->price / env('USD_RATE'),2)}} / IDR {{number_format($item->price,0,',','.')}}</p>
                                        <p>{{$item->desc}}</p>

                                        <div class="form-group" id="qty-additional-{{$item->id}}" style="display: none;opacity: 0;height:0px">
                                            <div class="d-flex">
                                                <label style="margin-right:10px"><span>Qty</span></label>
                                                <div class="input-group">
                                                    <input type="number" class="inp-qty" name="qty-f" min="1" value="{{$value->qty}}" id="inp-qty-add-{{$item->id}}" data-id="{{$item->id}}" data-price="{{$item->price}}" required style="width:50px">
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <button id="add-rem-item-{{$item->id}}" type="button" class="btn btn-info w-90 add-rem-item" data-process='add' data-add-rem-item-id="{{$item->id}}" data-add-rem-item-price-usd="{{number_format($item->price / env('USD_RATE'),0)}}" data-add-rem-item-price-idr="{{number_format($item->price,0,',','.')}}" style="opacity: 0;height:0px">
                                            <p style="color: white;margin:0"><i class="fa fa-plus" aria-hidden="true"></i> Add</p>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <?php
                                        }
                                    }
                                        
                                }
                            ?>
                                
                            @endforeach
                            
                                <input type="hidden" value="{{$product[0]->id}}" name="productId">
                                <input type="hidden" value="{{$product[0]->code}}" name="productCode" id="productCode">
                                <div class=" row">
                                    <div class="col">
                                        <ul class="list-group">
                                            {{-- <li class="list-group-item">
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
                                                                    <input type="text" placeholder="YYYY" maxlength="4" name="year" id="yr" class="form-control" required>
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
                                            </li> --}}
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
                                                    <div class="row" id="select-bank">
                                                        <div class="col ml-3">
                                                            <div class="form-group">
                                                                <select class="form-control" id="bankToSelect" name="bankToSelect">
                                                                    <option value="1">Mandiri</option>
                                                                    <option value="2">CIMB Niaga</option>
                                                                    <option value="3">Bank Central Asia (BCA)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h5 class="text-center">Bank account details</h5>
                                                        <div class="hide-bank" id="bank-detail-1">
                                                            <dl class="mt-2">
                                                                <dt>Bank</dt>
                                                                <dd>Mandiri</dd>
                                                            </dl>
                                                            <dl>
                                                                <dt>Account Name</dt>
                                                                <dd>PT IGN Global Network</dd>
                                                            </dl>
                                                            <dl>
                                                                <dt>Account number</dt>
                                                                <dd><span id="accnum">1570006314489</span> &nbsp;<button type="button" onclick="copy('#accnum')" class="btn btn-outline-secondary btn-sm" data-toggle="tooltip" title="Copy to Clipboard">
                                                                        <i class="fa fa-clipboard" aria-hidden="true"></i></button>
                                                                </dd>
                                                            </dl>
                                                        </div>
                                                        <div class="hide-bank" id="bank-detail-2">
                                                            <dl class="mt-2">
                                                                <dt>Bank</dt>
                                                                <dd>CIMB Niaga</dd>
                                                            </dl>
                                                            <dl>
                                                                <dt>Account Name</dt>
                                                                <dd>PT IGN Global Network</dd>
                                                            </dl>
                                                            <dl>
                                                                <dt>Account number</dt>
                                                                <dd><span id="accnum2">860007012500</span> &nbsp;<button type="button" onclick="copy('#accnum2')" class="btn btn-outline-secondary btn-sm" data-toggle="tooltip" title="Copy to Clipboard">
                                                                        <i class="fa fa-clipboard" aria-hidden="true"></i></button>
                                                                </dd>
                                                            </dl>
                                                        </div>
                                                        <div class="hide-bank" id="bank-detail-3">
                                                            <dl class="mt-2">
                                                                <dt>Bank</dt>
                                                                <dd>Bank Central Asia (BCA)</dd>
                                                            </dl>
                                                            <dl>
                                                                <dt>Account Name</dt>
                                                                <dd>PT IGN Global Network</dd>
                                                            </dl>
                                                            <dl>
                                                                <dt>Account number</dt>
                                                                <dd><span id="accnum3">7650875529</span> &nbsp;<button type="button" onclick="copy('#accnum3')" class="btn btn-outline-secondary btn-sm" data-toggle="tooltip" title="Copy to Clipboard">
                                                                        <i class="fa fa-clipboard" aria-hidden="true"></i></button>
                                                                </dd>
                                                            </dl>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row el">
                                                    <div class="col md-3 ml-3">
                                                        <input class="form-check-input perkdrop3" type="radio" name="payment-type" value="wallet">
                                                        <label class="form-check-label" for="gridRadios1"><i class="fa fa-google-wallet" aria-hidden="true"></i> E-Wallet</label>
                                                        <img src="https://pay.internationalglobalnetwork.com/wp-content/plugins/nicepay_ewalletv2/e_wallet.png" class="img-fluid my-2" style="margin: 0px !important;">
                                                    </div>
                                                </div>
                                                <div class="dropdown dp3 mt-2">
                                                    <div class="row" id="select-wallet">
                                                        <div class="col ml-3">
                                                            <div class="form-group">
                                                                <select class="form-control" id="walletToSelect" name="walletToSelect">
                                                                    <option value="OVOE">OVO</option>
                                                                    <option value="DANA">DANA</option>
                                                                    <option value="LINK">LINK</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label><span>Wallet Number</span></label>
                                                                <div class="input-group">
                                                                    
                                                                    <input type="text" name="wallet-number" maxlength="19" placeholder="082-1234-xxxx" id="walletnum" class="form-control" required>
                                                                    
                                                                    @error('card-number')
                                                                    <span class="text-danger"><i>{{ $message }}</i></span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </li>
                                            {{-- <li class="list-group-item">
                                                <div class="row el">
                                                    <div class="col md-3 ml-3">
                                                        <input class="form-check-input perkdrop4" type="radio" name="payment-type" value="qris">
                                                        <label class="form-check-label" for="gridRadios1"><i class="fa fa-qrcode" aria-hidden="true"></i> QRIS</label>
                                                        <img src="https://pay.internationalglobalnetwork.com/wp-content/plugins/nicepay_qrisv2/qrislogo1.png" class="img-fluid my-2" style="margin: 0px !important;">
                                                    </div>
                                                </div>
                                                
                                            </li> --}}
                                        </ul>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col">
                                        <button type="submit" id="proceedCheckout" class="btn btn-danger w-100">
                                            <h5 class="font-italic"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Checkout</h5>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col">
                                        <div class="">
                                            <p>Informasi Pembayaran:</p>
                                            <ol>
                                                <li>Silahkan Isi data Anda dengan lengkap dan benar.</li>
                                                <li>Setelah melakukan pembayaran silahkan kirimkan bukti pembayaran Anda ke Whatsapp +62895396903642 dengan format Nama_Bukti Pembayaran</li>
                                                <li>Pembayaran akan kami cek paling lama 2x24 jam (Hari kerja)</li>
                                                <li>Informasi Resi akan dikirimkan setelah pembayaran terkonfirmasi</li>
                                                <li>Butuh bantuan? Klik tombol WA dibawah ini dan tim kami akan membantu Anda</li>
                                                
                                            </ol>
                                            <div class="col text-center">
                                                <a href="https://wa.me/+62895396903642" target="_blank" type="" class="btn btn-success w-50">
                                                    <h5 class="font-italic pt-1"><i class="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp</h5>
                                                </a>
                                            </div>
            
                                        </div>
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

    .hide-bank {
        display: none;
    }
</style>
<link href="{{ asset('css/custom.css') }}" rel="stylesheet">
@endsection

@section('register-scriptcode')
<script id="midtrans-script" type="text/javascript" src="https://api.midtrans.com/v2/assets/js/midtrans-new-3ds.min.js" data-environment="production" data-client-key="{{env('MIDTRANS_CLIENT_KEY')}}"></script>
<script>

    function showBankDetail(val) {
        if (val === '1') {
            $("#bank-detail-1").show();
            $("#bank-detail-2").hide();
            $("#bank-detail-3").hide();
        } else if (val === '2') {
            $("#bank-detail-1").hide();
            $("#bank-detail-2").show();
            $("#bank-detail-3").hide();
        } else {
            $("#bank-detail-1").hide();
            $("#bank-detail-2").hide();
            $("#bank-detail-3").show();
        }
    }

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

    /**
     * get total payment
     */
    function total() {
        var usdRate= {{env('USD_RATE')}};
        var totalPrice = <?= $product[0]->price; ?>;
        $('.inp-qty').each(function() {
            if ($(this).attr('name')=='qty[]') {
                totalPrice = parseInt(totalPrice) + (parseInt($(this).attr('data-price'))* parseInt($(this).val())) ;
                var price = $(this).attr('data-price');
                var qty = $(this).val();
                var usd = parseInt(price)*parseInt(qty)/usdRate;
                var idr = parseInt(price)*parseInt(qty);
                price = 'USD '+usd.toFixed(2)+' / IDR '+idr.toFixed(2);
                $('.subtotal-add[data-id="'+$(this).attr('data-id')+'"]').empty().append(price);   
            }
            
        });

        var usd = parseInt(totalPrice)/usdRate;
        var idr = parseInt(totalPrice);
        var pTotal = 'USD '+usd.toFixed(2)+' / IDR '+idr.toFixed(2);
        $('#total').empty().append(pTotal);
    }

    $(document).ready(function() {

        /**
        * on click button add remove
        */
        $('.add-rem-item').on('click',function(){
            if ($(this).attr('data-process')=='add') {
                $('#qty-additional-'+$(this).attr('data-add-rem-item-id')).show();
                $('#inp-qty-add-'+$(this).attr('data-add-rem-item-id')).attr('name','qty[]');
                $(this).removeClass('btn-info').addClass('btn-danger');
                $(this).find('p').empty().append('<i class="fa fa-minus" aria-hidden="true"></i> Remove');
                $(this).attr('data-process','remove');
                $('#table-additional-'+$(this).attr('data-add-rem-item-id')).show();
                $('form').prepend('<input type="hidden" value="'+$(this).attr('data-add-rem-item-id')+'" name="addItem[]" id="input-add-item-'+$(this).attr('data-add-rem-item-id')+'">');
            }
            else{
                $('#qty-additional-'+$(this).attr('data-add-rem-item-id')).hide();
                $('#inp-qty-add-'+$(this).attr('data-add-rem-item-id')).attr('name','qty-f');
                $(this).removeClass('btn-danger').addClass('btn-info');
                $(this).find('p').empty().append('<i class="fa fa-plus" aria-hidden="true"></i> Add');
                $(this).attr('data-process','add');
                $('#table-additional-'+$(this).attr('data-add-rem-item-id')).hide();
                $('#input-add-item-'+$(this).attr('data-add-rem-item-id')).remove();
            }
            total();
            
        })
        
        /**
        * check session cart
        */
        setTimeout(function(){ 
            @php
            if (isset($cart)) {
                foreach ($cart as $key => $value) {
                    echo "$('#add-rem-item-".$value->id."').click();";
                }
                
            }
                
            @endphp
        }, 1000);

        /**
        * change quantity product
        */
        $('.inp-qty').on('change',function() {
            total()    
        })


        /**
         * hide table price additional product
         */
        $('.table-additional').hide();
        /**
         * on select bank
         */
        showBankDetail("1");
        $('#bankToSelect').on('change', function() {
            showBankDetail(this.value);
        });

        /**
         * on change payment method direct transfer
         */
        $(".perkdrop1").click(function() {
            $('.dp1').slideDown();
            $('.dp2').slideUp();
            $('.dp3').slideUp();
            $("#mnth, #yr, #ccv, #ccnum").prop('disabled', true);
            $("#bankToSelect").prop('disabled', false);
            $("#walletToSelect, #walletnum").prop('disabled', true);
        });

        /**
         * on change payment method credit card
         */
        $(".perkdrop2").click(function() {
            $('.dp2').slideDown();
            $('.dp1').slideUp();
            $('.dp3').slideUp();
            $("#mnth, #yr, #ccv, #ccnum").prop('disabled', false);
            $("#bankToSelect").prop('disabled', true);
            $("#walletToSelect, #walletnum").prop('disabled', true);
        });

        $(".perkdrop3").click(function() {
            $('.dp1').slideUp();
            $('.dp2').slideUp();
            $('.dp3').slideDown();
            $("#mnth, #yr, #ccv, #ccnum").prop('disabled', true);
            $("#bankToSelect").prop('disabled', true);
            $("#walletToSelect, #walletnum").prop('disabled', false);
        });

        $(".perkdrop4").click(function() {
            $('.dp1').slideUp();
            $('.dp2').slideUp();
            $('.dp3').slideUp();
            $("#mnth, #yr, #ccv, #ccnum").prop('disabled', true);
            $("#bankToSelect").prop('disabled', true);
            $("#walletToSelect, #walletnum").prop('disabled', true);
        });

        $("#mnth, #yr, #ccv, #ccnum").inputFilter(function(value) {
            return /^\d*$/.test(value); // Allow digits only, using a RegExp
        });

        // proceed checkout form
        $('#proceedCheckout').click(function(e) {
            $('#cover-spin').show();
            e.preventDefault();
            //payment type direct transfer
            if ($("input[name=payment-type]:checked").val() === 'direct-transfer') {
                getDirectTransferResponse();
                return true;
            }
            //payment type wallet and qris
            else if ($("input[name=payment-type]:checked").val() === 'wallet' || $("input[name=payment-type]:checked").val() === 'qris') {
                getNicepayResponse();
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
                        title: "Permintaan Berhasil",
                        icon: "success",
                        buttons: "OK"
                    }).then((value) => {
                        window.location = "{{url('/checkout/finish')}}/" + $("#productCode").val() + "/1";
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
                    title: "Permintaan Berhasil",
                    icon: "success",
                    buttons: "OK"
                }).then((value) => {
                    window.location = "{{url('/checkout/finish')}}/" + $("#productCode").val() + "/1";
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
                window.location.replace('{{url("/checkout/finish")}}/' + $("#productCode").val() + '/1');
            }
        };
        // trigger `authenticate` function
        MidtransNew3ds.authenticate(redirect_url, options);
    }
    /**
     * get nicepay payment response
     */
    function getNicepayResponse() {
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
                }else{
                    window.location = data.link;
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
                        title: "Permintaan Berhasil",
                        icon: "success",
                        buttons: "OK"
                    }).then((value) => {
                        window.location = "{{url('/checkout/finish')}}/" + $("#productCode").val() + "/2-" + $('#bankToSelect').find(":selected").val();
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