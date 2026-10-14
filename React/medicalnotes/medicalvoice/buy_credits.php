<?php include('header.php'); ?>

<!-- Enhanced Buy Credits Interface -->
<div class="buy-credits-container">
    <!-- Hero Section -->
    <div class="credits-hero text-center py-5 mb-5">
        <div class="hero-content">
            <h1 class="hero-title mb-3">
                <i class="fas fa-coins text-warning me-3"></i>
                Purchase AI Processing Credits
            </h1>
            <p class="hero-subtitle text-muted fs-5">
                Unlock the power of AI-powered medical audio processing with our flexible credit system
            </p>
        </div>
    </div>

    <!-- Credit Packages -->
    <div class="credit-packages mb-5">
        <div class="packages-grid">
            <!-- Basic Package -->
            <div class="package-card" data-credits="1">
                <div class="package-header basic">
                    <div class="package-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="package-title">Basic</h3>
                    <div class="package-price">
                        <span class="currency">$</span>
                        <span class="amount">1</span>
                        <span class="period">/credit</span>
                    </div>
                </div>
                <div class="package-body">
                    <div class="credits-amount">
                        <span class="number">1</span>
                        <span class="label">Credit</span>
                    </div>
                    <ul class="package-features">
                        <li><i class="fas fa-check text-success"></i> 1 Audio Processing</li>
                        <li><i class="fas fa-check text-success"></i> AI Transcription</li>
                        <li><i class="fas fa-check text-success"></i> Medical Analysis</li>
                        <li><i class="fas fa-check text-success"></i> 24/7 Support</li>
                    </ul>
                    <button class="select-package-btn" onclick="selectPackage(1, 1)">
                        Select Package
                    </button>
                </div>
            </div>

            <!-- Popular Package -->
            <div class="package-card popular" data-credits="5">
                <div class="popular-badge">Most Popular</div>
                <div class="package-header popular">
                    <div class="package-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h3 class="package-title">Professional</h3>
                    <div class="package-price">
                        <span class="currency">$</span>
                        <span class="amount">5</span>
                        <span class="period">/total</span>
                    </div>
                    <div class="savings">Save 20%</div>
                </div>
                <div class="package-body">
                    <div class="credits-amount">
                        <span class="number">5</span>
                        <span class="label">Credits</span>
                    </div>
                    <ul class="package-features">
                        <li><i class="fas fa-check text-success"></i> 5 Audio Processings</li>
                        <li><i class="fas fa-check text-success"></i> AI Transcription</li>
                        <li><i class="fas fa-check text-success"></i> Medical Analysis</li>
                        <li><i class="fas fa-check text-success"></i> Priority Support</li>
                        <li><i class="fas fa-check text-success"></i> Bulk Discount</li>
                    </ul>
                    <button class="select-package-btn" onclick="selectPackage(5, 5)">
                        Select Package
                    </button>
                </div>
            </div>

            <!-- Premium Package -->
            <div class="package-card" data-credits="10">
                <div class="package-header premium">
                    <div class="package-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h3 class="package-title">Premium</h3>
                    <div class="package-price">
                        <span class="currency">$</span>
                        <span class="amount">10</span>
                        <span class="period">/total</span>
                    </div>
                    <div class="savings">Save 30%</div>
                </div>
                <div class="package-body">
                    <div class="credits-amount">
                        <span class="number">10</span>
                        <span class="label">Credits</span>
                    </div>
                    <ul class="package-features">
                        <li><i class="fas fa-check text-success"></i> 10 Audio Processings</li>
                        <li><i class="fas fa-check text-success"></i> AI Transcription</li>
                        <li><i class="fas fa-check text-success"></i> Medical Analysis</li>
                        <li><i class="fas fa-check text-success"></i> VIP Support</li>
                        <li><i class="fas fa-check text-success"></i> Maximum Savings</li>
                        <li><i class="fas fa-check text-success"></i> Extended Validity</li>
                    </ul>
                    <button class="select-package-btn" onclick="selectPackage(10, 10)">
                        Select Package
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Package Summary -->
    <div class="package-summary" id="packageSummary" style="display: none;">
        <div class="summary-content">
            <div class="summary-info">
                <h3 class="summary-title">Selected Package</h3>
                <div class="summary-details">
                    <span class="selected-credits" id="selectedCredits">0</span> Credits
                    <span class="selected-price" id="selectedPrice">$0</span>
                </div>
            </div>
            <form action="stripe_checkout.php" method="POST" id="paymentForm">
                <input type="hidden" name="credits" id="hiddenCredits" value="">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                </button>
            </form>
        </div>
    </div>

    <!-- Features Section -->
    <div class="features-section mt-5">
        <h2 class="text-center mb-4">Why Choose Our AI Processing?</h2>
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h4>Advanced AI</h4>
                <p>State-of-the-art machine learning for accurate medical transcription</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4>HIPAA Compliant</h4>
                <p>Your medical data is protected with enterprise-grade security</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>24/7 Processing</h4>
                <p>Process your audio files anytime, anywhere with instant results</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4>99.9% Accuracy</h4>
                <p>Industry-leading accuracy rates for medical transcription</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation back to app -->
    <div class="text-center my-4">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
        <a href="index.php#upload" class="btn btn-primary ms-2">
            <i class="fas fa-cloud-upload-alt me-1"></i> Go to Upload
        </a>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
