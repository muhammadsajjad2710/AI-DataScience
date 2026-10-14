# 📊 MedicalVoice Complete Database Structure

## Database: `db2`

## 🗂️ Table Overview

The MedicalVoice system uses **29 tables** organized into the following categories:

1. **User Management**: members, user_sessions
2. **Patient Management**: patients, patient_* tables
3. **Transcription Core**: audio_transcriptions, medicalvoice_transcriptions
4. **Document Management**: patient_documents, medicalvoice_docs, transcription_documents
5. **Financial**: credit_transactions, payment_history
6. **Audit & Logging**: processing_logs, patient_access_log
7. **FHIR Compliance**: fhir_audio, fhir_documents
8. **Views**: patient_documents_view, transcription_documents_view

---

## 📋 Detailed Table Structures

### 1. **members** (User Accounts)
Primary table for user authentication and management.

| Column | Type | Key | Default | Description |
|--------|------|-----|---------|-------------|
| member_id | INT | PRI, AUTO_INCREMENT | | Unique user identifier |
| username | VARCHAR(50) | UNI | | Login username |
| email | VARCHAR(100) | UNI | | User email |
| password | VARCHAR(255) | | | Encrypted password |
| credits | INT | | 10 | Available credits |
| firstname | VARCHAR(100) | | | First name |
| lastname | VARCHAR(100) | | | Last name |
| doctorname | VARCHAR(150) | | NULL | Doctor name (if applicable) |
| phone | VARCHAR(20) | | NULL | Contact number |
| company | VARCHAR(150) | | NULL | Organization |
| status | ENUM('active','inactive','suspended') | | active | Account status |
| storage_quota_mb | INT | | 500 | Storage limit |
| storage_used_mb | DECIMAL(10,2) | | 0.00 | Storage used |
| doc_upload_enabled | TINYINT(1) | | 1 | Document upload permission |
| max_docs_per_transcription | INT | | 10 | Document limit per session |
| created_at | TIMESTAMP | | CURRENT_TIMESTAMP | Registration date |
| updated_at | TIMESTAMP | | CURRENT_TIMESTAMP | Last update |
| last_login | TIMESTAMP | | NULL | Last login time |

**Sample Data:**
```
member_id: 1, username: admin, email: admin@medicalnotes.local, credits: 1000
member_id: 2, username: testuser, email: test@medicalnotes.local, credits: 151
member_id: 3, username: test, email: test@gmail.com, credits: 160
```

---

### 2. **patients** (Patient Records)
Central patient information table with MR Number system.

| Column | Type | Key | Default | Description |
|--------|------|-----|---------|-------------|
| patient_id | INT | PRI, AUTO_INCREMENT | | Unique patient ID |
| mr_number | VARCHAR(50) | UNI | | Medical Record Number |
| member_id | INT | FK→members | | Owner/creator |
| first_name | VARCHAR(100) | | | Patient first name |
| last_name | VARCHAR(100) | | | Patient last name |
| date_of_birth | DATE | | NULL | DOB |
| gender | ENUM('Male','Female','Other') | | NULL | Gender |
| phone | VARCHAR(20) | | NULL | Contact |
| email | VARCHAR(100) | | NULL | Email |
| address | TEXT | | NULL | Address |
| blood_type | VARCHAR(10) | | NULL | Blood type |
| allergies | TEXT | | NULL | Known allergies |
| medical_conditions | TEXT | | NULL | Medical conditions |
| emergency_contact | VARCHAR(255) | | NULL | Emergency contact |
| is_active | BOOLEAN | | TRUE | Active status |
| created_at | TIMESTAMP | | CURRENT_TIMESTAMP | Created date |
| updated_at | TIMESTAMP | | CURRENT_TIMESTAMP | Last update |

**Sample Data:**
```
patient_id: 1, mr_number: MRN-2025-0076, first_name: test, last_name: user, gender: Male
patient_id: 2, mr_number: MRN-2025-8652, first_name: xyz, last_name: dfjs, gender: Female
```

---

