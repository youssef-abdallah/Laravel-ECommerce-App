@extends ('layouts.master')

@section ('extra-meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section ('extra-script')
<script src="https://js.stripe.com/v3/"></script>
@endsection


@section ('content')
    <div class="col-md-12">
        <h1>Payment Page</h1>
        <form action="{{ route('checkout.store') }}" method="POST" id="payment-form" class="my-4">
            @csrf
            <div id="card-element"><!--Stripe.js injects the Card Element--></div>
            <button id="submit" class="mt-4 btn btn-success">
                <div class="spinner hidden" id="spinner"></div>
                <span id="button-text">Pay ({{ getPrice(Cart::total()) }})</span>
            </button>
            <p id="card-error" role="alert"></p>
            <p hidden class="result-message">
                Payment succeeded, see the result in your
                <a href="" target="_blank">Stripe dashboard.</a> Refresh the page to pay again.
            </p>
        </form>
    </div>
@endsection

@section ('extra-js')
    <script>
        var stripe = Stripe("pk_test_TYooMQauvdEDq54NiTphI7jx");
        var elements = stripe.elements();
        var style = {
        base: {
            color: "#32325d",
            fontFamily: 'Arial, sans-serif',
            fontSmoothing: "antialiased",
            fontSize: "16px",
            "::placeholder": {
            }
            },
            invalid: {
                fontFamily: 'Arial, sans-serif',
                color: "#fa755a",
            }
        };
        var card = elements.create("card", { style: style });
        // Stripe injects an iframe into the DOM
        card.mount("#card-element");
        card.on("change", function (event) {
        // Disable the Pay button if there are no card details in the Element
            document.querySelector("button").disabled = event.empty;
            document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
        });
        var form = document.getElementById("payment-form");
        form.addEventListener("submit", function(event) {
            event.preventDefault();
            // Complete payment when the submit button is clicked
            let clientSecret = '{{ $clientSecret }}';
            payWithCard(stripe, card, clientSecret);
        });
        var payWithCard = function(stripe, card, clientSecret) {
        stripe
            .confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card
                }
            })
            .then(function(result) {
                if (result.error) {
                    // Show error to your customer
                    showError(result.error.message);
                } else {
                    // The payment succeeded!
                    orderComplete(result.paymentIntent.id);
                    var url = form.action;
                    var redirect = '/thanks';
                    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    
                    fetch(
                        url,
                        {
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json, text-plain, */*",
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": token
                            },
                            method: 'post',
                            body: JSON.stringify({
                                paymentIntent: result.paymentIntent
                            })
                        }).then((data) => {
                            console.log(data);
                            form.reset();
                            window.location.href = redirect;
                    }).catch((error) => {
                        console.log(error)
                    })
            }
            });
        };
        var showError = function(errorMsgText) {
            var errorMsg = document.querySelector("#card-error");
            errorMsg.textContent = errorMsgText;
            setTimeout(function() {
                errorMsg.textContent = "";
            }, 4000);
        };
        var orderComplete = function(paymentIntentId) {
            document
                .querySelector(".result-message a")
                .setAttribute(
                "href",
                "https://dashboard.stripe.com/test/payments/" + paymentIntentId
                );
            document.querySelector(".result-message").classList.remove("hidden");
            document.querySelector("button").disabled = true;
        };
    </script>
@endsection