.buy-credits-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Hero Section */
.credits-hero {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 20px;
    color: white;
    position: relative;
    overflow: hidden;
}

.credits-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-subtitle {
    font-weight: 300;
    opacity: 0.9;
}

/* Credit Packages */
.packages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.package-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
}

.package-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.package-card.popular {
    border-color: #667eea;
    transform: scale(1.05);
}

.package-card.popular:hover {
    transform: scale(1.05) translateY(-5px);
}

.popular-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    z-index: 10;
}

.package-header {
    padding: 2rem 2rem 1.5rem;
    text-align: center;
    color: white;
    position: relative;
}

.package-header.basic {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.package-header.popular {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.package-header.premium {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.package-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.package-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
}

.package-price {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.currency {
    font-size: 1.5rem;
    vertical-align: top;
}

.period {
    font-size: 1rem;
    font-weight: 400;
    opacity: 0.8;
}

.savings {
    background: rgba(255,255,255,0.2);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
}

.package-body {
    padding: 2rem;
    text-align: center;
}

.credits-amount {
    margin-bottom: 2rem;
}

.credits-amount .number {
    font-size: 3rem;
    font-weight: 800;
    color: #2d3748;
    display: block;
}

.credits-amount .label {
    font-size: 1.2rem;
    color: #718096;
    font-weight: 600;
}

.package-features {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem 0;
    text-align: left;
}

.package-features li {
    padding: 0.5rem 0;
    color: #4a5568;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.package-features i {
    width: 20px;
    text-align: center;
}

.select-package-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

.select-package-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* Package Summary */
.package-summary {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 2px solid #667eea;
    margin-bottom: 3rem;
}

.summary-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
    flex-wrap: wrap;
}

.summary-title {
    color: #2d3748;
    margin: 0 0 0.5rem 0;
    font-weight: 600;
}

.summary-details {
    font-size: 1.2rem;
    color: #718096;
}

.selected-credits {
    font-weight: 800;
    color: #667eea;
}

.selected-price {
    font-weight: 800;
    color: #48bb78;
    margin-left: 1rem;
}

/* Features Section */
.features-section {
    background: #f8fafc;
    border-radius: 20px;
    padding: 3rem 2rem;
}

.features-section h2 {
    color: #2d3748;
    font-weight: 700;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.feature-item {
    text-align: center;
    padding: 2rem 1rem;
}

.feature-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    margin: 0 auto 1.5rem;
}

.feature-item h4 {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 1rem;
}

.feature-item p {
    color: #718096;
    margin: 0;
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .packages-grid {
        grid-template-columns: 1fr;
    }
    
    .package-card.popular {
        transform: none;
    }
    
    .package-card.popular:hover {
        transform: translateY(-5px);
    }
    
    .summary-content {
        flex-direction: column;
        text-align: center;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
}

/* Animation Classes */
.package-card {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
    transform: translateY(30px);
}

.package-card:nth-child(1) { animation-delay: 0.1s; }
.package-card:nth-child(2) { animation-delay: 0.2s; }
.package-card:nth-child(3) { animation-delay: 0.3s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<!-- Package Selection JavaScript -->
<script>
function selectPackage(credits, price) {
    // Update hidden form field
    document.getElementById('hiddenCredits').value = credits;
    
    // Update summary display
    document.getElementById('selectedCredits').textContent = credits;
    document.getElementById('selectedPrice').textContent = '$' + price;
    
    // Show summary
    document.getElementById('packageSummary').style.display = 'block';
    
    // Scroll to summary
    document.getElementById('packageSummary').scrollIntoView({ 
        behavior: 'smooth',
        block: 'center'
    });
    
    // Update button states
    document.querySelectorAll('.select-package-btn').forEach(btn => {
        btn.textContent = 'Selected';
        btn.style.background = '#48bb78';
        btn.disabled = true;
    });
    
    // Highlight selected package
    document.querySelectorAll('.package-card').forEach(card => {
        card.style.borderColor = '#e2e8f0';
    });
    
    event.target.closest('.package-card').style.borderColor = '#667eea';
    event.target.textContent = 'Selected';
    event.target.style.background = '#48bb78';
    event.target.disabled = true;
}
</script>

<?php include('footer.php'); ?>
