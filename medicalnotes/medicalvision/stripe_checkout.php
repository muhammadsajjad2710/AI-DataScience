<?php
require 'vendor/autoload.php';
session_start();

// Stripe Test Secret Key
\Stripe\Stripe::setApiKey('YOUR_API_KEY_HERE');

// Get credits from form
$credits = intval($_POST['credits']);
$amount = $credits * 100; // Stripe uses cents

// Update these URLs to your local or live environment
$domain = 'http://localhost/medicalvision';

$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => "$credits Credits"
            ],
            'unit_amount' => $amount,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => $domain . '/payment_success.php?session_id={CHECKOUT_SESSION_ID}&credits=' . $credits,
    'cancel_url' => $domain . '/index.php',
]);

header("Location: " . $checkout_session->url);
exit;
