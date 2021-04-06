@extends('layouts.app')

@section('content')
<div class="jumbotron text-center col">
    <h1 class="display-3">Terimakasih</h1>
    <p class="lead">Silahkan transfer ke bank dibawah dan kirim bukti transaksi anda ke Whatsapp.</p>
    <div class="col text-center">
        <a href="https://wa.me/+62895396903642" target="_blank" type="" class="btn btn-success w-30">
            <h5 class="font-italic pt-1"><i class="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp</h5>
        </a>
    </div>
    <div id="countdown"></div>
    <hr>
    <div class="row my-4">
        <div class="col mt-4">
            <h5 class="text-center">Bank detail</h5>
            <div>
                @if($paymentChannel[1] == 1)
                <div class="hide-bank" id="bank-detail-1">
                    <dl class="mt-2">
                        <dt>Bank</dt>
                        <dd>Mandiri</dd>
                    </dl>
                    <dl>
                        <dt>Nama Akun</dt>
                        <dd>PT IGN Global Network</dd>
                    </dl>
                    <dl>
                        <dt>Nomor Akun</dt>
                        <dd><span id="accnum">1570006314489</span> &nbsp;<button type="button" onclick="copy('#accnum')" class="btn btn-outline-secondary btn-sm" data-toggle="tooltip" title="Copy to Clipboard">
                                <i class="fa fa-clipboard" aria-hidden="true"></i></button>
                        </dd>
                    </dl>
                </div>
                @elseif($paymentChannel[1] == 2)
                <div class="hide-bank" id="bank-detail-2">
                    <dl class="mt-2">
                        <dt>Bank</dt>
                        <dd>CIMB Niaga</dd>
                    </dl>
                    <dl>
                        <dt>Nama Akun</dt>
                        <dd>PT IGN Global Network</dd>
                    </dl>
                    <dl>
                        <dt>Nomor Akun</dt>
                        <dd><span id="accnum2">860007012500</span> &nbsp;<button type="button" onclick="copy('#accnum2')" class="btn btn-outline-secondary btn-sm" data-toggle="tooltip" title="Copy to Clipboard">
                                <i class="fa fa-clipboard" aria-hidden="true"></i></button>
                        </dd>
                    </dl>
                </div>
                @elseif($paymentChannel[1] == 3)
                <div class="hide-bank" id="bank-detail-3">
                    <dl class="mt-2">
                        <dt>Bank</dt>
                        <dd>Bank Central Asia (BCA)</dd>
                    </dl>
                    <dl>
                        <dt>Nama Akun</dt>
                        <dd>PT IGN Global Network</dd>
                    </dl>
                    <dl>
                        <dt>Nomor Akun</dt>
                        <dd><span id="accnum3">7650875529</span> &nbsp;<button type="button" onclick="copy('#accnum3')" class="btn btn-outline-secondary btn-sm" data-toggle="tooltip" title="Copy to Clipboard">
                                <i class="fa fa-clipboard" aria-hidden="true"></i></button>
                        </dd>
                    </dl>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('register-scriptcode')
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
</script>
@endsection