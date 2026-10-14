<?php
/**
 * Setup FHIR Documents Table for MedicalVision
 * Run this script to create the required table
 */

require_once 'config.php';

echo "<h1>🏥 Setting up FHIR Documents Table for MedicalVision</h1>";

try {
    $pdo = getDatabaseConnection();
    
    if (!$pdo) {
        echo "<p style='color: red;'>❌ Database connection failed!</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Create fhir_documents table
    $sql = "CREATE TABLE IF NOT EXISTS fhir_documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        member_id INT NOT NULL,
        image_path VARCHAR(500) NOT NULL,
        extracted_text TEXT,
        fhir_json LONGTEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (member_id) REFERENCES members(member_id)
    )";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ FHIR documents table created successfully!</p>";
    
    // Add indexes for better performance
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_fhir_documents_member_id ON fhir_documents(member_id)",
        "CREATE INDEX IF NOT EXISTS idx_fhir_documents_image_path ON fhir_documents(image_path)",
        "CREATE INDEX IF NOT EXISTS idx_fhir_documents_created_at ON fhir_documents(created_at)"
    ];
    
    foreach ($indexes as $indexSql) {
        try {
            $pdo->exec($indexSql);
            echo "<p style='color: green;'>✅ Index created successfully!</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Index creation warning: " . $e->getMessage() . "</p>";
        }
    }
    
    // Check if table exists and has correct structure
    $stmt = $pdo->query("DESCRIBE fhir_documents");
    $columns = $stmt->fetchAll();
    
    echo "<h3>📋 Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>🎉 FHIR Documents table setup completed successfully!</h2>";
    echo "<p>MedicalVision can now store OCR results and AI analysis data.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
