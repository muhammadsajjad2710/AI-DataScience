#!/bin/bash

# MedicalNotes Installation Script
# This script sets up the complete environment for MedicalNotes

echo "========================================="
echo "MedicalNotes Installation Script"
echo "========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    print_error "Please run as root (use sudo)"
    exit 1
fi

# Step 1: Install Python dependencies
echo ""
echo "Step 1: Installing Python dependencies..."
echo "-----------------------------------------"

# Check if pip3 is installed
if ! command -v pip3 &> /dev/null; then
    print_warning "pip3 not found. Installing python3-pip..."
    apt-get update
    apt-get install -y python3-pip
fi

# Install Python packages
pip3 install -r requirements.txt
if [ $? -eq 0 ]; then
    print_status "Python dependencies installed successfully"
else
    print_error "Failed to install Python dependencies"
    exit 1
fi

# Step 2: Create database
echo ""
echo "Step 2: Setting up database..."
echo "-----------------------------------------"

# Check if MySQL is installed
if ! command -v mysql &> /dev/null; then
    print_error "MySQL is not installed. Please install MySQL first."
    exit 1
fi

# Create database
mysql -u root -e "SOURCE setup_db2.sql" 2>/dev/null
if [ $? -eq 0 ]; then
    print_status "Database 'db2' created successfully"
else
    print_warning "Database might already exist or there was an error. Continuing..."
fi

# Step 3: Set up directories and permissions
echo ""
echo "Step 3: Setting up directories and permissions..."
echo "-----------------------------------------"

# Create required directories
directories=(
    "storage/uploads/audio/files"
    "storage/uploads/audio/json"
    "storage/uploads/audio/logs"
    "storage/uploads/vision/files"
    "storage/uploads/vision/json"
    "storage/uploads/vision/logs"
    "medicalvoice/storage/uploads/audio/files"
    "medicalvoice/storage/uploads/audio/json"
    "medicalvoice/storage/uploads/audio/logs"
    "medicalvision/storage/uploads/images"
)

for dir in "${directories[@]}"; do
    mkdir -p "$dir"
    chmod 755 "$dir"
    chown www-data:www-data "$dir"
    print_status "Created directory: $dir"
done

# Step 4: Set file permissions
echo ""
echo "Step 4: Setting file permissions..."
echo "-----------------------------------------"

# Set ownership for web files
chown -R www-data:www-data /var/www/html/medicalnotes
chmod -R 755 /var/www/html/medicalnotes

# Make Python scripts executable
chmod +x medicalvoice/process_audio.py
chmod +x medicalvision/handwritten_to_text.py

print_status "File permissions set"

# Step 5: Create .env file if it doesn't exist
echo ""
echo "Step 5: Setting up environment configuration..."
echo "-----------------------------------------"

if [ ! -f .env ]; then
    cp .env.example .env
    print_status "Created .env file from template"
    print_warning "Please edit .env file and add your API keys"
else
    print_status ".env file already exists"
fi

# Step 6: Create test user
echo ""
echo "Step 6: Creating test user..."
echo "-----------------------------------------"

# Generate password hash for 'admin123'
PASS_HASH='$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'

mysql -u root db2 -e "INSERT IGNORE INTO members (username, email, password, credits) VALUES ('testuser', 'test@medicalnotes.local', '$PASS_HASH', 100);" 2>/dev/null

if [ $? -eq 0 ]; then
    print_status "Test user created (username: testuser, password: admin123)"
else
    print_warning "Test user might already exist"
fi

# Step 7: Verify installation
echo ""
echo "Step 7: Verifying installation..."
echo "-----------------------------------------"

# Check Python modules
python3 -c "import openai; import assemblyai; import google.generativeai; print('All Python modules loaded successfully')" 2>/dev/null
if [ $? -eq 0 ]; then
    print_status "Python modules verified"
else
    print_error "Some Python modules are missing"
fi

# Check database connection
mysql -u root db2 -e "SELECT COUNT(*) FROM members;" > /dev/null 2>&1
if [ $? -eq 0 ]; then
    print_status "Database connection verified"
else
    print_error "Cannot connect to database"
fi

# Final status
echo ""
echo "========================================="
echo "Installation Complete!"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Edit .env file and add your API keys"
echo "2. Update database credentials if needed"
echo "3. Access the application at http://localhost/medicalnotes"
echo ""
echo "Default test account:"
echo "Username: testuser"
echo "Password: admin123"
echo ""
print_warning "Remember to change the default password!"
echo ""