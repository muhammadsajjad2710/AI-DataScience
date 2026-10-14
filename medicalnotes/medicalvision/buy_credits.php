<?php include('header.php'); ?>
<div class="container mt-5">
    <h2 class="mb-4 text-center text-primary">Buy Credits</h2>
    <form action="stripe_checkout.php" method="POST" class="p-4 border rounded bg-light shadow-sm">
        <div class="mb-3">
            <label for="credits" class="form-label fw-bold">Select Credits:</label>
            <select name="credits" id="credits" class="form-select" required>
                <option value="1">1 Credit - $1</option>
                <option value="5">5 Credits - $5</option>
                <option value="10">10 Credits - $10</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success w-100">Proceed to Payment</button>
    </form>
</div>
<?php include('footer.php'); ?>
