        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer bg-dark text-white text-center py-3 mt-4">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> Chief AI. All rights reserved.</p>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        document.getElementById('sidebarMenu').classList.toggle('collapsed');
        document.getElementById('contentArea').classList.toggle('expanded');
    });

    // Spinner on form submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
            }
        });
    });
</script>
</body>
</html>
