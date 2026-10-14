<?php
/**
 * Patient Selection Component for MedicalVoice
 * Maintains consistent UI with the existing design system
 */
?>

<!-- Patient Selection Modal - Using existing design system classes -->
<div id="patientModal" class="modal-overlay" style="display: flex;">
    <div class="modal-container animate-fade-in-up" style="max-width: 900px;">
        <!-- Modal Header with existing gradient style -->
        <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-600), var(--secondary-600)); color: white; padding: var(--space-6); border-radius: var(--radius-2xl) var(--radius-2xl) 0 0;">
            <h2 class="modal-title" style="display: flex; align-items: center; gap: var(--space-3); color: white;">
                <span class="material-icons-round">person</span>
                Select or Add Patient
            </h2>
            <p style="margin-top: var(--space-2); opacity: 0.9;">Choose an existing patient or create a new patient record</p>
        </div>

        <!-- Modal Body -->
        <div class="modal-body" style="padding: var(--space-6);">
            <!-- Search Section -->
            <div class="search-section" style="margin-bottom: var(--space-6);">
                <div class="search-container" style="position: relative;">
                    <span class="material-icons-round" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--neutral-500);">search</span>
                    <input type="text" id="patientSearch" class="form-control"
                           placeholder="Search by MR Number, Name, or Phone..."
                           style="padding-left: 50px; padding: var(--space-3) var(--space-4) var(--space-3) 50px; font-size: var(--text-base); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300); width: 100%;">
                </div>
                <div id="searchResults" class="search-results" style="margin-top: var(--space-4); display: none;"></div>
            </div>

            <!-- Recent Patients Section -->
            <div class="recent-patients-section" style="margin-bottom: var(--space-6);">
                <h3 class="section-subtitle" style="font-size: var(--text-lg); font-weight: 600; margin-bottom: var(--space-3); color: var(--neutral-700);">
                    <span class="material-icons-round" style="vertical-align: middle; font-size: 20px;">history</span>
                    Recent Patients
                </h3>
                <div id="recentPatientsGrid" class="patients-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--space-4);">
                    <!-- Recent patients will be loaded here -->
                </div>
            </div>

            <!-- Divider with OR -->
            <div class="divider-section" style="text-align: center; margin: var(--space-8) 0;">
                <div style="position: relative;">
                    <hr style="border: none; border-top: 1px solid var(--neutral-300);">
                    <span style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: white; padding: 0 var(--space-4); color: var(--neutral-500);">OR</span>
                </div>
            </div>

            <!-- Add New Patient Button -->
            <div style="text-align: center;">
                <button id="showAddPatientForm" class="btn btn-primary" style="background: linear-gradient(135deg, var(--primary-500), var(--primary-600)); padding: var(--space-3) var(--space-6);">
                    <span class="material-icons-round">add</span>
                    Add New Patient
                </button>
            </div>

            <!-- Add Patient Form (Hidden by default) -->
            <div id="addPatientForm" style="display: none; margin-top: var(--space-6);">
                <h3 class="section-subtitle" style="font-size: var(--text-lg); font-weight: 600; margin-bottom: var(--space-4); color: var(--neutral-700);">
                    <span class="material-icons-round" style="vertical-align: middle; font-size: 20px;">person_add</span>
                    New Patient Information
                </h3>

                <form id="patientForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--space-4);">
                    <!-- MR Number -->
                    <div class="form-group">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">MR Number</label>
                        <div style="display: flex; gap: var(--space-2);">
                            <input type="text" id="mrNumber" name="mr_number" class="form-control" placeholder="Auto-generate" readonly
                                   style="flex: 1; padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300);">
                            <button type="button" id="generateMRBtn" class="btn btn-secondary" style="padding: var(--space-3);">
                                <span class="material-icons-round">refresh</span>
                            </button>
                        </div>
                    </div>

                    <!-- First Name -->
                    <div class="form-group">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">
                            First Name <span style="color: var(--error-500);">*</span>
                        </label>
                        <input type="text" id="firstName" name="first_name" class="form-control" required
                               style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300);">
                    </div>

                    <!-- Last Name -->
                    <div class="form-group">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">
                            Last Name <span style="color: var(--error-500);">*</span>
                        </label>
                        <input type="text" id="lastName" name="last_name" class="form-control" required
                               style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300);">
                    </div>

                    <!-- Date of Birth -->
                    <div class="form-group">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">Date of Birth</label>
                        <input type="date" id="dateOfBirth" name="date_of_birth" class="form-control"
                               style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300);">
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">Gender</label>
                        <select id="gender" name="gender" class="form-control"
                                style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300);">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="555-0123"
                               style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300);">
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="patient@email.com"
                               style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300);">
                    </div>

                    <!-- Blood Type -->
                    <div class="form-group">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">Blood Type</label>
                        <select id="bloodType" name="blood_type" class="form-control"
                                style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300);">
                            <option value="">Select Blood Type</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>

                    <!-- Address (Full width) -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="2"
                                  style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300); resize: vertical;"></textarea>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="form-group">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">Emergency Contact</label>
                        <input type="text" id="emergencyContact" name="emergency_contact" class="form-control"
                               style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300);">
                    </div>

                    <!-- Emergency Phone -->
                    <div class="form-group">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">Emergency Phone</label>
                        <input type="tel" id="emergencyPhone" name="emergency_phone" class="form-control"
                               style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300);">
                    </div>

                    <!-- Allergies (Full width) -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">Allergies</label>
                        <textarea id="allergies" name="allergies" class="form-control" rows="2" placeholder="List any known allergies..."
                                  style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300); resize: vertical;"></textarea>
                    </div>

                    <!-- Medical Conditions (Full width) -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" style="color: var(--neutral-700); font-weight: 500; margin-bottom: var(--space-2);">Medical Conditions</label>
                        <textarea id="medicalConditions" name="medical_conditions" class="form-control" rows="3" placeholder="List any existing medical conditions..."
                                  style="padding: var(--space-3); border-radius: var(--radius-lg); border: 1px solid var(--neutral-300); resize: vertical;"></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions" style="grid-column: 1 / -1; display: flex; gap: var(--space-3); justify-content: flex-end; margin-top: var(--space-4);">
                        <button type="button" id="cancelAddPatient" class="btn btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, var(--primary-500), var(--primary-600));">
                            <span class="material-icons-round">save</span>
                            Save Patient
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Skip Patient Selection Option -->
        <div class="modal-footer" style="padding: var(--space-4) var(--space-6); background: var(--neutral-50); border-radius: 0 0 var(--radius-2xl) var(--radius-2xl); border-top: 1px solid var(--neutral-200);">
            <button id="skipPatientSelection" class="btn btn-link" style="color: var(--neutral-600); text-decoration: none;">
                Skip Patient Selection (Process without patient)
            </button>
        </div>
    </div>
</div>

<!-- Selected Patient Banner (Initially hidden) -->
<div id="selectedPatientBanner" class="patient-banner animate-fade-in-down" style="display: none; background: linear-gradient(135deg, var(--primary-50), var(--secondary-50)); border: 1px solid var(--primary-200); border-radius: var(--radius-xl); padding: var(--space-4); margin-bottom: var(--space-6);">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: var(--space-4);">
            <div class="patient-avatar" style="width: 50px; height: 50px; border-radius: var(--radius-full); background: linear-gradient(135deg, var(--primary-500), var(--primary-600)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: var(--text-lg);">
                <span id="patientInitials">--</span>
            </div>
            <div>
                <div style="font-weight: 600; color: var(--neutral-900); font-size: var(--text-lg);">
                    <span id="patientName">No Patient Selected</span>
                </div>
                <div style="color: var(--neutral-600); font-size: var(--text-sm); display: flex; gap: var(--space-4); margin-top: var(--space-1);">
                    <span><strong>MR#:</strong> <span id="patientMRNumber">--</span></span>
                    <span><strong>DOB:</strong> <span id="patientDOB">--</span></span>
                    <span><strong>Age:</strong> <span id="patientAge">--</span></span>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: var(--space-2);">
            <button id="viewPatientHistory" class="btn btn-sm btn-secondary">
                <span class="material-icons-round">history</span>
                View History
            </button>
            <button id="changePatient" class="btn btn-sm btn-primary">
                <span class="material-icons-round">swap_horiz</span>
                Change Patient
            </button>
        </div>
    </div>
