# Patient Table Relationships in MedicalVoice

## 🏥 The Patient Table - Central Hub

The **`patients`** table is the **CENTRAL HUB** of the MedicalVoice system. Every piece of medical data ultimately connects back to a patient record.

## 📋 Patient Table Structure

```sql
CREATE TABLE patients (
    patient_id INT PRIMARY KEY AUTO_INCREMENT,  -- Primary identifier
    mr_number VARCHAR(50) UNIQUE NOT NULL,      -- Medical Record Number (KEY for external systems)
    member_id INT NOT NULL,                     -- Which user created this patient
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    emergency_contact VARCHAR(100),
    emergency_phone VARCHAR(20),
    blood_type VARCHAR(10),
    allergies TEXT,
    medical_conditions TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (member_id) REFERENCES members(member_id)
)
```

## 🔗 How Patient Table Links to Other Tables

### Two Key Linking Methods:
1. **patient_id** - Database foreign key (ensures referential integrity)
2. **mr_number** - Medical Record Number (universal identifier for exports)

## 📊 Complete Relationship Map

```
                            PATIENTS TABLE
                    ┌────────────────────────────┐
                    │  patient_id (PK)           │
                    │  mr_number (UNIQUE)        │
                    │  member_id (FK)            │
                    └────────────────────────────┘
                                │
                    ┌───────────┼───────────┐
                    │           │           │
              [ONE-TO-MANY] [ONE-TO-MANY] [ONE-TO-MANY]
                    │           │           │
                    ↓           ↓           ↓
        ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
        │ AUDIO_       │ │ PATIENT_     │ │ PATIENT_     │
        │ TRANSCRIPTIONS│ │ DOCUMENTS    │ │ REPORTS      │
        └──────────────┘ └──────────────┘ └──────────────┘
                    │           │           │
                    ↓           ↓           ↓
        ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
        │ PATIENT_     │ │ PATIENT_     │ │ PATIENT_     │
        │ VISITS       │ │ MEDICAL_     │ │ ACCESS_LOG   │
        │              │ │ HISTORY      │ │              │
        └──────────────┘ └──────────────┘ └──────────────┘
```

## 🔍 Detailed Relationship Explanations

### 1. **patients → audio_transcriptions** (One-to-Many)

**Relationship Type**: One patient can have MANY audio transcriptions

**Linking Fields**:
- `audio_transcriptions.patient_id` → `patients.patient_id` (Foreign Key)
- `audio_transcriptions.mr_number` = `patients.mr_number` (Redundant for fast lookups)

**Purpose**: Links all audio recordings/transcriptions to the specific patient

**Example**:
```sql
-- Get all audio transcriptions for patient John Doe
SELECT at.*
FROM audio_transcriptions at
JOIN patients p ON at.patient_id = p.patient_id
WHERE p.mr_number = 'MRN-2025-0076';
```

**Real Data Example**:
- Patient "xyz dfjs" (MRN-2025-8652) has 1 audio transcription
- When audio is uploaded with patient_id=2, it automatically links to this patient

---

### 2. **patients → patient_documents** (One-to-Many)

**Relationship Type**: One patient can have MANY documents

**Linking Fields**:
- `patient_documents.patient_id` → `patients.patient_id` (Foreign Key)
- `patient_documents.mr_number` = `patients.mr_number`

**Purpose**: Stores all patient-related documents (lab reports, prescriptions, imaging, etc.)

**Document Types**:
- lab_report
- prescription
- imaging
- clinical_note
- discharge_summary
- consultation
- other

**Example**:
```sql
-- Get all lab reports for a patient
SELECT * FROM patient_documents
WHERE patient_id = 1
AND document_type = 'lab_report';
```

---

### 3. **patients → patient_reports** (One-to-Many)

**Relationship Type**: One patient can have MANY generated reports

**Linking Fields**:
- `patient_reports.patient_id` → `patients.patient_id` (Foreign Key)
- `patient_reports.mr_number` = `patients.mr_number`

