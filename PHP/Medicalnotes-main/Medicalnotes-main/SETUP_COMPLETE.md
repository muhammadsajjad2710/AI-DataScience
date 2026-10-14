# MedicalNotes Setup Complete Guide

## ✅ What Has Been Fixed

### 1. **Database Setup (COMPLETE)**
- Created new database `db2` with all required tables
- Tables include: members, fhir_documents, audio_transcriptions, credit_transactions, etc.
- Test user created: `testuser` / password: `password`

### 2. **Missing Files Created (COMPLETE)**
- ✅ `/medicalvoice/database.php` - Database connection for MedicalVoice
- ✅ `/medicalvision/database.php` - Database connection for MedicalVision
- ✅ `.env` file with configuration
- ✅ `config_loader.php` - Environment variable loader
- ✅ All required directories created

### 3. **Configuration Files (COMPLETE)**
- Environment-based configuration system implemented
- API keys moved to .env file
- Database connections updated to use db2

## ⚠️ Remaining Manual Steps Required

### 1. **Install Python Dependencies**
Due to system restrictions, run this command manually:
```bash
sudo pip3 install --break-system-packages --upgrade pip
sudo pip3 install --break-system-packages openai assemblyai google-generativeai Pillow
```

Or use a virtual environment (recommended):
```bash
python3 -m venv /var/www/html/medicalnotes/venv
source /var/www/html/medicalnotes/venv/bin/activate
pip install openai assemblyai google-generativeai Pillow python-dotenv
```

### 2. **Update API Keys in .env**
Edit `/var/www/html/medicalnotes/.env` and replace with your actual keys:
- `OPENAI_API_KEY=your_actual_openai_key`
- `ASSEMBLYAI_API_KEY=your_actual_assemblyai_key`
- `GOOGLE_AI_API_KEY=your_actual_google_ai_key`

### 3. **Update Python Scripts for Virtual Environment**
If using virtual environment, update the shebang in:
- `/medicalvoice/process_audio.py`
- `/medicalvision/handwritten_to_text.py`

Change first line to:
```python
#!/var/www/html/medicalnotes/venv/bin/python3
```

## 📁 Directory Structure
```
/var/www/html/medicalnotes/
├── medicalvoice/
│   ├── database.php ✅ (NEW)
│   ├── index.php
│   ├── process_audio.py
│   └── storage/uploads/audio/
├── medicalvision/
│   ├── database.php ✅ (NEW)
│   ├── index.php
│   ├── handwritten_to_text.py
│   └── storage/uploads/images/
├── .env ✅ (NEW)
├── config_loader.php ✅ (NEW)
├── setup_db2.sql ✅ (NEW)
└── requirements.txt ✅ (NEW)
```

## 🗄️ Database Tables Created in `db2`
1. `members` - User accounts
2. `fhir_documents` - OCR documents (MedicalVision)
3. `audio_transcriptions` - Audio transcriptions (MedicalVoice)
4. `credit_transactions` - Credit usage tracking
5. `processing_logs` - Activity logs
6. `api_usage` - API call tracking
7. `user_sessions` - Session management
8. `password_reset_tokens` - Password recovery
9. `payment_history` - Stripe payments

## 🔐 Test Credentials
- **Username:** testuser
- **Password:** password
- **Credits:** 100

## 🚀 Quick Test Commands

### Test Database Connection:
```bash
mysql -u root db2 -e "SELECT * FROM members;"
```

### Test Python Modules:
```bash
python3 -c "import openai, assemblyai, google.generativeai; print('All modules OK')"
```

### Test File Permissions:
```bash
ls -la medicalvoice/database.php
ls -la medicalvision/database.php
```

## 🔧 Troubleshooting

### If modules still don't work:
1. **Database Connection Errors:**
   - Check MySQL is running: `sudo systemctl status mysql`
   - Verify database exists: `mysql -u root -e "SHOW DATABASES;"`

2. **Python Module Errors:**
   - Use virtual environment as shown above
   - Or install system-wide with `--break-system-packages` flag

3. **Permission Errors:**
   ```bash
   sudo chown -R www-data:www-data /var/www/html/medicalnotes
   sudo chmod -R 755 /var/www/html/medicalnotes
   ```

## ✨ Summary
Both `medicalvoice` and `medicalvision` modules are now properly configured with:
- ✅ Database connections to `db2`
- ✅ All required tables
- ✅ Missing PHP files created
- ✅ Directory structure fixed
- ✅ Configuration system implemented
- ⏳ Python dependencies need manual installation
- ⏳ API keys need to be updated in .env

Once you complete the Python installation and add your API keys, both modules should work correctly!