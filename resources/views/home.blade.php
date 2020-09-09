@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('My Orders') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @foreach (Auth::user()->orders as $order)
                        <div class="card">
                            <div class="card-header">
                                Ordered on {{ Carbon\Carbon::parse($order->payment_created_at)
                                    ->format('d/m/Y at H:i') }} with a total of
                                <strong>{{ getPrice($order->amount) }}</strong>
                            </div>
                            <div class="card-body">
                                <h6>List of products</h6>
                                @foreach (unserialize($order->products) as $product)
                                    <div>Product name: {{ $product[0] }}</div>
                                    <div>Product price: {{ getPrice($product[1]) }}</div>
                                    <div>Quantity: {{ $product[2] }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
