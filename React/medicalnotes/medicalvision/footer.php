        </div>
    </div>
</div>
<footer class="mv-footer" role="contentinfo">
    <div class="mv-footer-content">
        <div class="mv-footer-brand">
            <div class="mv-footer-logo" aria-hidden="true"><i class="fas fa-eye"></i></div>
            <div>
                <h4 class="mv-footer-title">MedicalVision</h4>
                <p class="mv-footer-tagline">AI‑powered medical document analysis with OCR and FHIR</p>
            </div>
        </div>

        <div class="mv-footer-grid">
            <div class="mv-footer-section">
                <h5>Navigate</h5>
                <ul>
                    <li><a href="index.php#step1"><i class="fas fa-upload"></i> Upload</a></li>
                    <li><a href="index.php#step2"><i class="fas fa-magic"></i> Extract Text</a></li>
                    <li><a href="index.php#step3"><i class="fas fa-book-open"></i> Read Response</a></li>
                    <li><a href="buy_credits.php"><i class="fas fa-coins"></i> Buy Credits</a></li>
                </ul>
            </div>
            <div class="mv-footer-section">
                <h5>Resources</h5>
                <ul>
                    <li><a href="#" aria-disabled="true"><i class="fas fa-file-alt"></i> Documentation</a></li>
                    <li><a href="#" aria-disabled="true"><i class="fas fa-shield-alt"></i> Privacy</a></li>
                    <li><a href="#" aria-disabled="true"><i class="fas fa-scale-balanced"></i> Terms</a></li>
                </ul>
            </div>
            <div class="mv-footer-section">
                <h5>Support</h5>
                <ul>
                    <li><a href="#" aria-disabled="true"><i class="fas fa-question-circle"></i> Help Center</a></li>
                    <li><a href="#" aria-disabled="true"><i class="fas fa-envelope"></i> Contact</a></li>
                </ul>
            </div>
            <div class="mv-footer-section">
                <h5>Follow</h5>
                <div class="mv-footer-social">
                    <a href="#" class="mv-social" aria-label="Twitter" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="mv-social" aria-label="LinkedIn" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    <a href="#" class="mv-social" aria-label="GitHub" title="GitHub"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="mv-footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Chief AI. All rights reserved.</p>
    </div>
</footer>

<style>
    .mv-footer { 
        margin-top: 2.5rem; 
        color: #fff; 
        background: linear-gradient(135deg, var(--dark-color, #2d3748) 0%, #1a202c 100%);
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    .mv-footer-content { 
        max-width: 1200px; 
        margin: 0 auto; 
        padding: 2.5rem 1.25rem 1.25rem; 
        display: grid; 
        grid-template-columns: 1fr; 
        gap: 1.75rem; 
    }
    .mv-footer-brand { 
        display: flex; 
        align-items: center; 
        gap: 1rem; 
    }
    .mv-footer-logo { 
        width: 56px; height: 56px; 
        border-radius: 14px; 
        display: flex; align-items: center; justify-content: center; 
        background: linear-gradient(135deg, var(--accent-color, #f093fb), var(--primary-color, #667eea));
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .mv-footer-logo i { color:#fff; font-size: 1.25rem; }
    .mv-footer-title { margin: 0; font-size: 1.25rem; font-weight: 800; }
    .mv-footer-tagline { margin: .25rem 0 0; color: rgba(255,255,255,0.8); font-size: .95rem; }

    .mv-footer-grid { 
        display: grid; 
        grid-template-columns: repeat(4, minmax(0, 1fr)); 
        gap: 1.5rem; 
    }
    .mv-footer-section h5 { margin: 0 0 .75rem; font-size: .95rem; color: var(--accent-color, #f093fb); }
    .mv-footer-section ul { list-style: none; padding: 0; margin: 0; display: grid; gap: .5rem; }
    .mv-footer-section a { color: rgba(255,255,255,0.9); text-decoration: none; display: inline-flex; align-items: center; gap: .5rem; }
    .mv-footer-section a:hover { color: #fff; transform: translateX(2px); transition: transform .2s ease, color .2s ease; }
    .mv-footer-section i { width: 16px; text-align: center; color: var(--accent-color, #f093fb); }

    .mv-footer-social { display: flex; gap: .75rem; }
    .mv-social { 
        width: 40px; height: 40px; 
        border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; 
        color: #fff; text-decoration: none; 
        border: 1px solid rgba(255,255,255,0.2); 
        background: rgba(255,255,255,0.08);
        transition: transform .2s ease, background .2s ease;
    }
    .mv-social:hover { transform: translateY(-2px); background: var(--accent-color, #f093fb); }

    .mv-footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); padding: 1rem 1.25rem; text-align: center; color: rgba(255,255,255,0.85); }

    @media (max-width: 900px) {
        .mv-footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 600px) {
        .mv-footer-content { padding: 2rem 1rem 1rem; }
        .mv-footer-grid { grid-template-columns: 1fr; }
        .mv-footer-brand { flex-direction: column; align-items: flex-start; }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    if (toggleBtn && sidebar && mainContent) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
        });
    }
</script>
</body>
</html>
