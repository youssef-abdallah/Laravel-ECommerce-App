<?php

namespace App\Http\Controllers;

use App\Order;
use DateTime;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\PaymentIntent;


class CheckoutController extends Controller
{
    public function index()
    {
        if (Cart::count() <= 0)
        {
            return redirect()->route('products.index');
        }
        Stripe::setApiKey('sk_test_4eC39HqLyjWDarjtT1zdp7dc');
        $paymentIntent = PaymentIntent::create([
            'amount' => round(Cart::total()),
            'currency' => 'usd',
            'metadata' => [
                'userId' => Auth::user()->id
            ]
        ]);

        $clientSecret = Arr::get($paymentIntent, 'client_secret');
        return view('checkout.index', [
            'clientSecret' => $clientSecret
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->json()->all();
        $order = new Order();
        $order->payment_intent_id = $data['paymentIntent']['id'];
        $order->amount = $data['paymentIntent']['amount'];
        $order->payment_created_at = (new DateTime())
            ->setTimestamp($data['paymentIntent']['created'])
            ->format('Y-m-d H:i:s');
        $products = [];
        $i = 0;
        foreach (Cart::content() as $product)
        {
            $products['product_'.$i][] = $product->model->title;
            $products['product_'.$i][] = $product->model->price;
            $products['product_'.$i][] = $product->qty;
            $i++;
        }
        $order->products = serialize($products);
        $order->user_id = Auth::user()->id;
        $order->save();
        if ($data['paymentIntent']['status'] === 'succeeded')
        {
            Session::flash('success', 'Your payment succeeded!');
            Cart::destroy();
            return response()->json(['success' => 'Payment Intent succeeded']);
        }
        return response()->json(['error' => 'Payment Intent did not succeed']);
    }

    public function thanks()
    {
        return Session::has('success') ? view('checkout.thanks') : redirect()
            ->route('products.index');
    }
}
