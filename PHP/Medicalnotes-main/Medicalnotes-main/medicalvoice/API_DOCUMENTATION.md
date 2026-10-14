# MedicalVoice Patient Data Export API Documentation

## Overview
The MedicalVoice Patient Data Export API provides comprehensive access to patient records including audio transcriptions, documents, reports, and medical history. All data is linked via the Medical Record Number (MR Number).

## Base URL
```
http://your-domain/medicalnotes/medicalvoice/api/patient_export_api.php
```

## Authentication
The API supports two authentication methods:

1. **Session-based** (for internal use)
   - Requires active user session

2. **API Key** (for external systems)
   - Pass `api_key` parameter with your request
   - Example: `?api_key=your_api_key_here`

## Endpoints

### 1. Export Complete Patient Record (JSON)

**Request:**
```
GET /patient_export_api.php?mr_number=MRN-2024-0001&format=json
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "export_timestamp": "2024-01-15 10:30:00",
    "export_version": "1.0",
    "mr_number": "MRN-2024-0001",
    "patient": {
      "patient_id": 123,
      "mr_number": "MRN-2024-0001",
      "first_name": "John",
      "last_name": "Doe",
      "date_of_birth": "1985-03-15",
      "gender": "Male",
      "phone": "555-0101",
      "email": "john.doe@email.com",
      "blood_type": "O+",
      "allergies": "Penicillin",
      "medical_conditions": "Hypertension"
    },
    "audio_transcriptions": [...],
    "documents": [...],
    "reports": [...],
    "medical_history": [...],
    "visits": [...],
    "summary": {
      "total_transcriptions": 5,
      "total_documents": 10,
      "total_reports": 3,
      "total_visits": 8,
      "medical_history_entries": 15
    }
  }
}
```

### 2. Export as FHIR Bundle

**Request:**
```
GET /patient_export_api.php?mr_number=MRN-2024-0001&format=fhir
```

**Response:**
```json
{
  "resourceType": "Bundle",
  "type": "collection",
  "timestamp": "2024-01-15T10:30:00Z",
  "entry": [
    {
      "resource": {
        "resourceType": "Patient",
        "id": "123",
        "identifier": [{
          "system": "http://hospital.local/mrn",
          "value": "MRN-2024-0001"
        }],
        "name": [{
          "family": "Doe",
          "given": ["John"]
        }],
        "gender": "male",
        "birthDate": "1985-03-15"
      }
    },
    {
      "resource": {
        "resourceType": "DocumentReference",
        "id": "transcription-456",
        "status": "current",
        "subject": {
          "reference": "Patient/123"
        },
        "content": [...]
      }
    }
  ]
}
```

### 3. Export as CSV

**Request:**
```
GET /patient_export_api.php?mr_number=MRN-2024-0001&format=csv
```

**Response:**
- Downloads CSV file with patient data
- Filename: `patient_MRN-2024-0001_2024-01-15.csv`

### 4. Export Summary (Lightweight)

**Request:**
```
GET /patient_export_api.php?mr_number=MRN-2024-0001&format=summary
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "patient": {...},
    "summary": {
      "total_transcriptions": 5,
      "total_documents": 10,
      "total_reports": 3,
      "total_visits": 8
    },
    "recent_transcriptions": [...],
    "recent_documents": [...],
    "recent_visits": [...]
  },
  "timestamp": "2024-01-15 10:30:00"
}
```

## Error Responses

### 401 Unauthorized
```json
{
  "status": "error",
  "message": "Authentication required"
}
```

### 404 Not Found
```json
{
  "status": "error",
  "message": "Patient not found with MR Number: MRN-XXX"
}
```

### 400 Bad Request
```json
{
  "status": "error",
  "message": "MR Number is required"
}
```

## PHP Integration Examples

### Export Patient Data
```php
require_once 'includes/patient_export_functions.php';

// Export complete record
$patientData = exportPatientRecordByMRN($conn, 'MRN-2024-0001');

// Export as FHIR
$fhirBundle = exportPatientAsFHIR($conn, 'MRN-2024-0001');

// Generate report
$report = generatePatientReport($conn, 'MRN-2024-0001');
```

### Link Transcription to Patient
```php
// Link existing transcription to a patient
$result = linkTranscriptionToPatient($conn, $transcriptionId, $patientId);

if ($result['success']) {
    echo "Transcription linked successfully";
}
```

### Upload with Patient Association
```php
// When uploading audio
$formData = [
    'action' => 'upload_file',
    'patient_id' => $selectedPatientId,
    'audio_file' => $_FILES['audio']
];

// When uploading documents
$formData = [
    'action' => 'upload_documents',
    'patient_id' => $selectedPatientId,
    'transcription_id' => $transcriptionId,
    'documents' => $_FILES['documents']
];
```

## JavaScript/AJAX Examples

### Fetch Patient Data via AJAX
```javascript
// jQuery example
$.ajax({
    url: '/medicalnotes/medicalvoice/api/patient_export_api.php',
    method: 'GET',
    data: {
        mr_number: 'MRN-2024-0001',
        format: 'json',
        api_key: 'your_api_key' // if using API key auth
    },
    success: function(response) {
        if (response.status === 'success') {
            console.log('Patient data:', response.data);
            // Process patient data
        }
    },
    error: function(xhr) {
        console.error('API error:', xhr.responseJSON);
    }
});
```

### Upload with Patient Association
```javascript
// When uploading audio
const formData = new FormData();
formData.append('action', 'upload_file');
formData.append('patient_id', selectedPatientId);
formData.append('audio_file', audioFile);

fetch('/medicalnotes/medicalvoice/upload.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Upload successful, transcription ID:', data.transcription_id);
    }
});
```

## Database Schema

### Key Tables
- `patients` - Patient records with MR Number
- `audio_transcriptions` - Audio processing results (linked via patient_id and mr_number)
- `patient_documents` - Uploaded documents (linked via patient_id and mr_number)
- `patient_reports` - Generated reports
- `patient_medical_history` - Medical history entries
- `patient_visits` - Patient encounters

### Relationships
```sql
audio_transcriptions.patient_id -> patients.patient_id
audio_transcriptions.mr_number = patients.mr_number
patient_documents.patient_id -> patients.patient_id
patient_documents.mr_number = patients.mr_number
patient_reports.patient_id -> patients.patient_id
patient_reports.mr_number = patients.mr_number
```

## Testing

Run the test script to verify your implementation:
```bash
php /var/www/html/medicalnotes/medicalvoice/test_patient_linking.php
```

## Security Considerations

1. **Authentication**: Always verify user authentication before accessing patient data
2. **HIPAA Compliance**: Ensure all data transmission is encrypted
3. **Audit Logging**: All patient data access is logged
4. **Data Validation**: Validate MR Number format before processing
5. **Rate Limiting**: Implement rate limiting for API endpoints

## Support

For issues or questions about the API, please contact your system administrator.