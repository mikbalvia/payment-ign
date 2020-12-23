@extends('layouts.app')

@section('content')

@include('layouts.sidemenu')

<div class="col-md-9 my-2">
    <div class="card">
        <div class="card-header">{{ __('Transaction') }}</div>
        @include('layouts.flash-message')
        <div class="card-body">
            <table class="table table-bordered table-responsive">
                <colgroup>
                    <col span="1" style="width: 5%;">
                    <col span="1" style="width: 25%;">
                    <col span="1" style="width: 20%;">
                    <col span="1" style="width: 15%;">
                    <col span="1" style="width: 13%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 12%;">
                    <col span="1" style="width: 5%;">
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Order Id</th>
                        <th scope="col">Name</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Payment Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $key => $payment)
                    <tr>
                        <td>{{$payments->firstItem() + $key }}</td>
                        <td>{{$payment->order_id}}</td>
                        <td>{{$payment->firstname}} {{$payment->lastname}}</td>
                        <td>Rp. {{number_format($payment->amount,0)}}</td>
                        <td>{{$payment->payment_type}}</td>
                        <td>
                            @if($payment->transaction_status == 'capture' || $payment->transaction_status == 'success')
                            <span class="badge badge-success">Success</span>
                            @elseif($payment->transaction_status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                            @elseif($payment->transaction_status == 'deny' || $payment->transaction_status == 'failed')
                            <span class="badge badge-danger">Failed</span>
                            @else
                            <span class="badge badge-primary">{{$payment->transaction_status}}</span>
                            @endif
                        </td>
                        <td>{{date('d-m-Y', strtotime($payment->created_at))}}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a class="btn btn-primary btn-sm mr-1" href="{{ url('transaction/'.$payment->id.'/edit') }}">Show</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection

@section('register-scriptcode')
<script>

</script>
@endsection