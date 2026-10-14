<?php
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('YOUR_API_KEY_HERE'); // Replace with your real Stripe secret key

$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => ['name' => 'Test Product'],
            'unit_amount' => 2000, // $20.00 in cents
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'http://localhost:8000/success.html',
    'cancel_url' => 'http://localhost:8000/cancel.html',
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $session->url);
exit();
