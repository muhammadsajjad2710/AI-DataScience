<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // ✅ Show errors in browser

require 'vendor/autoload.php';
session_start();

\Stripe\Stripe::setApiKey('YOUR_API_KEY_HERE');

$credits = intval($_POST['credits']);
$amount = $credits * 100;

$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => ['name' => "$credits Credits"],
            'unit_amount' => $amount,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'http://yourdomain.com/payment_success.php?session_id={CHECKOUT_SESSION_ID}&credits=' . $credits,
    'cancel_url' => 'http://yourdomain.com/index.php',
]);

header("Location: " . $checkout_session->url);
exit;
?>
