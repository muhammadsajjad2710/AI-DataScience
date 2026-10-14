        </div>
    </div>
</div>

<!-- Enhanced Footer -->
<footer class="enhanced-footer">
    <div class="footer-content">
        <div class="footer-section">
            <div class="footer-brand">
                <div class="footer-logo">
                    <i class="fas fa-brain"></i>
                </div>
                <h4>Chief AI Medical Support</h4>
                <p>Advanced AI-powered medical audio processing for healthcare professionals</p>
            </div>
        </div>
        
        <div class="footer-section">
            <h5>Quick Links</h5>
            <ul class="footer-links">
                <li><a href="#upload"><i class="fas fa-upload"></i> Audio Upload</a></li>
                <li><a href="#convert"><i class="fas fa-microphone"></i> AI Processing</a></li>
                <li><a href="#read"><i class="fas fa-book-open"></i> Read Response</a></li>
                <li><a href="buy_credits.php"><i class="fas fa-credit-card"></i> Buy Credits</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h5>Features</h5>
            <ul class="footer-links">
                <li><i class="fas fa-check text-success"></i> Speech-to-Text</li>
                <li><i class="fas fa-check text-success"></i> Medical Analysis</li>
                <li><i class="fas fa-check text-success"></i> HIPAA Compliant</li>
                <li><i class="fas fa-check text-success"></i> 24/7 Processing</li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h5>Support</h5>
            <ul class="footer-links">
                <li><a href="#"><i class="fas fa-question-circle"></i> Help Center</a></li>
                <li><a href="#"><i class="fas fa-envelope"></i> Contact Support</a></li>
                <li><a href="#"><i class="fas fa-file-alt"></i> Documentation</a></li>
                <li><a href="#"><i class="fas fa-shield-alt"></i> Privacy Policy</a></li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="footer-bottom-content">
            <p>&copy; <?php echo date("Y"); ?> Chief AI Medical Support. All rights reserved.</p>
            <div class="footer-social">
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="social-link"><i class="fab fa-github"></i></a>
                <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Enhanced JavaScript -->
<script>
    // Enhanced Sidebar Toggle with Multiple States
    let sidebarState = 'expanded'; // expanded, collapsed, hidden
    
    function updateToggleStatus() {
        const toggleBtn = document.getElementById('sidebarToggle');
        const toggleIcon = toggleBtn.querySelector('.toggle-icon');
        const toggleStatus = document.querySelector('.toggle-status');
        
        if (toggleStatus) {
            if (sidebarState === 'expanded') {
                toggleBtn.title = 'Collapse Sidebar - Click to make sidebar smaller';
                toggleStatus.textContent = '(Click to collapse)';
            } else if (sidebarState === 'collapsed') {
                toggleBtn.title = 'Hide Sidebar - Click to completely hide sidebar';
                toggleStatus.textContent = '(Click to hide)';
            } else {
                toggleBtn.title = 'Show Sidebar - Click to show sidebar again';
                toggleStatus.textContent = '(Click to show)';
            }
        }
    }
    
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        const sidebar = document.getElementById('sidebarMenu');
        const content = document.getElementById('contentArea');
        const toggleIcon = this.querySelector('.toggle-icon');
        
        // Cycle through states: expanded -> collapsed -> hidden -> expanded
        if (sidebarState === 'expanded') {
            // Collapse sidebar
            sidebar.classList.remove('hidden');
            sidebar.classList.add('collapsed');
            content.classList.add('expanded');
            sidebarState = 'collapsed';
            toggleIcon.className = 'fas fa-chevron-left toggle-icon';
        } else if (sidebarState === 'collapsed') {
            // Hide sidebar completely
            sidebar.classList.remove('collapsed');
            sidebar.classList.add('hidden');
            content.classList.remove('expanded');
            content.classList.add('full-width');
            sidebarState = 'hidden';
            toggleIcon.className = 'fas fa-chevron-right toggle-icon';
        } else {
            // Show expanded sidebar
            sidebar.classList.remove('hidden');
            sidebar.classList.remove('collapsed');
            content.classList.remove('expanded');
            content.classList.remove('full-width');
            sidebarState = 'expanded';
            toggleIcon.className = 'fas fa-bars toggle-icon';
        }
        
        // Update toggle status text
        updateToggleStatus();
        
        // Store state in localStorage
        localStorage.setItem('sidebarState', sidebarState);
    });

    // Restore sidebar state on page load
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebarMenu');
        const content = document.getElementById('contentArea');
        const toggleBtn = document.getElementById('sidebarToggle');
        const toggleIcon = toggleBtn.querySelector('.toggle-icon');
        
        const savedState = localStorage.getItem('sidebarState') || 'expanded';
        sidebarState = savedState;
        
        if (savedState === 'collapsed') {
            sidebar.classList.add('collapsed');
            content.classList.add('expanded');
            toggleIcon.className = 'fas fa-chevron-left toggle-icon';
            toggleBtn.title = 'Hide Sidebar';
        } else if (savedState === 'hidden') {
            sidebar.classList.add('hidden');
            content.classList.add('full-width');
            toggleIcon.className = 'fas fa-chevron-right toggle-icon';
            toggleBtn.title = 'Show Sidebar';
        } else {
            // Default expanded state
            sidebar.classList.remove('collapsed', 'hidden');
            content.classList.remove('expanded', 'full-width');
            toggleIcon.className = 'fas fa-bars toggle-icon';
        }
        
        // Update toggle status text
        updateToggleStatus();
    });

    // Keyboard shortcut for sidebar toggle (Ctrl/Cmd + B)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            document.getElementById('sidebarToggle').click();
        }
    });

    // Enhanced Form Spinner
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            }
        });
    });

    // Smooth Scrolling for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Active Navigation Highlighting
    function updateActiveNav() {
        const sections = document.querySelectorAll('[id]');
        const navLinks = document.querySelectorAll('.sidebar a');
        
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (window.pageYOffset >= sectionTop - 200) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    }

    // Update active navigation on scroll
    window.addEventListener('scroll', updateActiveNav);
    
    // Initial active navigation
    updateActiveNav();

    // Enhanced Mobile Sidebar Behavior
    function handleMobileSidebar() {
        const sidebar = document.getElementById('sidebarMenu');
        const content = document.getElementById('contentArea');
        
        if (window.innerWidth <= 768) {
            // On mobile, default to hidden sidebar
            if (!localStorage.getItem('sidebarState')) {
                sidebar.classList.add('hidden');
                content.classList.add('full-width');
                sidebarState = 'hidden';
                localStorage.setItem('sidebarState', 'hidden');
            }
        }
    }

    // Handle mobile sidebar on load and resize
    window.addEventListener('load', handleMobileSidebar);
    window.addEventListener('resize', handleMobileSidebar);

    // Performance optimization for lazy loading images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