### 3. **audio_transcriptions** (Main Transcription Table)
Core table for audio processing with patient linking.

| Column | Type | Key | Default | Description |
|--------|------|-----|---------|-------------|
| id | INT | PRI, AUTO_INCREMENT | | Transcription ID |
| member_id | INT | FK→members | | User who uploaded |
| patient_id | INT | FK→patients | NULL | Linked patient |
| mr_number | VARCHAR(50) | | NULL | Direct MR reference |
| audio_file_path | VARCHAR(500) | | | Path to audio file |
| transcribed_text | TEXT | | NULL | Transcription result |
| medical_analysis | JSON | | NULL | AI analysis |
| json_file_path | VARCHAR(500) | | NULL | JSON output path |
| log_file_path | VARCHAR(500) | | NULL | Processing log |
| file_size | INT | | 0 | File size in bytes |
| duration_seconds | INT | | NULL | Audio duration |
| processing_status | ENUM('pending','processing','completed','failed') | | pending | Status |
| error_message | TEXT | | NULL | Error details |
| created_at | TIMESTAMP | | CURRENT_TIMESTAMP | Upload time |
| processed_at | TIMESTAMP | | NULL | Processing time |
| credits_used | INT | | 1 | Credits consumed |

**Sample Data:**
```
id: 1, member_id: 2, patient_id: NULL, audio_file_path: storage/uploads/audio/files/audio1.mp3
id: 2, member_id: 2, patient_id: 2, mr_number: MRN-2025-8652, status: completed
```

---

### 4. **medicalvoice_transcriptions** (Legacy Transcription Table)
Original transcription storage table (53 records).

| Column | Type | Key | Default | Description |
|--------|------|-----|---------|-------------|
| transcription_id | INT | PRI, AUTO_INCREMENT | | Unique ID |
| member_id | INT | FK→members | | User ID |
| audio_path | VARCHAR(255) | | | Audio file location |
| text | TEXT | | NULL | Transcribed text |
| json_path | VARCHAR(255) | | NULL | JSON file path |
| log_path | VARCHAR(255) | | NULL | Log file path |
| created_at | TIMESTAMP | | CURRENT_TIMESTAMP | Creation time |
| updated_at | TIMESTAMP | | CURRENT_TIMESTAMP | Last update |

---

### 5. **patient_documents** (Document Storage)
Stores all patient-related documents with categorization.

| Column | Type | Key | Default | Description |
|--------|------|-----|---------|-------------|
| document_id | INT | PRI, AUTO_INCREMENT | | Document ID |
| patient_id | INT | FK→patients | | Patient reference |
| mr_number | VARCHAR(50) | | | MR Number |
| member_id | INT | FK→members | | Uploader |
| transcription_id | INT | FK→audio_transcriptions | NULL | Related audio |
| document_type | ENUM('lab_report','prescription','imaging','consultation','discharge_summary','other') | | other | Type |
| document_name | VARCHAR(255) | | | Display name |
| original_filename | VARCHAR(255) | | | Original file |
| file_path | VARCHAR(500) | | | Storage path |
| file_size | INT | | 0 | Size in bytes |
| mime_type | VARCHAR(100) | | NULL | MIME type |
| extracted_text | TEXT | | NULL | OCR text |
| metadata | JSON | | NULL | Additional data |
| upload_date | TIMESTAMP | | CURRENT_TIMESTAMP | Upload time |
| is_archived | BOOLEAN | | FALSE | Archive status |

---

### 6. **patient_reports** (Generated Reports)
AI-generated medical reports and summaries.

| Column | Type | Key | Default | Description |
|--------|------|-----|---------|-------------|
| report_id | INT | PRI, AUTO_INCREMENT | | Report ID |
| patient_id | INT | FK→patients | | Patient |
| mr_number | VARCHAR(50) | | | MR Number |
| member_id | INT | FK→members | | Creator |
| report_type | ENUM('transcription_summary','medical_summary','discharge','consultation') | | | Type |
| report_title | VARCHAR(255) | | | Title |
| report_content | JSON | | NULL | Structured content |
| report_text | TEXT | | NULL | Plain text |
| fhir_resource | JSON | | NULL | FHIR format |
| generated_by | VARCHAR(100) | | system | Generator |
| is_finalized | BOOLEAN | | FALSE | Final status |
| created_at | TIMESTAMP | | CURRENT_TIMESTAMP | Creation time |
| finalized_at | TIMESTAMP | | NULL | Finalization time |

