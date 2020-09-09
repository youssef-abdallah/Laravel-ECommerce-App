<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;

class ProductController extends Controller
{
    public function index()
    {
        if (request()->category)
        {
            $products = Product::whereHas('categories', function ($query) {
                $query->where('slug', request()->category);
            })->paginate(6);
        }
        else 
            $products = Product::with('categories')->paginate(6);
        return view('products.index', ['products' => $products]);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return view('products.show', [
            'product' => $product
        ]);
    }

    public function search()
    {
        request()->validate([
            'q' => 'required|min:3'
        ]);
        $q = request()->input('q');
        
        $products = Product::where('title', 'like', "%$q%")
            ->orWhere('description', 'like', "%$q%")
            ->paginate('6');

        return view('products.search')->with('products', $products);
    }
}
