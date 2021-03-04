@extends('layouts.app')

@section('content')

@include('layouts.sidemenu')

<div class="col-md-9 my-2">
    <div class="card">
        <div class="card-header">{{ __('Additional Product') }}</div>
        @include('layouts.flash-message')
        <div class="card-body">
            <div class="pull-right mb-2"><a href="{{ url('additional-product/'.Request::segment(3).'/create') }}" class="btn btn-success"><i class="fa fa-plus-circle"></i> Create Additional Product</a></div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $key => $product)
                    <tr>
                        <td>{{$products->firstItem() + $key }}</td>
                        <td>
                            @if($product->image)
                            <img src="<?php echo asset($product->image) ?>" class="img-thumbnail" style="width:140px; height:140px;" />
                            @else
                            <img src="<?php echo asset('/images/no-image.jpg') ?>" class="img-thumbnail" style="width:140px; height:140px;" />
                            @endif
                        </td>
                        <td>{{$product->name}}</td>
                        <td>Rp. {{number_format($product->price,0)}}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a class="btn btn-primary btn-sm mr-1" href="{{ url('additional-product/'.$product->id.'/edit') }}">Edit</a>
                                <button type="button" onClick="handleDelete({{$product->id}})" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $products->links() }}
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-center">Are you sure you want to delete this product?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <form action="{{ route('additional-product.destroy', 'id') }}" method="post">
                    @csrf
                    @method('DELETE')
                    <input id="id" name="id" hidden />
                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('register-scriptcode')
<script>
    function handleDelete(id) {
        document.getElementById("id").value = id;
    }
</script>
@endsection