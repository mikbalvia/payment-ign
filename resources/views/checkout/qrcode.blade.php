@extends('layouts.app')

@section('content')
<div class="jumbotron text-center col">
    <h1 class="display-6">Silahkan Scan QRIS berikut menggunakan Applikasi pembayaran yang mendukung QRIS.</h1>
    <div>
        <p><img src="https://pay.internationalglobalnetwork.com/wp-content/plugins/nicepay_qrisv2/qrislogo1.png" alt=""></p>
        <h2>QR hanya valid dalam</h2>
        <h2 id="countdown"></h2>
        <p><img src="{{$_GET['qrUrl']}}" alt=""></p>
        
        <button class="btn btn-danger" onclick="window . location = '{{$_GET['qrUrl']}}';" class="btn"> <i class="fa fa-download"></i> Download QR</button>
        <h2 style="margin-top:50px ">Tekan tombol Continue jika sudah melakukan pembayaran.</h2>
        <button class="btn btn-success" onclick="window . location = '{{env('NICEPAY_CALLBACK_URL').'?amt='.$_GET['amt'].'&referenceNo='.$_GET['referenceNo'].'&tXid='.$_GET['tXid']}}';" class="btn"> <i class="fa fa-check"></i> Continue</button>
        
    </div>
    
    
    <hr>
    <p>
        Having trouble? <a href="">Contact us</a>
    </p>
</div>
<script>
    var startDate = String({{$_GET['paymentExpDt']}});
    var startTime = String({{$_GET['paymentExpTm']}});

    var year = startDate.substring(0, 4);
    var month = startDate.substring(4, 6);
    var day = startDate.substring(6, 8);
    startDate = year + '-' + month + '-' + day;
    var hh = startTime.substring(0, 2);
    var ii = startTime.substring(2, 4);
    var ss = startTime.substring(4, 6);
    startTime = hh  + "-" + ii + "-" + ss;
    startTime = startTime.replaceAll("-", /:/);
    startTime = startTime.replaceAll("/", "");
    
    console.log(startTime);
    // Set the date we're counting down to
    var countDownDate = new Date(startDate+" "+startTime).getTime();

    // Update the count down every 1 second
    var x = setInterval(function () {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Output the result in an element with id="countdown"
        document.getElementById("countdown").innerHTML = days + "d " + hours + "h " +
            minutes + "m " + seconds + "s ";

        // If the count down is over, write some text 
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("countdown").innerHTML = "EXPIRED";
        }
    }, 1000);
</script>
@endsection


