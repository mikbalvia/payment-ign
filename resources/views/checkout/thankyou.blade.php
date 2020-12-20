@extends('layouts.app')

@section('content')
<div class="jumbotron text-center col">
    <h1 class="display-3">Thank You!</h1>
    <p class="lead">Your request is being processed</p>
    <div id="countdown"></div>
    <hr>
    <p>
        Having trouble? <a href="">Contact us</a>
    </p>
</div>
@endsection

@section('register-scriptcode')
<script>
    // var timeleft = 10;
    // var downloadTimer = setInterval(function() {
    //     if (timeleft <= 0) {
    //         clearInterval(downloadTimer);
    //         window.location.replace('{{url("/")}}');
    //     } else {
    //         document.getElementById("countdown").innerHTML = timeleft + " seconds remaining";
    //     }
    //     timeleft -= 1;
    // }, 1000);
</script>
@endsection