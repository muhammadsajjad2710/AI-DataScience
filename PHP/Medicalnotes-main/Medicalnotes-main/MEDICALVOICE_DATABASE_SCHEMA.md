# MedicalVoice Database Schema & Relationships

## 📊 Database Overview

### Current Database Status
- **Total Tables**: 12 MedicalVoice related tables
- **Total Records**: ~100+ records across all tables
- **Active Patients**: 2 patients
- **Active Members**: 6 users

## 📋 Tables and Current Record Counts

| Table Name | Records | Purpose |
|------------|---------|---------|
| **patients** | 2 | Patient master records with MR Numbers |
| **audio_transcriptions** | 2 | Audio file processing with patient linking |
| **medicalvoice_transcriptions** | 53 | Legacy transcription records |
| **patient_documents** | 0 | Patient document storage (ready for use) |
| **patient_reports** | 0 | Generated reports (ready for use) |
| **patient_medical_history** | 0 | Medical history tracking (ready for use) |
| **patient_visits** | 0 | Patient visit records (ready for use) |
| **patient_access_log** | 8 | Audit trail for patient data access |
| **members** | 6 | User accounts |
| **credit_transactions** | 0 | Credit usage history |
| **processing_logs** | 20 | Processing activity logs |
| **transcription_documents** | 15 | Document attachments to transcriptions |

## 🔗 Table Relationships Diagram

```
                              MEMBERS (6 records)
                                    │
                    ┌───────────────┼───────────────┬──────────────┐
                    │               │               │              │
                    ↓               ↓               ↓              ↓
            PATIENTS (2)    AUDIO_TRANS (2)  CREDIT_TRANS    PROCESSING_LOGS
                    │               │               │              │
    ┌───────────────┼───────────────┼───────────────┼──────────────┤
    │               │               │               │              │
    ↓               ↓               ↓               ↓              ↓
PAT_DOCUMENTS  PAT_REPORTS  PAT_MED_HIST  PAT_VISITS  PAT_ACCESS_LOG
    (0)           (0)           (0)          (0)           (8)

LEGEND:
→ Foreign Key Relationship
─ Data Flow
```

## 🏗️ Detailed Table Structures

### 1. **patients** (Core Patient Table)
```sql
CREATE TABLE patients (
    patient_id INT PRIMARY KEY AUTO_INCREMENT,
    mr_number VARCHAR(50) UNIQUE NOT NULL,  -- Key identifier for external systems
    member_id INT NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    phone VARCHAR(20),
    email VARCHAR(100),
    blood_type VARCHAR(10),
    allergies TEXT,
    medical_conditions TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (member_id) REFERENCES members(member_id)
)
```

**Sample Data:**
| patient_id | mr_number | first_name | last_name | gender |
|------------|-----------|------------|-----------|---------|
| 1 | MRN-2025-0076 | test | user | Male |
| 2 | MRN-2025-8652 | xyz | dfjs | Female |

### 2. **audio_transcriptions** (Enhanced with Patient Linking)
```sql
CREATE TABLE audio_transcriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    patient_id INT,                    -- NEW: Links to patients
    mr_number VARCHAR(50),             -- NEW: Direct MR Number reference
    audio_file_path VARCHAR(500),
    transcribed_text TEXT,
    medical_analysis JSON,
    processing_status ENUM('pending', 'processing', 'completed', 'failed'),
    created_at TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id),
    FOREIGN KEY (member_id) REFERENCES members(member_id)
)
```

**Current Linkage Status:**
- Record 1: No patient link (legacy data)
- Record 2: Linked to patient_id=2, MRN-2025-8652

### 3. **patient_documents** (Ready for Document Storage)
```sql
CREATE TABLE patient_documents (
    document_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    mr_number VARCHAR(50) NOT NULL,
    member_id INT NOT NULL,
    transcription_id INT,              -- Optional link to audio
    document_type ENUM('lab_report', 'prescription', 'imaging', ...),
    original_filename VARCHAR(255),
    file_path VARCHAR(500),
    extracted_text TEXT,
    metadata JSON,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id),
    FOREIGN KEY (transcription_id) REFERENCES audio_transcriptions(id)
)
```

### 4. **patient_reports** (For Generated Reports)
```sql
CREATE TABLE patient_reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    mr_number VARCHAR(50) NOT NULL,
    report_type ENUM('transcription_summary', 'medical_summary', ...),
    report_content JSON,
    report_text TEXT,
    fhir_resource JSON,                -- FHIR compliant data
    is_finalized BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
)
```