**Purpose**: Stores generated medical reports and summaries

**Report Types**:
- transcription_summary
- medical_summary
- visit_report
- diagnostic_report
- comprehensive_report

**Example**:
```sql
-- Generate and link a report to patient
INSERT INTO patient_reports (patient_id, mr_number, report_type, report_content)
SELECT patient_id, mr_number, 'medical_summary', '{"summary": "..."}'
FROM patients WHERE mr_number = 'MRN-2025-0076';
```

---

### 4. **patients → patient_visits** (One-to-Many)

**Relationship Type**: One patient can have MANY visits

**Linking Fields**:
- `patient_visits.patient_id` → `patients.patient_id` (Foreign Key)
- `patient_visits.mr_number` = `patients.mr_number`

**Purpose**: Tracks all patient encounters/visits

**Visit Types**:
- consultation
- follow_up
- emergency
- routine_checkup
- procedure
- telemedicine

**Example**:
```sql
-- Record a new patient visit
INSERT INTO patient_visits (patient_id, mr_number, visit_date, visit_type, chief_complaint)
VALUES (1, 'MRN-2025-0076', NOW(), 'consultation', 'Annual checkup');
```

---

### 5. **patients → patient_medical_history** (One-to-Many)

**Relationship Type**: One patient can have MANY medical history entries

**Linking Fields**:
- `patient_medical_history.patient_id` → `patients.patient_id` (Foreign Key)
- `patient_medical_history.mr_number` = `patients.mr_number`

**Purpose**: Maintains comprehensive medical history

**History Types**:
- medication
- allergy
- diagnosis
- procedure
- immunization
- family_history
- social_history

**Example**:
```sql
-- Add allergy to patient history
INSERT INTO patient_medical_history
(patient_id, mr_number, history_type, description, severity, status)
VALUES (1, 'MRN-2025-0076', 'allergy', 'Penicillin', 'severe', 'active');
```

---

### 6. **patients → patient_access_log** (One-to-Many)

**Relationship Type**: One patient can have MANY access log entries

**Linking Fields**:
- `patient_access_log.patient_id` → `patients.patient_id` (Foreign Key)

**Purpose**: Audit trail - tracks who accessed patient data and when

**Access Types**:
- view
- create
- update
- delete

**Example**:
```sql
-- Log when someone views patient record
INSERT INTO patient_access_log (patient_id, member_id, access_type)
VALUES (1, 2, 'view');
```

---

### 7. **members → patients** (One-to-Many)

**Relationship Type**: One member (user) can create MANY patients

**Linking Field**:
- `patients.member_id` → `members.member_id` (Foreign Key)

**Purpose**: Tracks which healthcare provider created/owns the patient record

**Example**:
```sql
-- Get all patients created by a specific user
SELECT * FROM patients
WHERE member_id = 2;
```

## 🔄 Complex Relationships

### Document-Transcription-Patient Triangle

```
PATIENT ←→ AUDIO_TRANSCRIPTION ←→ PATIENT_DOCUMENT
   ↑                                      ↑
   └──────────────────────────────────────┘
```

A document can be:
1. Directly linked to a patient
2. Linked to a transcription (which is linked to a patient)
3. Both

**Example**:
```sql
-- Get all documents for a patient (both direct and via transcriptions)
SELECT pd.* FROM patient_documents pd
WHERE pd.patient_id = 1
UNION
SELECT pd.* FROM patient_documents pd
JOIN audio_transcriptions at ON pd.transcription_id = at.id
WHERE at.patient_id = 1;
```

## 📊 Relationship Cardinality Summary

