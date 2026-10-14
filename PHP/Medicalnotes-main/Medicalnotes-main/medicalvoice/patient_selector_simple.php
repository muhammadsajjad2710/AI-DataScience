<?php
/**
 * Simplified Patient Selection Component for MedicalVoice
 * Standalone version with inline styles
 */
?>

<!-- Patient Selection Modal -->
<div id="patientModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); max-width: 900px; width: 90%; max-height: 90vh; overflow-y: auto;">

        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #3B82F6, #2563EB); color: white; padding: 30px; border-radius: 15px 15px 0 0;">
            <h2 style="margin: 0; display: flex; align-items: center; gap: 10px; color: white; font-size: 24px; font-weight: 700;">
                <span class="material-icons-round">person</span>
                Select or Add Patient
            </h2>
            <p style="margin-top: 10px; opacity: 0.9;">Choose an existing patient or create a new patient record</p>
        </div>

        <!-- Modal Body -->
        <div style="padding: 30px;">

            <!-- Search Section -->
            <div style="margin-bottom: 30px;">
                <div style="position: relative;">
                    <span class="material-icons-round" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999;">search</span>
                    <input type="text" id="patientSearch" placeholder="Search by MR Number, Name, or Phone..."
                           style="width: 100%; padding: 12px 12px 12px 50px; font-size: 16px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;">
                </div>
                <div id="searchResults" style="margin-top: 15px; display: none; max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 8px; background: white;"></div>
            </div>

            <!-- Recent Patients -->
            <div style="margin-bottom: 30px;">
                <h3 style="font-size: 18px; margin-bottom: 15px; color: #333;">
                    <span class="material-icons-round" style="vertical-align: middle; font-size: 20px;">history</span>
                    Recent Patients
                </h3>
                <div id="recentPatientsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                    <p style="color: #999; grid-column: 1 / -1;">Loading recent patients...</p>
                </div>
            </div>

            <!-- Divider -->
            <div style="text-align: center; margin: 40px 0;">
                <div style="position: relative;">
                    <hr style="border: none; border-top: 1px solid #ddd;">
                    <span style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: white; padding: 0 20px; color: #999;">OR</span>
                </div>
            </div>

            <!-- Add New Patient Button -->
            <div style="text-align: center;">
                <button id="showAddPatientForm" style="background: linear-gradient(135deg, #3B82F6, #2563EB); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);">
                    <span class="material-icons-round">add</span>
                    Add New Patient
                </button>
            </div>

            <!-- Add Patient Form (Hidden by default) -->
            <div id="addPatientForm" style="display: none; margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <h3 style="font-size: 18px; margin-bottom: 20px; color: #333;">
                    <span class="material-icons-round" style="vertical-align: middle;">person_add</span>
                    New Patient Information
                </h3>

                <form id="patientForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">

                    <!-- MR Number -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">MR Number</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="mrNumber" name="mr_number" placeholder="Auto-generate" readonly
                                   style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <button type="button" id="generateMRBtn" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f0f0f0; cursor: pointer;">
                                <span class="material-icons-round">refresh</span>
                            </button>
                        </div>
                    </div>

                    <!-- First Name -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">
                            First Name <span style="color: red;">*</span>
                        </label>
                        <input type="text" id="firstName" name="first_name" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">
                            Last Name <span style="color: red;">*</span>
                        </label>
                        <input type="text" id="lastName" name="last_name" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Date of Birth</label>
                        <input type="date" id="dateOfBirth" name="date_of_birth"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Gender</label>
                        <select id="gender" name="gender"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500;">Phone</label>
                        <input type="tel" id="phone" name="phone" placeholder="555-0123"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
                    </div>

                    <!-- Form Actions -->
                    <div style="grid-column: 1 / -1; display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                        <button type="button" id="cancelAddPatient" style="padding: 10px 20px; border: 1px solid #E5E7EB; border-radius: 8px; background: white; cursor: pointer; font-weight: 500; transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);">
                            Cancel
                        </button>
                        <button type="submit" style="background: linear-gradient(135deg, #3B82F6, #2563EB); color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);">
                            <span class="material-icons-round">save</span>
                            Save Patient
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 20px 30px; background: #f8f9fa; border-radius: 0 0 15px 15px; border-top: 1px solid #ddd;">
            <button id="skipPatientSelection" style="background: none; border: none; color: #666; cursor: pointer; text-decoration: underline;">
                Skip Patient Selection (Process without patient)
            </button>
        </div>
    </div>
</div>

<!-- Selected Patient Banner (Initially hidden) -->
<div id="selectedPatientBanner" style="display: none; background: linear-gradient(135deg, #e8f4f8, #d1e7dd); border: 1px solid #a3cfbb; border-radius: 10px; padding: 20px; margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #3B82F6, #2563EB); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                <span id="patientInitials">--</span>
            </div>
            <div>
                <div style="font-weight: 600; color: #333; font-size: 18px;">
                    <span id="patientName">No Patient Selected</span>
                </div>
                <div style="color: #666; font-size: 14px; margin-top: 5px;">
                    <span><strong>MR#:</strong> <span id="patientMRNumber">--</span></span> |
                    <span><strong>DOB:</strong> <span id="patientDOB">--</span></span> |
                    <span><strong>Age:</strong> <span id="patientAge">--</span></span>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <button id="viewPatientHistory" style="padding: 10px 20px; background: linear-gradient(135deg, #3B82F6, #2563EB); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);">
                <span class="material-icons-round" style="font-size: 18px;">history</span>
                View History
            </button>
            <button id="changePatient" style="padding: 10px 20px; background: linear-gradient(135deg, #8B5CF6, #7C3AED); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);">
                <span class="material-icons-round" style="font-size: 18px;">swap_horiz</span>
                Change Patient
            </button>
        </div>
    </div>
</div>