</script>

<!-- Enhanced Footer Styles -->
<style>
.enhanced-footer {
    background: linear-gradient(135deg, var(--dark-color) 0%, #1a202c 100%);
    color: white;
    margin-top: 4rem;
    position: relative;
    overflow: hidden;
}

.enhanced-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 3rem;
    padding: 3rem 2rem;
    position: relative;
    z-index: 2;
}

.footer-section h5 {
    color: var(--accent-color);
    font-weight: 700;
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
    position: relative;
}

.footer-section h5::after {
    content: '';
    position: absolute;
    bottom: -0.5rem;
    left: 0;
    width: 30px;
    height: 2px;
    background: var(--accent-color);
    border-radius: 1px;
}

.footer-brand {
    text-align: center;
}

.footer-logo {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
}

.footer-brand h4 {
    color: white;
    font-weight: 700;
    margin-bottom: 1rem;
}

.footer-brand p {
    color: rgba(255,255,255,0.8);
    line-height: 1.6;
    margin: 0;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.footer-links a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.footer-links a:hover {
    color: var(--accent-color);
    transform: translateX(5px);
}

.footer-links i {
    width: 16px;
    text-align: center;
    color: var(--accent-color);
}

.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding: 2rem;
    position: relative;
    z-index: 2;
}

.footer-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.footer-bottom p {
    margin: 0;
    color: rgba(255,255,255,0.7);
}

.footer-social {
    display: flex;
    gap: 1rem;
}

.social-link {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.2);
}

.social-link:hover {
    background: var(--accent-color);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 2rem;
        padding: 2rem 1rem;
    }
    
    .footer-section h5::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .footer-bottom-content {
        flex-direction: column;
        text-align: center;
    }
    
    .footer-social {
        justify-content: center;
    }
}

/* Animation Classes */
.footer-section {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
    transform: translateY(30px);
}

.footer-section:nth-child(1) { animation-delay: 0.1s; }
.footer-section:nth-child(2) { animation-delay: 0.2s; }
.footer-section:nth-child(3) { animation-delay: 0.3s; }
.footer-section:nth-child(4) { animation-delay: 0.4s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Enhanced Spinner */
.spinner-border {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
}

/* Lazy Loading */
img.lazy {
    opacity: 0;
    transition: opacity 0.3s;
}

img.lazy.loaded {
    opacity: 1;
}
</style>

</body>
</html>
