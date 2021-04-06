@extends('layouts.app')

@section('content')
<div class="jumbotron text-center col">
    <h1 class="">Terimakasih</h1>
    <p class="lead">Status pembayaran anda {{$result->resultMsg == 'paid'? 'sudah bayar':'belum bayar'}}</p>
    <div id="countdown"></div>
    <hr>
    <p>
        Silahkan kirim bukti transaksi anda ke Whatsapp
    </p>
    <div class="col text-center">
        <a href="https://wa.me/+62895396903642" target="_blank" type="" class="btn btn-success w-30">
            <h5 class="font-italic pt-1"><i class="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp</h5>
        </a>
    </div>

</div>
@endsection