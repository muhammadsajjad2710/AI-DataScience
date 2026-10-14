<?php
session_start();
include('header.php');

// ✅ Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    echo "<div class='alert alert-warning mt-3'>
            <h4>⚠️ Session Expired</h4>
            <p>Please log in again to complete your purchase.</p>
            <a href='../login.php' class='btn btn-primary'>Login</a>
          </div>";
    exit;
}

// ✅ Check if credits parameter exists
if (!isset($_GET['credits'])) {
    echo "<div class='alert alert-danger mt-3'>
            <h4>❌ Invalid Request</h4>
            <p>No credits specified for this purchase.</p>
            <a href='index.php' class='btn btn-primary'>← Back to Dashboard</a>
          </div>";
    exit;
}

$credits = intval($_GET['credits']);
$userId = $_SESSION['member_id'];

// ✅ Validate credits amount
if ($credits <= 0 || $credits > 100) {
    echo "<div class='alert alert-danger mt-3'>
            <h4>❌ Invalid Credits Amount</h4>
            <p>The specified credits amount is invalid.</p>
            <a href='index.php' class='btn btn-primary'>← Back to Dashboard</a>
          </div>";
    exit;
}

try {
    include('database.php');
    
    // ✅ Update user credits
    $stmt = $conn->prepare("UPDATE members SET credits = credits + ? WHERE member_id = ?");
    $stmt->bind_param("ii", $credits, $userId);
    $result = $stmt->execute();
    
    if ($result) {
        // ✅ Get updated credit balance
        $stmt = $conn->prepare("SELECT credits FROM members WHERE member_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($newBalance);
        $stmt->fetch();
        $stmt->close();
        
        echo "<div class='container mt-5'>
                <div class='row justify-content-center'>
                    <div class='col-md-8'>
                        <div class='card border-success shadow'>
                            <div class='card-header bg-success text-white text-center'>
                                <h3><i class='fas fa-check-circle'></i> Payment Successful!</h3>
                            </div>
                            <div class='card-body text-center p-5'>
                                <div class='mb-4'>
                                    <i class='fas fa-coins text-warning' style='font-size: 4rem;'></i>
                                </div>
                                <h4 class='text-success mb-3'>✅ Credits Added Successfully!</h4>
                                <p class='lead'>Your account has been credited with <strong class='text-primary'>$credits credits</strong>.</p>
                                <p class='text-muted'>New balance: <strong class='text-success'>$newBalance credits</strong></p>
                                
                                <div class='mt-4'>
                                    <a href='index.php' class='btn btn-primary btn-lg me-3'>
                                        <i class='fas fa-home'></i> Return to Dashboard
                                    </a>
                                    <a href='buy_credits.php' class='btn btn-outline-success btn-lg'>
                                        <i class='fas fa-plus'></i> Buy More Credits
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>";
    } else {
        throw new Exception("Failed to update credits in database");
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger mt-3'>
            <h4>❌ Database Error</h4>
            <p>Failed to update your credits. Please contact support.</p>
            <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <a href='index.php' class='btn btn-primary'>← Back to Dashboard</a>
          </div>";
}

include('footer.php');
?>
