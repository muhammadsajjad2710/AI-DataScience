# MedicalVoice Patient Management System - User Guide

## Overview
The Patient Management System has been successfully integrated into MedicalVoice. This system allows you to:
- Track patients with unique MR Numbers
- Link all transcriptions to specific patients
- View patient history
- Search and filter by patient

## How It Works

### 1. First Time Access
When you enter MedicalVoice (`/medicalnotes/medicalvoice/`), you will see:
- **Patient Selection Modal** - A popup window asking you to select or create a patient
- This modal appears automatically on page load

### 2. Patient Selection Options

#### A. Search for Existing Patient
- Use the search bar at the top of the modal
- Search by:
  - MR Number (e.g., MRN-2024-0001)
  - Patient Name
  - Phone Number

#### B. Select from Recent Patients
- Shows your 6 most recent patients
- Click on any patient card to select them

#### C. Create New Patient
- Click "Add New Patient" button
- Fill in the form:
  - **MR Number** - Auto-generated (click refresh icon to generate new)
  - **First Name** * (required)
  - **Last Name** * (required)
  - **Date of Birth** (optional)
  - **Gender** (optional)
  - **Phone** (optional)
- Click "Save Patient" to create

#### D. Skip Patient Selection
- Click "Skip Patient Selection" at the bottom
- Allows processing without patient context
- Transcriptions won't be linked to any patient

### 3. After Patient Selection

Once a patient is selected:
- **Patient Banner** appears at the top showing:
  - Patient name and initials
  - MR Number
  - Date of Birth
  - Age
- **Change Patient** button to select a different patient
- **View History** button to see all patient's transcriptions

### 4. Processing with Patient Context

When you upload audio files:
- The transcription is automatically linked to the selected patient
- Patient information is included in the processing
- Results are saved under the patient's record

### 5. Viewing Patient History

Access patient history by:
- Clicking "View History" in the patient banner
- Or visiting: `/medicalnotes/medicalvoice/patient_history.php?patient_id=[ID]`

The history page shows:
- Patient demographics
- Contact information
- Medical information
- All transcriptions with dates
- Download options for each transcription

## Database Structure

### Patients Table
```sql
- patient_id (Primary Key)
- mr_number (Unique identifier)
- member_id (Links to user account)
- first_name, last_name
- date_of_birth, gender
- phone, email, address
- emergency_contact, emergency_phone
- blood_type, allergies, medical_conditions
- created_at, updated_at
```

### Transcriptions Linkage
- `medicalvoice_transcriptions` table now includes `patient_id`
- All new transcriptions are linked to the selected patient

## API Endpoints

The system provides REST API endpoints:

### Search Patients
```
GET /medicalvoice/api/patient_api.php?action=search&q=[search_term]
```

### Get Recent Patients
```
GET /medicalvoice/api/patient_api.php?action=recent&limit=5
```

### Create Patient
```
POST /medicalvoice/api/patient_api.php?action=create
Body: JSON with patient data
```

### Select Patient
```
POST /medicalvoice/api/patient_api.php?action=select
Body: { "patient_id": 123 }
```

## Testing the System

### Test URLs:
1. **Main Application**: `/medicalnotes/medicalvoice/`
2. **Test Patient System**: `/medicalnotes/medicalvoice/test_patient_system.php`
3. **Test Access**: `/medicalnotes/medicalvoice/test_access.php`

### Test Workflow:
1. Log in to MedicalNotes
2. Navigate to MedicalVoice
3. Create a test patient
4. Upload an audio file
5. Check patient history

## Troubleshooting

### Patient Modal Not Showing
- Clear browser cache
- Check browser console for errors
- Verify you're logged in

### Can't Create Patient
- Ensure required fields are filled (First Name, Last Name)
- Check if MR Number is unique
- Verify database connection

### Can't Search Patients
- Minimum 2 characters required for search
- Check API endpoint is accessible
- Verify permissions on `/api/` directory

## Security Features

- **User Isolation**: Each user can only see their own patients
- **Audit Trail**: All patient access is logged
- **Session Management**: Patient context stored in session
- **Data Validation**: Input sanitization and validation

## File Locations

```
/medicalvoice/
├── patient_selector_simple.php   # Patient selection UI
├── patient_history.php           # Patient history view
├── includes/
│   └── patient_functions.php     # Core patient functions
├── api/
│   └── patient_api.php          # REST API endpoints
├── sql/
│   └── create_patients_table.sql # Database schema
└── test_patient_system.php      # Testing script
```

## MR Number Format

- Format: `MRN-YYYY-XXXX`
- Example: `MRN-2024-0001`
- Auto-generated to ensure uniqueness
- Year-based for easy organization

## Future Enhancements

Potential improvements:
- Patient photo upload
- Insurance information
- Appointment scheduling
- Document attachments
- Export to FHIR format
- Batch patient import
- Advanced search filters
- Patient dashboard

## Support

For issues or questions:
1. Check browser console for JavaScript errors
2. Review Apache error logs: `/var/log/apache2/error.log`
3. Test API endpoints directly
4. Verify database tables exist
5. Check file permissions (should be 644 for PHP files, 755 for directories)

---

The Patient Management System is now fully integrated with MedicalVoice, providing comprehensive patient tracking and history management while maintaining the existing design consistency.