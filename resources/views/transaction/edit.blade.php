@extends('layouts.app')

@section('content')

@include('layouts.sidemenu')

<div class="col-md-9 my-2">
    <div class="card">
        <div class="card-header">{{ __('Update Transaction') }}</div>
        <div class="card-body">
            <form method="POST" action="{{route('transaction.update',$payment->id)}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="produdctName">Order Id</label>
                    <input type="text" class="form-control" id="orderId" name="orderId" value="{{$payment->order_id}}" disabled>
                </div>
                <div class="form-group">
                    <label for="produdctName">Payment Type</label>
                    <input type="text" class="form-control" id="paymentType" name="paymentType" value="{{$payment->payment_type}}" disabled>
                </div>
                <div class="form-group">
                    <label for="produdctName">Amount</label>
                    <input type="text" class="form-control" id="amount" name="amount" value="{{$payment->amount}}" disabled>
                </div>
                <div class="form-group">
                    <label for="produdctName">Created At</label>
                    <input type="text" class="form-control" id="createdAt" name="createdAt" value="{{date('d-m-Y', strtotime($payment->created_at))}}" disabled>
                </div>
                @if($payment->payment_type === 'Direct Transfer')
                <div class="form-group">
                    <label for="paymentStatus">Status</label>
                    <select name="paymentStatus" class="form-control">
                        <option value="success" {{ $payment->transaction_status == 'success' ? 'selected="selected"' : '' }}>Success</option>
                        <option value="pending" {{ $payment->transaction_status == 'pending' ? 'selected="selected"' : ''}}>Pending</option>
                        <option value="failed" {{ $payment->transaction_status == 'failed' ? 'selected="selected"' : ''}}>Failed</option>
                    </select>
                </div>
                @else
                <div class="form-group">
                    <label for="produdctName">Status</label>
                    <input type="text" class="form-control" id="transactionStatus" name="transactionStatus" value="{{$payment->transaction_status}}" disabled>
                </div>
                @endif
                <div class="form-group">
                    @if($payment->payment_type === 'Direct Transfer')
                    <button type="submit" class="btn btn-success">Update</button>
                    @endif
                    <a href="{{ route('transaction') }}" class="btn btn-outline-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection