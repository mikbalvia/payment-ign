<div class="col-md-3 my-2">
    <div class="card">
        <div class="card-header">{{ __('Menu') }}</div>
        <div class="card-body">
            <ul class="list-group">
                <a href="{{ route('product.index') }}">
                    <li class="list-group-item" id="product"><i class="fa fa-book" aria-hidden="true"></i> &nbsp; Product</li>
                </a>
                <a href="{{ route('transaction') }}">
                    <li class="list-group-item" id="transaction"><i class="fa fa-money" aria-hidden="true"></i> &nbsp; Transaction</li>
                </a>
            </ul>
        </div>
    </div>
</div>