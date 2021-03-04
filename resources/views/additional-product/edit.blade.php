@extends('layouts.app')

@section('content')

@include('layouts.sidemenu')

<div class="col-md-9 my-2">
    <div class="card">
        <div class="card-header">{{ __('Edit Product') }}</div>
        <div class="card-body">
            <form method="POST" action="{{route('additional-product.update',$product->id)}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    @if($product->image)
                    <img src="<?php echo asset($product->image) ?>" class="img-thumbnail" style="width:140px; height:140px;" />
                    @else
                    <img src="<?php echo asset('/images/no-image.jpg') ?>" class="img-thumbnail" style="width:140px; height:140px;" />
                    @endif
                </div>
                <div class="form-group">
                    <label for="productImage">Image</label>
                    <input type="file" class="form-control-file" id="productImage" name="image" value="{{ $product->image }}">
                </div>
                <div class="form-group">
                    <label for="produdctName">Name *</label>
                    <input type="text" class="form-control" id="produdctName" name="name" value="{{old('name', $product->name)}}">
                    @error('name')
                    <span class="text-danger"><i>{{ $message }}</i></span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="productPrice">Price *</label>
                    <input type="text" class="form-control" id="productPrice" name="price" value="{{old('price', $product->price)}}">
                    @error('price')
                    <span class="text-danger"><i>{{ $message }}</i></span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="productDesc">Desc</label>
                    <textarea class="form-control" id="productDesc" rows="3" name="desc">{{$product->desc}}</textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('product.index') }}" class="btn btn-outline-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection