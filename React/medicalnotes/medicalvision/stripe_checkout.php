<?php
require 'vendor/autoload.php';
session_start();

// Stripe Test Secret Key
\Stripe\Stripe::setApiKey('YOUR_API_KEY_HERE');

// Validate credits
$credits = isset($_POST['credits']) ? (int) $_POST['credits'] : 0;
if ($credits <= 0) {
    $credits = 1; // default to 1 credit to avoid invalid amount
}
$amount = $credits * 100; // cents

// Build a robust base URL from current request (keeps port if any)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST']; // includes port when non-80/443
$dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); // e.g. /medicalvision
$baseUrl = $scheme . '://' . $host . $dir;

$successUrl = $baseUrl . '/payment_success.php?session_id={CHECKOUT_SESSION_ID}&credits=' . urlencode((string)$credits);
$cancelUrl  = $baseUrl . '/buy_credits.php';

$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => $credits . ' Credits'
            ],
            'unit_amount' => $amount,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => $successUrl,
    'cancel_url'  => $cancelUrl,
]);

header('Location: ' . $checkout_session->url);
exit;
