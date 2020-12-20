@extends('layouts.app')

@section('content')
<div class="jumbotron text-center col">
    <h1 class="display-3">Thank You!</h1>
    <p class="lead">Please complete your payment.</p>
    <div id="countdown"></div>
    <hr>
    <div class="row my-4">
        <div class="col mt-4">
            <h5 class="text-center">Bank account details</h5>
            <div>
                <dl class="mt-3">
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