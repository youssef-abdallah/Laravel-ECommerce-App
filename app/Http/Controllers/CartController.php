<?php

namespace App\Http\Controllers;

use App\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('cart.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::find($request->id);
        $duplicate = Cart::search(function ($cartItem, $rowId) use ($product) {
            return $cartItem->id === $product->id;
        });
        if ($duplicate->isNotEmpty())
        {
            return redirect()->route('products.index')->with('success', 'the product has already been added');
        }
        Cart::add($product->id, $product->title, 1, $product->price)
            ->associate('App\Product');
        return redirect()->route('products.index')->with('success', 'the product has been added');
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $rowId)
    {
        $data = $request->json()->all();
        $validator = Validator::make($request->all(), [
            'qty' => 'required|numeric|between:1,6'
        ]);

        if ($validator->fails())
        {
            Session::flash('danger', 'The quantity of the product should not exceed 6.');
            return response()->json(['error' => 'Cart quantity has not been updated']);
        }
        Cart::update($rowId, $data['qty']);
        Session::flash('success', 'The quantity has been changed.');
        return response()->json(['success' => 'Cart quantity has been updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($rowid)
    {
        Cart::remove($rowid);

        return back()->with('success', 'The product has been deleted.');
    }
}