| Parent Table | Child Table | Relationship | Cardinality | Link Method |
|--------------|-------------|--------------|-------------|-------------|
| patients | audio_transcriptions | Has many | 1:N | patient_id, mr_number |
| patients | patient_documents | Has many | 1:N | patient_id, mr_number |
| patients | patient_reports | Has many | 1:N | patient_id, mr_number |
| patients | patient_visits | Has many | 1:N | patient_id, mr_number |
| patients | patient_medical_history | Has many | 1:N | patient_id, mr_number |
| patients | patient_access_log | Has many | 1:N | patient_id |
| members | patients | Creates | 1:N | member_id |
| audio_transcriptions | patient_documents | Has many | 1:N | transcription_id |

## 🎯 Why This Design?

### 1. **Data Integrity**
- Foreign keys ensure you can't have orphaned records
- Can't delete a patient if they have transcriptions

### 2. **Performance**
- Indexed on both patient_id and mr_number
- Fast lookups by either field

### 3. **Flexibility**
- MR Number allows external system integration
- Patient ID maintains internal relationships

### 4. **Audit & Compliance**
- Every access is logged
- Complete history tracking
- HIPAA compliance ready

## 💡 Practical Usage Examples

### Example 1: Getting Complete Patient Data
```sql
SELECT
    p.*,
    COUNT(DISTINCT at.id) as total_transcriptions,
    COUNT(DISTINCT pd.document_id) as total_documents,
    COUNT(DISTINCT pr.report_id) as total_reports,
    COUNT(DISTINCT pv.visit_id) as total_visits
FROM patients p
LEFT JOIN audio_transcriptions at ON p.patient_id = at.patient_id
LEFT JOIN patient_documents pd ON p.patient_id = pd.patient_id
LEFT JOIN patient_reports pr ON p.patient_id = pr.patient_id
LEFT JOIN patient_visits pv ON p.patient_id = pv.patient_id
WHERE p.mr_number = 'MRN-2025-0076'
GROUP BY p.patient_id;
```

### Example 2: Patient Timeline
```sql
-- Get all activities for a patient in chronological order
SELECT
    'transcription' as activity_type,
    created_at as activity_date,
    'Audio transcription processed' as description
FROM audio_transcriptions
WHERE patient_id = 1

UNION ALL

SELECT
    'document' as activity_type,
    upload_date as activity_date,
    CONCAT('Document uploaded: ', document_type) as description
FROM patient_documents
WHERE patient_id = 1

UNION ALL

SELECT
    'visit' as activity_type,
    visit_date as activity_date,
    CONCAT('Visit: ', visit_type) as description
FROM patient_visits
WHERE patient_id = 1

ORDER BY activity_date DESC;
```

### Example 3: Export for External System
```php
function exportPatientByMRN($mrNumber) {
    // This single MR Number gets you EVERYTHING
    $data = [];

    // Get patient
    $data['patient'] = query("SELECT * FROM patients WHERE mr_number = ?", $mrNumber);

    // Get all related data using MR Number
    $data['transcriptions'] = query("SELECT * FROM audio_transcriptions WHERE mr_number = ?", $mrNumber);
    $data['documents'] = query("SELECT * FROM patient_documents WHERE mr_number = ?", $mrNumber);
    $data['reports'] = query("SELECT * FROM patient_reports WHERE mr_number = ?", $mrNumber);
    $data['visits'] = query("SELECT * FROM patient_visits WHERE mr_number = ?", $mrNumber);
    $data['history'] = query("SELECT * FROM patient_medical_history WHERE mr_number = ?", $mrNumber);

    return $data; // Complete patient record
}
```

## 🔑 Key Takeaways

1. **Patient table is the CENTER** - Everything connects to it
2. **Dual linking system** - patient_id (internal) + mr_number (external)
3. **One-to-Many relationships** - One patient, many records of each type
4. **Bidirectional navigation** - Can find patient from any related record
5. **Complete audit trail** - Every access is logged
6. **Export ready** - MR Number gets you everything

This design ensures that when you need to pass patient data to an external system, you can use the MR Number to retrieve ALL related records across ALL tables with complete data integrity!