---

### 7. **credit_transactions** (Credit Usage History)
Tracks all credit transactions for billing.

| Column | Type | Key | Default | Description |
|--------|------|-----|---------|-------------|
| transaction_id | INT | PRI, AUTO_INCREMENT | | Transaction ID |
| member_id | INT | FK→members | | User |
| transaction_type | ENUM('purchase','usage','refund','bonus') | | | Type |
| amount | INT | | | Credit amount |
| balance_after | INT | | | New balance |
| description | VARCHAR(255) | | NULL | Description |
| reference_id | VARCHAR(100) | | NULL | External reference |
| created_at | TIMESTAMP | | CURRENT_TIMESTAMP | Transaction time |

---

### 8. **processing_logs** (System Activity Logs)
Detailed logging of all processing activities.

| Column | Type | Key | Default | Description |
|--------|------|-----|---------|-------------|
| log_id | INT | PRI, AUTO_INCREMENT | | Log ID |
| member_id | INT | FK→members | | User |
| module_type | ENUM('medicalvoice','medicalvision') | | | Module |
| action | VARCHAR(100) | | | Action performed |
| status | ENUM('success','failure','processing') | | | Status |
| message | TEXT | | NULL | Log message |
| metadata | JSON | | NULL | Additional data |
| ip_address | VARCHAR(45) | | NULL | User IP |
| user_agent | VARCHAR(255) | | NULL | Browser info |
| created_at | TIMESTAMP | | CURRENT_TIMESTAMP | Log time |

**Sample Data:**
```
log_id: 1, member_id: 2, module: medicalvoice, action: upload_audio, status: success
log_id: 2, member_id: 2, module: medicalvoice, action: transcription, status: processing
```

---

### 9. **patient_access_log** (HIPAA Audit Trail)
Tracks all access to patient data for compliance.

| Column | Type | Key | Default | Description |
|--------|------|-----|---------|-------------|
| access_id | INT | PRI, AUTO_INCREMENT | | Access ID |
| patient_id | INT | FK→patients | | Patient accessed |
| mr_number | VARCHAR(50) | | | MR Number |
| member_id | INT | FK→members | | User accessing |
| action | VARCHAR(100) | | | Action type |
| resource_type | VARCHAR(50) | | NULL | Resource accessed |
| resource_id | INT | | NULL | Resource ID |
| ip_address | VARCHAR(45) | | NULL | Access IP |
| access_time | TIMESTAMP | | CURRENT_TIMESTAMP | Access time |

**Sample Data:**
```
access_id: 1, patient_id: 1, action: view_transcription, member_id: 2
access_id: 2, patient_id: 2, action: create_patient, member_id: 2
```

---

### 10. **transcription_documents** (Document Associations)
Links documents to transcription sessions.

| Column | Type | Key | Default | Description |
|--------|------|-----|---------|-------------|
| id | INT | PRI, AUTO_INCREMENT | | Link ID |
| transcription_id | INT | FK→audio_transcriptions | | Transcription |
| document_name | VARCHAR(255) | | | Document name |
| file_path | VARCHAR(500) | | | File location |
| file_type | VARCHAR(50) | | | Type |
| file_size | INT | | 0 | Size |
| uploaded_at | TIMESTAMP | | CURRENT_TIMESTAMP | Upload time |

---

## 🔗 Table Relationships

### Primary Relationships:

