<?php

/**
 * Project: Payment Point
 * File: ProductController.php
 * Date: 03/02/21
 * Time: 10:26 AM
 * @author: muhammadikbal
 * @copyright: IGN &copy; 2021
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdditionalProduct;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use UxWeb\SweetAlert\SweetAlert;

class AdditionalProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($product_id)
    {
        //
        $products = AdditionalProduct::where('product_id',$product_id)->paginate(10);

        return view('additional-product.index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //
        return view('additional-product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:200'],
            'price' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return redirect('additional-product/'.$request->product_id.'/create')
                ->withErrors($validator)
                ->withInput();
        }

        $url = null;
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {

                //second validation if image is exist
                $validator = Validator::make($request->all(), [
                    'image' => 'image|mimes:jpeg,png,jpg|max:3072',
                ]);

                if ($validator->fails()) {
                    return redirect('additional-product/'.$request->product_id.'/create')
                        ->withErrors($validator)
                        ->withInput();
                }

                $imageName = time() . '.' . $request->image->extension();

                $request->image->storeAs('/public', $imageName);
                $url = Storage::url($imageName);
            }
        }

        AdditionalProduct::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'image' => $url,
            'price' => $request->price,
            'desc' => $request->desc,
        ]);

        return redirect('additional-product/product/'.$request->product_id)->with('success', 'Product successfully created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = AdditionalProduct::find($id);

        return view('additional-product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:200'],
            'price' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return redirect('additional-product/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $product = AdditionalProduct::find($id);
        $url = $product->image;
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {

                //second validation if image is exist
                $validator = Validator::make($request->all(), [
                    'image' => 'image|mimes:jpeg,png,jpg|max:3072',
                ]);

                if ($validator->fails()) {
                    return redirect('additional-product/' . $id . '/edit')
                        ->withErrors($validator)
                        ->withInput();
                }

                $imageName = time() . '.' . $request->image->extension();

                $request->image->storeAs('/public', $imageName);
                $url = Storage::url($imageName);

                //delete previous image if exist
                if ($product->image) {
                    $getName = explode("/", $product->image);
                    Storage::disk('public')->delete($getName[2]);
                }
            }
        }

        $product->name = $request->name;
        $product->image = $url;
        $product->price = $request->price;
        $product->desc = $request->desc;
        $product->save();

        return redirect('additional-product/product/'.$product->product_id)
            ->with('success', 'Product successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        $product = AdditionalProduct::find($request->id);

        //delete previous image if exist
        if ($product->image) {
            $getName = explode("/", $product->image);
            Storage::disk('public')->delete($getName[2]);
        }

        $product_delete = AdditionalProduct::destroy($request->id);

        if ($product_delete) {
            return redirect('additional-product/product/'.$product->product_id)
                ->with('success', 'Product successfully deleted');
        }

        return redirect('additional-product/product/'.$product->product_id)
            ->with('error', 'Failed to delete product');
    }
}
