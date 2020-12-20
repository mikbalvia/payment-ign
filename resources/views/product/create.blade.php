@extends('layouts.app')

@section('content')

@include('layouts.sidemenu')

<div class="col-md-9 my-2">
    <div class="card">
        <div class="card-header">{{ __('Create Product') }}</div>
        <div class="card-body">
            <form method="POST" action="{{route('product.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="productImage">Image</label>
                    <input type="file" class="form-control-file" id="productImage" name="image">
                    @error('image')
                    <span class="text-danger"><i>{{ $message }}</i></span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="produdctName">Name *</label>
                    <input type="text" class="form-control" id="produdctName" name="name" value="{{ old('name') }}">
                    @error('name')
                    <span class="text-danger"><i>{{ $message }}</i></span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="produdctName">Code *</label>
                    <input type="text" class="form-control" maxlength="6" id="produdctCode" name="code" value="{{ old('code') }}" placeholder="EX: 0001">
                    @error('code')
                    <span class="text-danger"><i>{{ $message }}</i></span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="productPrice">Price *</label>
                    <input type="text" class="form-control" id="productPrice" name="price" value="{{ old('price') }}">
                    @error('price')
                    <span class="text-danger"><i>{{ $message }}</i></span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="productDesc">Desc</label>
                    <textarea class="form-control" id="productDesc" rows="3" name="desc"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Save</button>
                    <a href="{{ route('product.index') }}" class="btn btn-outline-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection