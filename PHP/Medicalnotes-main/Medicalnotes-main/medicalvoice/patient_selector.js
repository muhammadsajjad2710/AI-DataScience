/**
 * Patient Selector JavaScript
 * Handles patient selection, search, and form interactions
 */

// Global patient state
let currentPatient = null;

// Initialize patient selector on document ready
document.addEventListener('DOMContentLoaded', function() {
    initializePatientSelector();
});

function initializePatientSelector() {
    // Load recent patients
    loadRecentPatients();

    // Set up event listeners
    setupEventListeners();

    // Check if patient is already selected in session
    checkExistingPatientSession();

    // Show modal on page load if no patient selected
    if (!currentPatient) {
        showPatientModal();
    }
}

function setupEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('patientSearch');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchPatients(this.value);
            }, 300);
        });
    }

    // Show add patient form
    const showAddBtn = document.getElementById('showAddPatientForm');
    if (showAddBtn) {
        showAddBtn.addEventListener('click', function() {
            document.getElementById('addPatientForm').style.display = 'block';
            this.style.display = 'none';
            generateMRNumber();
        });
    }

    // Cancel add patient
    const cancelAddBtn = document.getElementById('cancelAddPatient');
    if (cancelAddBtn) {
        cancelAddBtn.addEventListener('click', function() {
            document.getElementById('addPatientForm').style.display = 'none';
            document.getElementById('showAddPatientForm').style.display = 'inline-flex';
            document.getElementById('patientForm').reset();
        });
    }

    // Generate MR Number
    const generateBtn = document.getElementById('generateMRBtn');
    if (generateBtn) {
        generateBtn.addEventListener('click', generateMRNumber);
    }

    // Patient form submission
    const patientForm = document.getElementById('patientForm');
    if (patientForm) {
        patientForm.addEventListener('submit', function(e) {
            e.preventDefault();
            savePatient();
        });
    }

    // Skip patient selection
    const skipBtn = document.getElementById('skipPatientSelection');
    if (skipBtn) {
        skipBtn.addEventListener('click', function() {
            hidePatientModal();
            showNotification('Processing without patient context', 'info');
        });
    }

    // Change patient button
    const changeBtn = document.getElementById('changePatient');
    if (changeBtn) {
        changeBtn.addEventListener('click', function() {
            showPatientModal();
        });
    }

    // View patient history
    const historyBtn = document.getElementById('viewPatientHistory');
    if (historyBtn) {
        historyBtn.addEventListener('click', function() {
            if (currentPatient) {
                viewPatientHistory(currentPatient.patient_id);
            }
        });
    }
}

// Load recent patients
async function loadRecentPatients() {
    try {
        const response = await fetch('api/patient_api.php?action=recent&limit=6');
        const data = await response.json();

        if (data.success && data.patients) {
            const grid = document.getElementById('recentPatientsGrid');
            if (grid) {
                grid.innerHTML = '';

                if (data.patients.length === 0) {
                    grid.innerHTML = '<p style="color: var(--neutral-500); text-align: center; grid-column: 1 / -1;">No recent patients found</p>';
                } else {
                    data.patients.forEach(patient => {
                        const card = createPatientCard(patient);
                        grid.appendChild(card);
                    });
                }
            }
        }
    } catch (error) {
        console.error('Error loading recent patients:', error);
    }
}

// Search patients
async function searchPatients(query) {
    if (!query || query.length < 2) {
        document.getElementById('searchResults').style.display = 'none';
        return;
    }

    try {
        const response = await fetch(`api/patient_api.php?action=search&q=${encodeURIComponent(query)}`);
        const data = await response.json();

        if (data.success && data.patients) {
            displaySearchResults(data.patients);
        }
    } catch (error) {
        console.error('Error searching patients:', error);
    }
}

// Display search results
function displaySearchResults(patients) {
    const resultsDiv = document.getElementById('searchResults');
    if (!resultsDiv) return;

    if (patients.length === 0) {
        resultsDiv.innerHTML = '<p style="padding: var(--space-4); color: var(--neutral-500); text-align: center;">No patients found</p>';
    } else {
        resultsDiv.innerHTML = patients.map(patient => {
            const age = patient.date_of_birth ? calculateAge(patient.date_of_birth) : '';
            return `
                <div class="search-result-item" onclick="selectPatient(${patient.patient_id})">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>${patient.first_name} ${patient.last_name}</strong>
                            <div style="font-size: var(--text-sm); color: var(--neutral-600);">
                                MR#: ${patient.mr_number} ${age ? `• Age: ${age}` : ''}
                            </div>
                        </div>
                        <span class="material-icons-round" style="color: var(--primary-500);">arrow_forward</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    resultsDiv.style.display = 'block';
}

// Create patient card
function createPatientCard(patient) {
    const template = document.getElementById('patientCardTemplate');
    const card = template.content.cloneNode(true);

    const initials = `${patient.first_name[0]}${patient.last_name[0]}`.toUpperCase();
    const age = patient.date_of_birth ? calculateAge(patient.date_of_birth) : '';

    card.querySelector('.patient-initials').textContent = initials;
    card.querySelector('.patient-name').textContent = `${patient.first_name} ${patient.last_name}`;
    card.querySelector('.patient-mr').textContent = `MR#: ${patient.mr_number}`;
    card.querySelector('.patient-dob').textContent = patient.date_of_birth ? formatDate(patient.date_of_birth) : 'DOB: Not set';

    const cardElement = card.querySelector('.patient-card');
    cardElement.onclick = () => selectPatient(patient.patient_id);

    return card;
}

// Select patient
async function selectPatient(patientId) {
    try {
        const response = await fetch('api/patient_api.php?action=select', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ patient_id: patientId })
        });

        const data = await response.json();

        if (data.success && data.patient) {
            currentPatient = data.patient;
            updatePatientBanner(data.patient);
            hidePatientModal();
            showNotification(`Patient ${data.patient.first_name} ${data.patient.last_name} selected`, 'success');

            // Update workflow to show patient context
            updateWorkflowWithPatient();
        } else {
            showNotification('Failed to select patient', 'error');
        }
    } catch (error) {
        console.error('Error selecting patient:', error);
        showNotification('Error selecting patient', 'error');
    }
}

// Save new patient
async function savePatient() {
    const form = document.getElementById('patientForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    // Validate required fields
    if (!data.first_name || !data.last_name) {
        showNotification('Please fill in required fields', 'error');
        return;
    }

    try {
        const response = await fetch('api/patient_api.php?action=create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success && result.patient) {
            currentPatient = result.patient;
            updatePatientBanner(result.patient);
            hidePatientModal();
            showNotification('Patient created successfully', 'success');
            form.reset();

            // Reload recent patients
            loadRecentPatients();

            // Update workflow
            updateWorkflowWithPatient();
        } else {
            const errors = result.errors ? result.errors.join(', ') : result.error;
            showNotification(`Error: ${errors}`, 'error');
        }
    } catch (error) {
        console.error('Error saving patient:', error);
        showNotification('Error saving patient', 'error');
    }
}

// Generate MR Number
async function generateMRNumber() {
    try {
        const response = await fetch('api/patient_api.php?action=generate_mr');
        const data = await response.json();

        if (data.success && data.mr_number) {
            document.getElementById('mrNumber').value = data.mr_number;
        }
    } catch (error) {
        console.error('Error generating MR number:', error);
    }
}

// Update patient banner
function updatePatientBanner(patient) {
    const banner = document.getElementById('selectedPatientBanner');
    if (!banner) return;

    const initials = `${patient.first_name[0]}${patient.last_name[0]}`.toUpperCase();
    const age = patient.date_of_birth ? calculateAge(patient.date_of_birth) : null;

    document.getElementById('patientInitials').textContent = initials;
    document.getElementById('patientName').textContent = `${patient.first_name} ${patient.last_name}`;
    document.getElementById('patientMRNumber').textContent = patient.mr_number;
    document.getElementById('patientDOB').textContent = patient.date_of_birth ? formatDate(patient.date_of_birth) : '--';
    document.getElementById('patientAge').textContent = age ? `${age} years` : '--';

    banner.style.display = 'block';
}

// View patient history
async function viewPatientHistory(patientId) {
    try {
        const response = await fetch(`api/patient_api.php?action=history&patient_id=${patientId}`);
        const data = await response.json();

        if (data.success && data.transcriptions) {
            // Create and show history modal
            showPatientHistoryModal(data.transcriptions);
        }
    } catch (error) {
        console.error('Error loading patient history:', error);
        showNotification('Error loading patient history', 'error');
    }
}

// Show patient history modal
function showPatientHistoryModal(transcriptions) {
    // This would show a modal with the patient's transcription history
    // For now, we'll just log it
    console.log('Patient transcriptions:', transcriptions);
    showNotification(`Found ${transcriptions.length} transcriptions for this patient`, 'info');
}

// Check existing patient session
async function checkExistingPatientSession() {
    // This function would check if there's a patient already selected in the session
    // For now, we'll skip this as it requires backend implementation
}

// Update workflow with patient context
function updateWorkflowWithPatient() {
    // Update the workflow to show patient is selected
    const uploadStep = document.querySelector('[data-step="upload"]');
    if (uploadStep && currentPatient) {
        uploadStep.classList.add('active');
        uploadStep.querySelector('.step-status').textContent = 'Ready with Patient';
    }
}

// Modal functions
function showPatientModal() {
    const modal = document.getElementById('patientModal');
    if (modal) {
        modal.style.display = 'flex';
        loadRecentPatients();
    }
}

function hidePatientModal() {
    const modal = document.getElementById('patientModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Utility functions
function calculateAge(dateOfBirth) {
    const dob = new Date(dateOfBirth);
    const now = new Date();
    let age = now.getFullYear() - dob.getFullYear();
    const monthDiff = now.getMonth() - dob.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && now.getDate() < dob.getDate())) {
        age--;
    }

    return age;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Export functions for use in other scripts
window.patientSelector = {
    currentPatient: () => currentPatient,
    showModal: showPatientModal,
    hideModal: hidePatientModal,
    selectPatient: selectPatient
};