<script>
// Simple inline JavaScript for patient selector
(function() {
    'use strict';

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initPatientSelector);

    function initPatientSelector() {
        console.log('Initializing patient selector...');

        // Set up event handlers
        setupEventHandlers();

        // Generate initial MR number
        generateMRNumber();

        // Load recent patients
        loadRecentPatients();
    }

    function setupEventHandlers() {
        // Skip button
        const skipBtn = document.getElementById('skipPatientSelection');
        if (skipBtn) {
            skipBtn.onclick = function() {
                document.getElementById('patientModal').style.display = 'none';
                console.log('Patient selection skipped');
            };
        }

        // Show add patient form
        const showAddBtn = document.getElementById('showAddPatientForm');
        if (showAddBtn) {
            showAddBtn.onclick = function() {
                document.getElementById('addPatientForm').style.display = 'block';
                this.style.display = 'none';
            };
        }

        // Cancel add patient
        const cancelBtn = document.getElementById('cancelAddPatient');
        if (cancelBtn) {
            cancelBtn.onclick = function() {
                document.getElementById('addPatientForm').style.display = 'none';
                document.getElementById('showAddPatientForm').style.display = 'inline-block';
            };
        }

        // Generate MR button
        const generateBtn = document.getElementById('generateMRBtn');
        if (generateBtn) {
            generateBtn.onclick = generateMRNumber;
        }

        // Patient form submission
        const patientForm = document.getElementById('patientForm');
        if (patientForm) {
            patientForm.onsubmit = function(e) {
                e.preventDefault();
                savePatient();
                return false;
            };
        }

        // Search input
        const searchInput = document.getElementById('patientSearch');
        if (searchInput) {
            let searchTimeout;
            searchInput.oninput = function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchPatients(this.value);
                }, 300);
            };
        }

        // Change patient button
        const changeBtn = document.getElementById('changePatient');
        if (changeBtn) {
            changeBtn.onclick = function() {
                document.getElementById('patientModal').style.display = 'flex';
            };
        }
    }

    function generateMRNumber() {
        fetch('api/patient_api.php?action=generate_mr')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.mr_number) {
                    document.getElementById('mrNumber').value = data.mr_number;
                }
            })
            .catch(error => console.error('Error generating MR number:', error));
    }

    function loadRecentPatients() {
        fetch('api/patient_api.php?action=recent&limit=6')
            .then(response => response.json())
            .then(data => {
                const grid = document.getElementById('recentPatientsGrid');
                if (data.success && data.patients && data.patients.length > 0) {
                    grid.innerHTML = data.patients.map(patient => `
                        <div onclick="selectPatient(${patient.patient_id})" style="background: white; border: 1px solid #ddd; border-radius: 8px; padding: 15px; cursor: pointer;">
                            <div style="font-weight: 600; color: #333;">${patient.first_name} ${patient.last_name}</div>
                            <div style="font-size: 14px; color: #666; margin-top: 5px;">MR#: ${patient.mr_number}</div>
                        </div>
                    `).join('');
                } else {
                    grid.innerHTML = '<p style="color: #999; grid-column: 1 / -1;">No recent patients found</p>';
                }
            })
            .catch(error => {
                console.error('Error loading recent patients:', error);
                document.getElementById('recentPatientsGrid').innerHTML = '<p style="color: #999; grid-column: 1 / -1;">Error loading patients</p>';
            });
    }

    function searchPatients(query) {
        if (!query || query.length < 2) {
            document.getElementById('searchResults').style.display = 'none';
            return;
        }

        fetch(`api/patient_api.php?action=search&q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                const resultsDiv = document.getElementById('searchResults');
                if (data.success && data.patients && data.patients.length > 0) {
                    resultsDiv.innerHTML = data.patients.map(patient => `
                        <div onclick="selectPatient(${patient.patient_id})" style="padding: 15px; border-bottom: 1px solid #eee; cursor: pointer;">
                            <strong>${patient.first_name} ${patient.last_name}</strong>
                            <div style="font-size: 14px; color: #666;">MR#: ${patient.mr_number}</div>
                        </div>
                    `).join('');
                    resultsDiv.style.display = 'block';
                } else {
                    resultsDiv.innerHTML = '<p style="padding: 15px; color: #999;">No patients found</p>';
                    resultsDiv.style.display = 'block';
                }
            })
            .catch(error => console.error('Error searching patients:', error));
    }

    function savePatient() {
        const formData = new FormData(document.getElementById('patientForm'));
        const data = Object.fromEntries(formData);

        fetch('api/patient_api.php?action=create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success && result.patient) {
                selectPatient(result.patient.patient_id);
                document.getElementById('patientForm').reset();
            } else {
                alert('Error: ' + (result.error || 'Failed to save patient'));
            }
        })
        .catch(error => {
            console.error('Error saving patient:', error);
            alert('Error saving patient');
        });
    }

    window.selectPatient = function(patientId) {
        fetch('api/patient_api.php?action=select', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ patient_id: patientId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.patient) {
                updatePatientBanner(data.patient);
                document.getElementById('patientModal').style.display = 'none';
                console.log('Patient selected:', data.patient);
            }
        })
        .catch(error => console.error('Error selecting patient:', error));
    };

    function updatePatientBanner(patient) {
        const banner = document.getElementById('selectedPatientBanner');
        if (banner) {
            document.getElementById('patientInitials').textContent =
                `${patient.first_name[0]}${patient.last_name[0]}`.toUpperCase();
            document.getElementById('patientName').textContent =
                `${patient.first_name} ${patient.last_name}`;
            document.getElementById('patientMRNumber').textContent = patient.mr_number;
            document.getElementById('patientDOB').textContent =
                patient.date_of_birth || '--';
            document.getElementById('patientAge').textContent =
                patient.age ? `${patient.age} years` : '--';
            banner.style.display = 'block';

            // Store in global for other scripts to access
            window.currentPatient = patient;
        }
    }

    // Make current patient accessible globally
    window.patientSelector = {
        currentPatient: () => window.currentPatient || null,
        showModal: () => document.getElementById('patientModal').style.display = 'flex',
        hideModal: () => document.getElementById('patientModal').style.display = 'none',
        selectPatient: window.selectPatient
    };
})();
</script>