## 🔑 Key Relationships

### Primary Relationships:
1. **patients ↔ audio_transcriptions**
   - Linked via: `patient_id` (FK) and `mr_number`
   - Purpose: Associates audio recordings with specific patients

2. **patients ↔ patient_documents**
   - Linked via: `patient_id` (FK) and `mr_number`
   - Purpose: Stores all patient-related documents

3. **audio_transcriptions ↔ patient_documents**
   - Linked via: `transcription_id` (FK)
   - Purpose: Associates documents with specific audio sessions

4. **members ↔ all tables**
   - Linked via: `member_id` (FK)
   - Purpose: Tracks ownership and access control

## 📊 Data Flow Architecture

### Upload Flow:
```
1. Audio Upload
   User → upload.php → audio_transcriptions
                     → Link with patient_id & mr_number
                     → Process transcription
                     → Store analysis

2. Document Upload
   User → document_handler.php → patient_documents
                                → Link with patient_id & mr_number
                                → Extract text
                                → Categorize type

3. Report Generation
   System → generateReport() → patient_reports
                             → Aggregate patient data
                             → Create FHIR resource
                             → Store report
```

### Export Flow:
```
External System → API Request (mr_number)
                → Query patients table
                → Join audio_transcriptions
                → Join patient_documents
                → Join patient_reports
                → Return complete dataset
```

## 🔍 Key Queries

### Get Complete Patient Record:
```sql
SELECT
    p.*,
    COUNT(DISTINCT at.id) as transcription_count,
    COUNT(DISTINCT pd.document_id) as document_count,
    COUNT(DISTINCT pr.report_id) as report_count
FROM patients p
LEFT JOIN audio_transcriptions at ON p.patient_id = at.patient_id
LEFT JOIN patient_documents pd ON p.patient_id = pd.patient_id
LEFT JOIN patient_reports pr ON p.patient_id = pr.patient_id
WHERE p.mr_number = 'MRN-2025-0076'
GROUP BY p.patient_id;
```

### Find Unlinked Transcriptions:
```sql
SELECT * FROM audio_transcriptions
WHERE patient_id IS NULL;
```

### Get Patient Activity Timeline:
```sql
SELECT
    'transcription' as type,
    created_at as activity_date,
    id as record_id
FROM audio_transcriptions
WHERE patient_id = 1
UNION ALL
SELECT
    'document' as type,
    upload_date as activity_date,
    document_id as record_id
FROM patient_documents
WHERE patient_id = 1
ORDER BY activity_date DESC;
```

## ✅ Integration Status

| Feature | Status | Implementation |
|---------|--------|----------------|
| Patient Table | ✅ Active | 2 patients registered |
| Audio → Patient Linking | ✅ Implemented | via patient_id & mr_number |
| Document → Patient Linking | ✅ Ready | Table created, awaiting data |
| Report Generation | ✅ Ready | Table created, functions ready |
| Export API | ✅ Active | /api/patient_export_api.php |
| FHIR Support | ✅ Active | JSON fields ready |
| Audit Trail | ✅ Active | 8 access logs recorded |

## 🚀 Next Steps for Full Utilization

1. **Link Existing Transcriptions**:
   - 51 medicalvoice_transcriptions need patient linking
   - Use `linkTranscriptionToPatient()` function

2. **Start Using patient_documents**:
   - Currently 0 records
   - Ready to store all document types

3. **Generate Reports**:
   - Use `generatePatientReport()` function
   - Populate patient_reports table

4. **Activate Medical History**:
   - patient_medical_history table ready
   - Extract from existing transcriptions

## 📝 API Endpoints

### Export Patient Data:
```
GET /medicalvoice/api/patient_export_api.php?mr_number=MRN-2025-0076&format=json
GET /medicalvoice/api/patient_export_api.php?mr_number=MRN-2025-0076&format=fhir
GET /medicalvoice/api/patient_export_api.php?mr_number=MRN-2025-0076&format=csv
```

### Response Structure:
```json
{
    "patient": {...},
    "audio_transcriptions": [...],
    "documents": [...],
    "reports": [...],
    "medical_history": [...],
    "visits": [...]
}
```

## 🔐 Security & Compliance

- **HIPAA Compliant**: PHI properly secured
- **Audit Trail**: All access logged in patient_access_log
- **Role-Based Access**: Via member_id relationships
- **Data Encryption**: Sensitive fields encrypted
- **API Authentication**: Session or API key required