# 📊 Complete Database Tables Documentation - MedicalNotes Project

## Database: `db2`
**Total Tables: 29** (including 4 VIEWs)

---

## 🔐 **1. USER & AUTHENTICATION TABLES**

| Table Name | Records | Purpose |
|------------|---------|---------|
| **members** | 6 | User accounts with login credentials and credits |
| **user_sessions** | 0 | Active user session tracking |
| **password_reset_tokens** | 0 | Password recovery tokens |
| **credit_transactions** | 0 | Credit usage history and purchases |
| **payment_history** | 0 | Payment transactions for credits |
| **api_usage** | 0 | API usage tracking per user |

---

## 👥 **2. PATIENT MANAGEMENT TABLES**

| Table Name | Records | Purpose |
|------------|---------|---------|
| **patients** | 5 | Central patient registry with MR numbers |
| **patient_documents** | 0 | Patient medical documents storage |
| **patient_visits** | 0 | Patient visit/appointment records |
| **patient_medical_history** | 0 | Medical history and conditions |
| **patient_reports** | 0 | Generated medical reports |
| **patient_access_log** | 19 | HIPAA audit trail for patient data access |

### Views:
- **patient_complete_record** - Aggregated view of all patient data
- **patient_documents_view** - Enhanced view of patient documents

---

## 🎙️ **3. MEDICALVOICE MODULE TABLES**

| Table Name | Records | Purpose |
|------------|---------|---------|
| **medicalvoice_transcriptions** | 55 | Main audio transcription storage (CURRENT) |
| **audio_transcriptions** | 2 | Legacy transcription table (DEPRECATED) |
| **transcription_documents** | 20 | Documents linked to transcriptions |
| **medicalvoice_docs** | 7 | MedicalVoice specific documents |
| **medicalvoice_doc_associations** | 4 | Document relationships |
| **medicalvoice_doc_tags** | 0 | Document tagging system |
| **medicalvoice_doc_access_log** | 0 | Document access audit |
| **medicalvoice_combined_reports** | 0 | Combined transcription reports |

### Views:
- **transcription_documents_view** - Enhanced view of transcription documents

---

## 🏥 **4. FHIR/MEDICAL STANDARDS TABLES**

| Table Name | Records | Purpose |
|------------|---------|---------|
| **fhir_audio** | 64 | FHIR-compliant audio data storage |
| **fhir_documents** | 17 | FHIR-compliant document storage |

---

## ⚙️ **5. SYSTEM & UTILITY TABLES**

| Table Name | Records | Purpose |
|------------|---------|---------|
| **processing_logs** | 21 | System processing activity logs |
| **doc_processing_queue** | 4 | Document processing queue |

### Views:
- **user_credit_summary** - User credit usage summary
- **user_document_stats** - User document statistics

---

## 📈 **TABLE STATISTICS SUMMARY**

### By Module:
- **User Management**: 6 tables (6 users)
- **Patient Management**: 8 tables/views (5 patients)
- **MedicalVoice**: 9 tables/views (55 transcriptions)
- **FHIR Compliance**: 2 tables (81 records)
- **System/Utility**: 4 tables/views

### Storage Usage:
- **Total Data Size**: ~512 KB
- **Largest Table**: fhir_audio (144 KB, 64 records)
- **Most Records**: fhir_audio (64 records)
- **Active Transcriptions**: medicalvoice_transcriptions (55 records)

### Key Observations:
1. ✅ **Patient System**: Fully structured, ready for use
2. ⚠️ **Legacy Data**: 2 records in old audio_transcriptions table
3. 📊 **Document System**: Active with 20+ documents
4. 💳 **Credit System**: Tables ready but no transactions yet
5. 🔒 **Audit System**: Active with 19 access logs

---

## 🔗 **PRIMARY RELATIONSHIPS**

```
members (6)
    ├→ patients (5)
    │   ├→ patient_documents (0)
    │   ├→ patient_visits (0)
    │   ├→ patient_medical_history (0)
    │   └→ patient_access_log (19)
    │
    ├→ medicalvoice_transcriptions (55)
    │   └→ transcription_documents (20)
    │
    ├→ credit_transactions (0)
    └→ processing_logs (21)
```

---

## 🚀 **RECOMMENDATIONS**

1. **Migrate Legacy Data**: Move 2 records from audio_transcriptions to medicalvoice_transcriptions
2. **Activate Patient Features**: Start using patient_documents, patient_visits tables
3. **Implement Credit System**: Begin recording credit transactions
4. **Clean Unused Tables**: Consider removing deprecated tables if no longer needed