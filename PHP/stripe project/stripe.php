<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php'; // Ensure this path is correct

\Stripe\Stripe::setApiKey('YOUR_API_KEY_HERE'); // Replace with your real test key

try {
    $account = \Stripe\Account::retrieve();composer show stripe/stripe-php

    echo "<p style='color: green;'>✅ Stripe Key is valid. Account Name: " . $account->business_profile->name . "</p>";
} catch (\Stripe\Exception\AuthenticationException $e) {
    echo "<p style='color: red;'>❌ Invalid Stripe Secret Key: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>⚠️ Error: " . $e->getMessage() . "</p>";
}


?>
