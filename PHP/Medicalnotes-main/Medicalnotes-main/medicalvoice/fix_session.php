<?php
// Session Fix and Debug Script for MedicalVoice
session_start();

// Check if this is a fix request
if (isset($_GET['action']) && $_GET['action'] === 'fix') {
    // Find any existing audio files
    $audioDir = __DIR__ . '/storage/uploads/audio/files/';
    $audioFiles = glob($audioDir . "*.{mp3,m4a,wav,aac}", GLOB_BRACE);
    
    if (count($audioFiles) > 0) {
        // Use the first available audio file
        $audioFile = $audioFiles[0];
        $fileName = basename($audioFile);
        $logFile = str_replace('/files/', '/logs/', $audioFile);
        $logFile = preg_replace('/\.(mp3|m4a|wav|aac)$/i', '.log', $logFile);
        
        // Set session variables
        $_SESSION['uploaded_file'] = $audioFile;
        $_SESSION['log_file'] = $logFile;
        
        echo "Session fixed! Set file: $fileName\n";
        echo "Redirecting to processing page...\n";
        
        header("Location: python_process.php");
        exit;
    } else {
        echo "No audio files found to set in session.\n";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>MedicalVoice Session Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #007bff; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
        .fix-button { background: #28a745; }
        .fix-button:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔍 MedicalVoice Session Debug & Fix Tool</h2>
        
        <div class="section">
            <h3>Session Information</h3>
            <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
            <p><strong>Session Status:</strong> <?php echo session_status() === PHP_SESSION_ACTIVE ? '<span class="success">ACTIVE</span>' : '<span class="error">NOT ACTIVE</span>'; ?></p>
            <p><strong>Session Save Path:</strong> <?php echo ini_get('session.save_path') ?: 'Default'; ?></p>
        </div>
        
        <div class="section">
            <h3>Current Session Variables</h3>
            <?php if (isset($_SESSION['member_id'])): ?>
                <p class="success">✓ User logged in (ID: <?php echo $_SESSION['member_id']; ?>)</p>
            <?php else: ?>
                <p class="error">✗ User not logged in</p>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['uploaded_file'])): ?>
                <p class="success">✓ Uploaded file: <?php echo basename($_SESSION['uploaded_file']); ?></p>
                <p>   Full path: <?php echo $_SESSION['uploaded_file']; ?></p>
                <p>   File exists: <?php echo file_exists($_SESSION['uploaded_file']) ? '<span class="success">YES</span>' : '<span class="error">NO</span>'; ?></p>
            <?php else: ?>
                <p class="warning">⚠ No uploaded file in session</p>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['log_file'])): ?>
                <p class="success">✓ Log file: <?php echo basename($_SESSION['log_file']); ?></p>
            <?php else: ?>
                <p class="warning">⚠ No log file in session</p>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['json_file'])): ?>
                <p class="success">✓ JSON file: <?php echo basename($_SESSION['json_file']); ?></p>
                <p>   File exists: <?php echo file_exists($_SESSION['json_file']) ? '<span class="success">YES</span>' : '<span class="error">NO</span>'; ?></p>
            <?php else: ?>
                <p class="warning">⚠ No JSON file in session (processing not complete)</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h3>Available Audio Files</h3>
            <?php
            $audioDir = __DIR__ . '/storage/uploads/audio/files/';
            $audioFiles = glob($audioDir . "*.{mp3,m4a,wav,aac}", GLOB_BRACE);
            
            if (count($audioFiles) > 0):
            ?>
                <p class="success">Found <?php echo count($audioFiles); ?> audio file(s):</p>
                <ul>
                <?php foreach($audioFiles as $file): ?>
                    <li>
                        <?php echo basename($file); ?> 
                        (<?php echo round(filesize($file) / 1048576, 2); ?> MB)
                        <?php if (isset($_SESSION['uploaded_file']) && $_SESSION['uploaded_file'] === $file): ?>
                            <span class="success">[CURRENT]</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="error">No audio files found in upload directory</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h3>Full Session Data</h3>
            <pre><?php print_r($_SESSION); ?></pre>
        </div>
        
        <div class="section">
            <h3>Actions</h3>
            <?php if (count($audioFiles) > 0 && !isset($_SESSION['uploaded_file'])): ?>
                <button class="fix-button" onclick="window.location.href='?action=fix'">
                    🔧 Fix Session (Use First Available File)
                </button>
            <?php endif; ?>
            
            <button onclick="window.location.href='index.php'">
                📤 Go to Upload Page
            </button>
            
            <button onclick="window.location.href='python_process.php'">
                ⚙️ Go to Processing Page
            </button>
            
            <button onclick="window.location.reload()">
                🔄 Refresh Debug Info
            </button>
            
            <?php if (isset($_SESSION['uploaded_file'])): ?>
                <button onclick="if(confirm('Clear session data?')) window.location.href='clear_session.php?redirect=fix_session.php'">
                    🗑️ Clear Session
                </button>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h3>Troubleshooting Tips</h3>
            <ol>
                <li>If "No uploaded file in session":
                    <ul>
                        <li>Upload a new file from the main page</li>
                        <li>Or use "Fix Session" button if files exist</li>
                    </ul>
                </li>
                <li>If "User not logged in":
                    <ul>
                        <li>Log in first at the main login page</li>
                    </ul>
                </li>
                <li>If processing fails:
                    <ul>
                        <li>Check that Python 3 is installed</li>
                        <li>Verify API keys are configured in .env</li>
                        <li>Check file permissions on upload directories</li>
                    </ul>
                </li>
            </ol>
        </div>
    </div>
</body>
</html>