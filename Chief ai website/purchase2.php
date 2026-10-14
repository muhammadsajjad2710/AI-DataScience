<?php
/**
 * Page 2: Creates PaymentIntent on the server, then shows Stripe Elements for payment
 */

// Make sure user arrived here with POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['credits'])) {
    // Redirect back to Page 1 if no data
    header('Location: purchase1.php');
    exit;
}

// 1. Retrieve the credits from the POST
$credits = (int)$_POST['credits'];
$dollars = floor($credits / 10000);
if ($dollars < 1) {
    $dollars = 1;
}
$amountInCents = $dollars * 100;

// 2. Include Stripe PHP library
require_once __DIR__ . '/stripe-php-master/init.php';

// 3. Set your **secret key** (never put this in client-side code!)
\Stripe\Stripe::setApiKey('');

// 4. Create a PaymentIntent on the server
try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amountInCents,
        'currency' => 'usd',
        'metadata' => [
            'credits' => $credits,
        ],
    ]);
    $clientSecret = $paymentIntent->client_secret;
} catch (\Exception $e) {
    // Handle error (e.g., invalid API key, network issues, etc.)
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Complete Payment</title>
    <!-- Load Stripe.js on the client side (publishable key only!) -->
    <script src="https://js.stripe.com/v3"></script>
    <style>
        /* Basic styling for Stripe Elements */
        .StripeElement {
            box-sizing: border-box;
            height: 40px;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: white;
        }
        #card-errors {
            color: red;
            margin-top: 8px;
        }
        button {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<?php if (!empty($error)): ?>
    <!-- If PaymentIntent creation failed, show error -->
    <p style="color:red;">Error creating PaymentIntent: <?php echo htmlspecialchars($error); ?></p>
    <p><a href="purchase1.php">Go back</a></p>

<?php else: ?>
    <h2>Complete Your Purchase</h2>
    <p>You are buying <strong><?php echo $credits; ?></strong> credits for $<?php echo $dollars; ?>.</p>
    
    <!-- Stripe Elements container -->
    <div id="card-element"></div>
    <div id="card-errors"></div>
    <button id="submit">Pay Now</button>

    <script>
        // 1. Initialize Stripe using your PUBLISHABLE key
        //    Example: 'pk_test_1234' or 'pk_live_ABC123'
        const stripe = Stripe('YOUR_API_KEY_HERE');

        // 2. Create an instance of Elements
        const elements = stripe.elements();

        // 3. Create a card element and mount it
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                },
            },
        });
        cardElement.mount('#card-element');

        // 4. Grab the PaymentIntent client_secret from the server
        const clientSecret = "<?php echo $clientSecret; ?>";

        // 5. Handle form submission
        const button = document.getElementById('submit');
        const cardErrors = document.getElementById('card-errors');

        button.addEventListener('click', async (event) => {
            event.preventDefault();
            button.disabled = true;
            cardErrors.textContent = '';

            // Confirm the card payment on the client
            const { paymentIntent, error } = await stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: cardElement,
                }
            });

            if (error) {
                // Show error to the customer
                cardErrors.textContent = error.message;
                button.disabled = false;
            } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                // Payment succeeded!
                alert("Payment succeeded! Thank you.");
                // Normally, you'd redirect or show a success message
                window.location.href = "https://example.com/success";
            }
        });
    </script>
<?php endif; ?>

</body>
</html>
