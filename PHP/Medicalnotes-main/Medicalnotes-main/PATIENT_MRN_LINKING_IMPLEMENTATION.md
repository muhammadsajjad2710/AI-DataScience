# Patient MRN Linking Implementation Guide

## Current State Analysis

Based on my analysis of your MedicalVoice system, here's the current state of patient data relationships:

### Existing Tables
1. **patients** - Contains patient records with MR Number (mr_number)
2. **audio_transcriptions** - Audio processing results (currently NOT linked to patients)
3. **transcriptions** - Has patient_id column (linked via foreign key)
4. **transcription_documents** - Document uploads (NOT directly linked to patients)

## Problem Identified

The main issue is that **audio_transcriptions** table and document-related tables are not properly linked to the **patients** table via MR Number. This prevents you from:
- Associating audio transcriptions with specific patients
- Linking uploaded documents to patient records
- Generating comprehensive patient reports
- Exporting complete patient data to external systems

## Solution Architecture

I've created a comprehensive solution that establishes proper MRN linking across all tables. Here's what it includes:

### 1. Enhanced Table Structure

#### Modified Tables:
- **audio_transcriptions** - Added patient_id and mr_number columns
- **transcriptions** - Added mr_number and visit_id columns

#### New Tables Created:
- **patient_documents** - Centralized document storage linked to patients
- **patient_reports** - Generated reports with patient association
- **patient_medical_history** - Comprehensive medical history tracking
- **patient_visits** - Patient encounter tracking

### 2. Key Features of the Solution

#### A. Direct MRN Linking
Every table now includes both:
- `patient_id` (foreign key reference)
- `mr_number` (indexed for fast lookups)

This dual approach ensures:
- Referential integrity through foreign keys
- Fast queries using MR Number
- Easy data export using MR Number as the universal identifier

#### B. Document Management
The new `patient_documents` table provides:
- Direct patient association
- Document type classification (lab reports, prescriptions, etc.)
- Text extraction storage
- Link to related transcriptions

#### C. Report Generation
The `patient_reports` table enables:
- Multiple report types (summary, diagnostic, comprehensive)
- FHIR-compliant storage
- Digital signatures for finalized reports
- Links to source transcriptions and documents

#### D. Medical History Tracking
Comprehensive history management with:
- ICD-10/11 code support
- SNOMED CT code support
- Source tracking (which transcription/document)
- Status tracking (active, resolved, chronic)

### 3. Implementation Steps

#### Step 1: Backup Your Database
```bash
mysqldump -u root -p db2 > db2_backup_$(date +%Y%m%d).sql
```

#### Step 2: Run the SQL Migration
```bash
mysql -u root -p db2 < /var/www/html/medicalnotes/medicalvoice/sql/patient_mrn_linking_solution.sql
```

#### Step 3: Update PHP Code

##### In audio transcription upload (upload.php):
```php
// When saving audio transcription with patient
if (isset($_SESSION['selected_patient_id'])) {
    $patientId = $_SESSION['selected_patient_id'];

    // Get MR Number
    $stmt = $conn->prepare("SELECT mr_number FROM patients WHERE patient_id = ?");
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    $mrNumber = $patient['mr_number'];

    // Save transcription with patient info
    $stmt = $conn->prepare("INSERT INTO audio_transcriptions
        (member_id, patient_id, mr_number, audio_file_path, transcribed_text, medical_analysis, json_file_path, log_file_path, file_size, processing_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed')");

    $stmt->bind_param("iissssssii", $memberId, $patientId, $mrNumber, ...);
}
```

##### For document uploads (document_handler.php):
```php
// Save document with patient association
if (isset($_SESSION['selected_patient_id'])) {
    $patientId = $_SESSION['selected_patient_id'];
    $mrNumber = getPatientMRNumber($conn, $patientId);

    $stmt = $conn->prepare("INSERT INTO patient_documents
        (patient_id, mr_number, member_id, transcription_id, document_type, original_filename, stored_filename, file_path, file_type, file_size, extracted_text)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("isiisssssis", $patientId, $mrNumber, ...);
}
```

### 4. Data Export Functions

#### Export Complete Patient Record
```php
function exportPatientRecord($conn, $mrNumber) {
    $exportData = [];

    // Get patient info
    $stmt = $conn->prepare("SELECT * FROM patients WHERE mr_number = ?");
    $stmt->bind_param("s", $mrNumber);
    $stmt->execute();
    $exportData['patient'] = $stmt->get_result()->fetch_assoc();

    // Get all transcriptions
    $stmt = $conn->prepare("SELECT * FROM audio_transcriptions WHERE mr_number = ?");
    $stmt->bind_param("s", $mrNumber);
    $stmt->execute();
    $exportData['transcriptions'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get all documents
    $stmt = $conn->prepare("SELECT * FROM patient_documents WHERE mr_number = ?");
    $stmt->bind_param("s", $mrNumber);
    $stmt->execute();
    $exportData['documents'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get all reports
    $stmt = $conn->prepare("SELECT * FROM patient_reports WHERE mr_number = ?");
    $stmt->bind_param("s", $mrNumber);
    $stmt->execute();
    $exportData['reports'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    return $exportData;
}
```

### 5. Benefits of This Implementation

1. **Complete Data Linkage**: All patient data is connected via MR Number
2. **Easy Export**: Use MR Number to extract all related records
3. **Audit Trail**: Track all patient data access and modifications
4. **FHIR Ready**: Structure supports FHIR resource generation
5. **Performance**: Indexed columns ensure fast queries
6. **Referential Integrity**: Foreign keys maintain data consistency

### 6. Testing the Implementation

After implementing, test with:

```sql
-- Verify patient linkage
SELECT
    p.mr_number,
    p.first_name,
    p.last_name,
    COUNT(DISTINCT at.id) as audio_count,
    COUNT(DISTINCT pd.document_id) as document_count,
    COUNT(DISTINCT pr.report_id) as report_count
FROM patients p
LEFT JOIN audio_transcriptions at ON p.patient_id = at.patient_id
LEFT JOIN patient_documents pd ON p.patient_id = pd.patient_id
LEFT JOIN patient_reports pr ON p.patient_id = pr.patient_id
GROUP BY p.patient_id;
```

### 7. Next Steps

1. **Immediate Actions**:
   - Backup your database
   - Run the migration SQL
   - Update PHP upload handlers

2. **Short Term**:
   - Update existing records to link with patients
   - Test data export functionality
   - Create API endpoints for external system integration

3. **Long Term**:
   - Implement automated report generation
   - Add HL7/FHIR export capabilities
   - Build patient portal for record access

## Conclusion

This solution provides a robust foundation for linking all patient-related data through the MR Number. It ensures data integrity, enables comprehensive reporting, and facilitates easy data exchange with external healthcare systems.