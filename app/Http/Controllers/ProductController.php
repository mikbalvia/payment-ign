<?php

/**
 * Project: Payment Point
 * File: ProductController.php
 * Date: 12/18/20
 * Time: 12:37 PM
 * @author: muhammadikhsan
 * @copyright: IGN &copy; 2020
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use UxWeb\SweetAlert\SweetAlert;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::paginate(10);

        return view('product.index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:200'],
            'price' => ['required', 'numeric'],
            'code' => ['required', 'unique:products,code'],
        ]);

        if ($validator->fails()) {
            return redirect('product/create')
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
                    return redirect('product/create')
                        ->withErrors($validator)
                        ->withInput();
                }

                $imageName = time() . '.' . $request->image->extension();

                $request->image->storeAs('/public', $imageName);
                $url = Storage::url($imageName);
            }
        }

        Product::create([
            'name' => $request->name,
            'image' => $url,
            'code' => Str::upper($request->code),
            'price' => $request->price,
            'tnc_url' => $request->tnc_url,
            'desc' => $request->desc,
            'endpoint' => url("/checkout/step1/" . Str::upper($request->code))
        ]);

        return redirect()->route('product.index')->with('success', 'Product successfully created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        return view('product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);

        return view('product.edit', compact('product'));
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
            'code' => ['required', 'unique:products,code,' . $id]
        ]);

        if ($validator->fails()) {
            return redirect('product/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $product = Product::find($id);
        $url = $product->image;
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {

                //second validation if image is exist
                $validator = Validator::make($request->all(), [
                    'image' => 'image|mimes:jpeg,png,jpg|max:3072',
                ]);

                if ($validator->fails()) {
                    return redirect('product/' . $id . '/edit')
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
        $product->tnc_url = $request->tnc_url;
        $product->desc = $request->desc;
        $product->endpoint = url("/checkout/step1/" . Str::upper($request->code));
        $product->save();

        return redirect()->route('product.index')
            ->with('success', 'Product successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $product = Product::find($request->id);

        //delete previous image if exist
        if ($product->image) {
            $getName = explode("/", $product->image);
            Storage::disk('public')->delete($getName[2]);
        }

        $product = Product::destroy($request->id);

        if ($product) {
            return redirect()->route('product.index')
                ->with('success', 'Product successfully deleted');
        }

        return redirect()->route('product.index')
            ->with('error', 'Failed to delete product');
    }
}
