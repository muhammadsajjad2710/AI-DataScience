<?php
// upload_form.php
$uploadMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = __DIR__ . "/../storage/uploads/images/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = basename($_FILES['image']['name']);
    $uploadFile = $uploadDir . $fileName;

    $uploadedFileHash = md5_file($_FILES['image']['tmp_name']);

    $duplicateFound = false;
    foreach (scandir($uploadDir) as $file) {
        if ($file === "." || $file === "..") continue;
        if (md5_file($uploadDir . $file) === $uploadedFileHash) {
            $duplicateFound = true;
            $uploadFile = $uploadDir . $file;
            break;
        }
    }

    if ($duplicateFound || move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        $_SESSION['uploaded_image'] = $uploadFile;
        $uploadMessage = "✅ Image ready. You can now extract text.";
    } else {
        $uploadMessage = "❌ Error uploading image.";
    }
}
?>

<form id="uploadForm" method="post" enctype="multipart/form-data">
    <input type="file" name="image" class="form-control mb-3" required>
    <button type="submit" class="btn btn-success">Upload Image</button>
</form>

<?php if (!empty($uploadMessage)) echo "<p class='mt-3'>$uploadMessage</p>"; ?>

<?php if (!empty($_SESSION['uploaded_image'])): ?>
    <div class="mt-3">
        <button id="extractBtn" class="btn btn-primary">Extract Text (Run OCR)</button>
    </div>

    <div id="resultCard" class="card mt-4" style="display:none;">
        <div class="card-body">
            <h5 class="card-title">Extraction Result</h5>
            <div id="extractedText" style="white-space: pre-wrap;"></div>
            <hr>
            <h6>AI Response</h6>
            <div id="aiResponse" style="white-space: pre-wrap; color: #333;"></div>
            <hr>
            <div id="messages" class="small text-muted"></div>
        </div>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function (e) {
            // standard form post for uploading the file
            // keep behavior as-is (server will reload page and show Extract button)
        });
    }

    const extractBtn = document.getElementById('extractBtn');
    if (extractBtn) {
        extractBtn.addEventListener('click', async function () {
            extractBtn.disabled = true;
            extractBtn.innerHTML = 'Processing... <span class="spinner-border spinner-border-sm"></span>';

            const imagePath = <?= json_encode(!empty($_SESSION['uploaded_image']) ? $_SESSION['uploaded_image'] : '') ?>;
            if (!imagePath) {
                alert('No uploaded image found in session.');
                extractBtn.disabled = false;
                extractBtn.innerHTML = 'Extract Text (Run OCR)';
                return;
            }

            try {
                const formData = new FormData();
                formData.append('image_path', imagePath);

                const resp = await fetch('process.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await resp.json();

                const resultCard = document.getElementById('resultCard');
                const extractedTextEl = document.getElementById('extractedText');
                const aiResponseEl = document.getElementById('aiResponse');
                const messagesEl = document.getElementById('messages');

                if (data.status === 'success') {
                    extractedTextEl.textContent = data.extracted_text || '(no text)';
                    aiResponseEl.textContent = data.ai_response || '(no AI response — set OPENAI_API_KEY to enable)';
                    messagesEl.innerHTML = `✅ ${data.message} &nbsp; <strong>Remaining credits: ${data.remaining_credits}</strong>`;
                } else {
                    extractedTextEl.textContent = '';
                    aiResponseEl.textContent = '';
                    if (data.raw_output) {
                        messagesEl.innerHTML = `<span style="color:red;">${data.message}</span><pre style="background:#f8f9fa;padding:8px;">${data.raw_output}</pre>`;
                    } else {
                        messagesEl.innerHTML = `<span style="color:red;">${data.message}</span>`;
                    }
                }

                resultCard.style.display = 'block';
            } catch (err) {
                alert('An error occurred: ' + err);
            } finally {
                extractBtn.disabled = false;
                extractBtn.innerHTML = 'Extract Text (Run OCR)';
            }
        });
    }
});
</script>
