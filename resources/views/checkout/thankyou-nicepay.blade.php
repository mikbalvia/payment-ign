@extends('layouts.app')

@section('content')
<div class="jumbotron text-center col">
    <h1 class="display-3">Thank You!</h1>
    <p class="lead">Your payment is {{$result->resultMsg?$result->resultMsg:'being processed'}}</p>
    <div id="countdown"></div>
    <hr>
    <p>
        Having trouble? <a href="">Contact us</a>
    </p>
</div>
@endsection