</div>

<!-- Patient Card Template -->
<template id="patientCardTemplate">
    <div class="patient-card" style="background: white; border: 1px solid var(--neutral-200); border-radius: var(--radius-lg); padding: var(--space-4); cursor: pointer; transition: all var(--transition-normal); box-shadow: var(--shadow-sm);">
        <div style="display: flex; align-items: center; gap: var(--space-3);">
            <div class="patient-avatar-sm" style="width: 40px; height: 40px; border-radius: var(--radius-full); background: linear-gradient(135deg, var(--primary-400), var(--primary-500)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 500;">
                <span class="patient-initials">--</span>
            </div>
            <div style="flex: 1;">
                <div class="patient-name" style="font-weight: 600; color: var(--neutral-900);"></div>
                <div style="font-size: var(--text-sm); color: var(--neutral-600);">
                    <span class="patient-mr"></span> • <span class="patient-dob"></span>
                </div>
            </div>
            <span class="material-icons-round" style="color: var(--primary-500);">arrow_forward</span>
        </div>
    </div>
</template>

<style>
/* Patient Modal Specific Styles - Consistent with existing design */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.3s ease-out;
}

.modal-container {
    background: white;
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-2xl);
    max-height: 90vh;
    overflow-y: auto;
    width: 90%;
}

.patient-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-300);
}

.search-results {
    max-height: 300px;
    overflow-y: auto;
    background: white;
    border: 1px solid var(--neutral-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
}

.search-result-item {
    padding: var(--space-3);
    border-bottom: 1px solid var(--neutral-100);
    cursor: pointer;
    transition: background var(--transition-fast);
}

.search-result-item:hover {
    background: var(--neutral-50);
}

.search-result-item:last-child {
    border-bottom: none;
}

/* Animation for patient banner */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-down {
    animation: fadeInDown 0.5s ease-out;
}

/* Ensure form controls match existing design */
.form-control:focus {
    outline: none;
    border-color: var(--primary-400);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

/* Loading spinner consistent with existing design */
.loading-spinner {
    border: 3px solid var(--neutral-200);
    border-top-color: var(--primary-500);
    border-radius: 50%;
    width: 20px;
    height: 20px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
// Ensure showNotification function exists
if (typeof showNotification === 'undefined') {
    window.showNotification = function(message, type) {
        console.log(`[${type}] ${message}`);
        alert(message); // Fallback to alert
    };
}

// Initialize patient modal immediately
document.addEventListener('DOMContentLoaded', function() {
    console.log('Patient selector initialized');

    // Make sure modal is visible
    const modal = document.getElementById('patientModal');
    if (modal) {
        modal.style.display = 'flex';
        console.log('Patient modal displayed');
    }

    // Set up basic event handlers if main script hasn't loaded
    const skipBtn = document.getElementById('skipPatientSelection');
    if (skipBtn) {
        skipBtn.onclick = function() {
            if (modal) modal.style.display = 'none';
            console.log('Patient selection skipped');
        };
    }

    // Handle show add patient form button
    const showAddBtn = document.getElementById('showAddPatientForm');
    if (showAddBtn) {
        showAddBtn.onclick = function() {
            const addForm = document.getElementById('addPatientForm');
            if (addForm) {
                addForm.style.display = 'block';
                this.style.display = 'none';
            }
        };
    }

    // Handle cancel add patient button
    const cancelBtn = document.getElementById('cancelAddPatient');
    if (cancelBtn) {
        cancelBtn.onclick = function() {
            const addForm = document.getElementById('addPatientForm');
            const showBtn = document.getElementById('showAddPatientForm');
            if (addForm) addForm.style.display = 'none';
            if (showBtn) showBtn.style.display = 'inline-flex';
        };
    }
});
</script>