```
members (1) ──┬──> (∞) patients
              ├──> (∞) audio_transcriptions
              ├──> (∞) medicalvoice_transcriptions
              ├──> (∞) patient_documents
              ├──> (∞) patient_reports
              ├──> (∞) credit_transactions
              └──> (∞) processing_logs

patients (1) ──┬──> (∞) audio_transcriptions
               ├──> (∞) patient_documents
               ├──> (∞) patient_reports
               ├──> (∞) patient_medical_history
               ├──> (∞) patient_visits
               └──> (∞) patient_access_log

audio_transcriptions (1) ──┬──> (∞) transcription_documents
                           └──> (∞) patient_documents
```

### Foreign Key Constraints:

1. **patients.member_id** → members.member_id
2. **audio_transcriptions.member_id** → members.member_id
3. **audio_transcriptions.patient_id** → patients.patient_id
4. **patient_documents.patient_id** → patients.patient_id
5. **patient_documents.member_id** → members.member_id
6. **patient_documents.transcription_id** → audio_transcriptions.id
7. **patient_reports.patient_id** → patients.patient_id
8. **patient_reports.member_id** → members.member_id
9. **credit_transactions.member_id** → members.member_id
10. **processing_logs.member_id** → members.member_id
11. **patient_access_log.patient_id** → patients.patient_id
12. **patient_access_log.member_id** → members.member_id
13. **transcription_documents.transcription_id** → audio_transcriptions.id

---

## 📊 Database Statistics

| Table | Records | Primary Use |
|-------|---------|-------------|
| members | 6 | User accounts |
| patients | 2 | Patient records |
| audio_transcriptions | 2 | New transcriptions |
| medicalvoice_transcriptions | 53 | Legacy transcriptions |
| patient_documents | 0 | Document storage (ready) |
| patient_reports | 0 | Generated reports (ready) |
| patient_medical_history | 0 | Medical history (ready) |
| patient_visits | 0 | Visit records (ready) |
| patient_access_log | 8 | Audit trail |
| credit_transactions | 0 | Credit history |
| processing_logs | 20 | System logs |
| transcription_documents | 15 | Document links |

---

## 🔐 Security Features

1. **HIPAA Compliance**:
   - patient_access_log tracks all data access
   - Encrypted password storage
   - Session-based authentication
   - IP tracking for audits

2. **Data Integrity**:
   - Foreign key constraints ensure referential integrity
   - ENUM types for controlled values
   - NOT NULL constraints on critical fields
   - Unique constraints on identifiers

3. **Credit System**:
   - Atomic transactions for credit deduction
   - Transaction history in credit_transactions
   - Default 10 credits for new users

---

## 💡 Key Design Patterns

1. **MR Number System**: Unique patient identification across systems
2. **Dual Linking**: Both patient_id and mr_number for flexibility
3. **JSON Storage**: For medical_analysis, metadata, and FHIR resources
4. **Audit Trail**: Complete tracking of patient data access
5. **Soft Deletes**: is_active/is_archived flags instead of deletion
6. **Timestamps**: created_at/updated_at on all tables

---

## 🚀 Usage Examples

### Get Patient with All Transcriptions:
```sql
SELECT p.*, COUNT(at.id) as transcription_count
FROM patients p
LEFT JOIN audio_transcriptions at ON p.patient_id = at.patient_id
WHERE p.mr_number = 'MRN-2025-0076'
GROUP BY p.patient_id;
```

### Check User Credits and Usage:
```sql
SELECT m.username, m.credits, COUNT(at.id) as total_transcriptions
FROM members m
LEFT JOIN audio_transcriptions at ON m.member_id = at.member_id
WHERE m.member_id = 2
GROUP BY m.member_id;
```

### Audit Trail Query:
```sql
SELECT pal.*, m.username, p.mr_number
FROM patient_access_log pal
JOIN members m ON pal.member_id = m.member_id
JOIN patients p ON pal.patient_id = p.patient_id
WHERE pal.patient_id = 1
ORDER BY pal.access_time DESC;
```

---

## 📝 Notes

- The database uses InnoDB engine for transaction support
- UTF8MB4 charset for full Unicode support including emojis
- Indexes on foreign keys for query optimization
- Views (patient_documents_view, transcription_documents_view) for simplified queries
- Ready for horizontal scaling with proper indexing