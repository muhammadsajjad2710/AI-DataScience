<?php
// public/index.php

$messagesLogFile = "../logs/messages.log";
$mediaDirectory = "../logs/media/";

// Read the received messages log
$receivedMessages = [];
if (file_exists($messagesLogFile)) {
    $receivedMessages = json_decode(file_get_contents($messagesLogFile), true);
    if (!is_array($receivedMessages)) {
        $receivedMessages = [];
    }
}

// Get media files
$mediaFiles = [];
if (is_dir($mediaDirectory)) {
    $mediaFiles = array_diff(scandir($mediaDirectory), ['.', '..']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp API Integration</title>
</head>
<body>

    <h2>Send WhatsApp Message</h2>
    <form action="../src/sendMessage.php" method="POST">
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" required>
        <br>
        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea>
        <br>
        <button type="submit">Send</button>
    </form>

    <hr>

    <h2>Received Messages</h2>
    <?php if (!empty($receivedMessages)): ?>
        <ul>
            <?php foreach ($receivedMessages as $msg): ?>
                <li><strong>From:</strong> <?= $msg['from'] ?? 'Unknown'; ?> - <strong>Message:</strong> <?= $msg['text']['body'] ?? 'Media received'; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No messages received yet.</p>
    <?php endif; ?>

    <hr>

    <h2>Received Media</h2>
    <?php if (!empty($mediaFiles)): ?>
        <div>
            <?php foreach ($mediaFiles as $file): ?>
                <p><?= $file; ?></p>
                <?php
                $filePath = $mediaDirectory . $file;
                $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                if (in_array($fileExtension, ['jpg', 'png', 'jpeg'])): ?>
                    <img src="<?= $filePath; ?>" width="200" alt="Received Image">
                <?php elseif ($fileExtension == 'mp4'): ?>
                    <video width="200" controls>
                        <source src="<?= $filePath; ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php elseif ($fileExtension == 'mp3'): ?>
                    <audio controls>
                        <source src="<?= $filePath; ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No media received yet.</p>
    <?php endif; ?>

</body>
</html>
