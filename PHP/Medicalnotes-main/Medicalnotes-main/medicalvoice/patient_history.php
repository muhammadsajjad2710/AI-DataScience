<?php
/**
 * Patient History View
 * Shows all transcriptions for a specific patient
 */

session_start();

// Check authentication
if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit;
}

// Include required files
require_once 'database.php';
require_once 'includes/patient_functions.php';

$memberId = $_SESSION['member_id'];
$patientId = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;

// Get patient details
$patient = null;
if ($patientId) {
    $patient = getPatientById($conn, $patientId, $memberId);
}

if (!$patient) {
    header("Location: index.php");
    exit;
}

// Get patient transcriptions
$transcriptions = getPatientTranscriptions($conn, $patientId, $memberId);

// Include header
include('header.php');
?>

<div class="dashboard-container">
    <!-- Patient Header -->
    <section class="patient-header" style="background: linear-gradient(135deg, var(--primary-600), var(--secondary-600)); color: white; padding: var(--space-8); margin-bottom: var(--space-8); border-radius: var(--radius-2xl);">
        <div style="display: flex; align-items: center; gap: var(--space-6);">
            <div class="patient-avatar-large" style="width: 100px; height: 100px; border-radius: var(--radius-full); background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: var(--text-3xl); font-weight: 700;">
                <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
            </div>
            <div style="flex: 1;">
                <h1 style="color: white; margin-bottom: var(--space-2);">
                    <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                </h1>
                <div style="display: flex; gap: var(--space-6); font-size: var(--text-lg); opacity: 0.95;">
                    <span><strong>MR#:</strong> <?php echo htmlspecialchars($patient['mr_number']); ?></span>
                    <?php if ($patient['date_of_birth']): ?>
                        <span><strong>DOB:</strong> <?php echo date('M d, Y', strtotime($patient['date_of_birth'])); ?></span>
                        <span><strong>Age:</strong> <?php echo calculateAge($patient['date_of_birth']); ?> years</span>
                    <?php endif; ?>
                    <?php if ($patient['gender']): ?>
                        <span><strong>Gender:</strong> <?php echo htmlspecialchars($patient['gender']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <button onclick="window.location.href='index.php'" class="btn btn-ghost">
                    <span class="material-icons-round">arrow_back</span>
                    Back to MedicalVoice
                </button>
            </div>
        </div>
    </section>

    <!-- Patient Information Cards -->
    <section class="patient-info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--space-6); margin-bottom: var(--space-8);">
        <!-- Contact Information -->
        <div class="info-card" style="background: white; border-radius: var(--radius-xl); padding: var(--space-6); box-shadow: var(--shadow-sm); border: 1px solid var(--neutral-200);">
            <h3 style="display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-4); color: var(--neutral-900);">
                <span class="material-icons-round" style="color: var(--primary-500);">contact_phone</span>
                Contact Information
            </h3>
            <div style="space-y: var(--space-3);">
                <?php if ($patient['phone']): ?>
                    <div style="margin-bottom: var(--space-2);">
                        <strong style="color: var(--neutral-600);">Phone:</strong> <?php echo htmlspecialchars($patient['phone']); ?>
                    </div>
                <?php endif; ?>
                <?php if ($patient['email']): ?>
                    <div style="margin-bottom: var(--space-2);">
                        <strong style="color: var(--neutral-600);">Email:</strong> <?php echo htmlspecialchars($patient['email']); ?>
                    </div>
                <?php endif; ?>
                <?php if ($patient['address']): ?>
                    <div style="margin-bottom: var(--space-2);">
                        <strong style="color: var(--neutral-600);">Address:</strong> <?php echo htmlspecialchars($patient['address']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!$patient['phone'] && !$patient['email'] && !$patient['address']): ?>
                    <p style="color: var(--neutral-500);">No contact information available</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="info-card" style="background: white; border-radius: var(--radius-xl); padding: var(--space-6); box-shadow: var(--shadow-sm); border: 1px solid var(--neutral-200);">
            <h3 style="display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-4); color: var(--neutral-900);">
                <span class="material-icons-round" style="color: var(--primary-500);">medical_information</span>
                Medical Information
            </h3>
            <div>
                <?php if ($patient['blood_type']): ?>
                    <div style="margin-bottom: var(--space-2);">
                        <strong style="color: var(--neutral-600);">Blood Type:</strong> <?php echo htmlspecialchars($patient['blood_type']); ?>
                    </div>
                <?php endif; ?>
                <?php if ($patient['allergies']): ?>
                    <div style="margin-bottom: var(--space-2);">
                        <strong style="color: var(--neutral-600);">Allergies:</strong> <?php echo htmlspecialchars($patient['allergies']); ?>
                    </div>
                <?php endif; ?>
                <?php if ($patient['medical_conditions']): ?>
                    <div style="margin-bottom: var(--space-2);">
                        <strong style="color: var(--neutral-600);">Conditions:</strong> <?php echo htmlspecialchars($patient['medical_conditions']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!$patient['blood_type'] && !$patient['allergies'] && !$patient['medical_conditions']): ?>
                    <p style="color: var(--neutral-500);">No medical information available</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="info-card" style="background: white; border-radius: var(--radius-xl); padding: var(--space-6); box-shadow: var(--shadow-sm); border: 1px solid var(--neutral-200);">
            <h3 style="display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-4); color: var(--neutral-900);">
                <span class="material-icons-round" style="color: var(--error-500);">emergency</span>
                Emergency Contact
            </h3>
            <div>
                <?php if ($patient['emergency_contact']): ?>
                    <div style="margin-bottom: var(--space-2);">
                        <strong style="color: var(--neutral-600);">Name:</strong> <?php echo htmlspecialchars($patient['emergency_contact']); ?>
                    </div>
                <?php endif; ?>
                <?php if ($patient['emergency_phone']): ?>
                    <div style="margin-bottom: var(--space-2);">
                        <strong style="color: var(--neutral-600);">Phone:</strong> <?php echo htmlspecialchars($patient['emergency_phone']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!$patient['emergency_contact'] && !$patient['emergency_phone']): ?>
                    <p style="color: var(--neutral-500);">No emergency contact available</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Transcription History -->
    <section class="transcription-history">
        <h2 style="display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-6); color: var(--neutral-900);">
            <span class="material-icons-round" style="color: var(--primary-500);">history</span>
            Transcription History
            <span style="background: var(--primary-100); color: var(--primary-700); padding: var(--space-1) var(--space-3); border-radius: var(--radius-full); font-size: var(--text-sm); font-weight: 500;">
                <?php echo count($transcriptions); ?> Records
            </span>
        </h2>

        <?php if (empty($transcriptions)): ?>
            <div style="background: var(--neutral-50); border: 1px solid var(--neutral-200); border-radius: var(--radius-xl); padding: var(--space-8); text-align: center;">
                <span class="material-icons-round" style="font-size: 48px; color: var(--neutral-400); margin-bottom: var(--space-4);">mic_none</span>
                <p style="color: var(--neutral-600); font-size: var(--text-lg);">No transcriptions found for this patient</p>
                <button onclick="window.location.href='index.php'" class="btn btn-primary" style="margin-top: var(--space-4);">
                    <span class="material-icons-round">add</span>
                    Create First Transcription
                </button>
            </div>
        <?php else: ?>
            <div class="transcription-cards" style="display: grid; gap: var(--space-4);">
                <?php foreach ($transcriptions as $transcription): ?>
                    <div class="transcription-card" style="background: white; border: 1px solid var(--neutral-200); border-radius: var(--radius-xl); padding: var(--space-6); box-shadow: var(--shadow-sm); transition: all var(--transition-normal);">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-4);">
                            <div>
                                <h3 style="color: var(--neutral-900); margin-bottom: var(--space-2);">
                                    <?php echo htmlspecialchars($transcription['audio_filename'] ?? 'Untitled Recording'); ?>
                                </h3>
                                <div style="display: flex; gap: var(--space-4); font-size: var(--text-sm); color: var(--neutral-600);">
                                    <span>
                                        <span class="material-icons-round" style="font-size: 16px; vertical-align: middle;">calendar_today</span>
                                        <?php echo date('M d, Y', strtotime($transcription['created_at'])); ?>
                                    </span>
                                    <span>
                                        <span class="material-icons-round" style="font-size: 16px; vertical-align: middle;">schedule</span>
                                        <?php echo date('h:i A', strtotime($transcription['created_at'])); ?>
                                    </span>
                                    <span>
                                        <span class="material-icons-round" style="font-size: 16px; vertical-align: middle;">
                                            <?php
                                            $status = $transcription['processing_status'] ?? 'pending';
                                            switch($status) {
                                                case 'completed': echo 'check_circle'; break;
                                                case 'processing': echo 'pending'; break;
                                                case 'failed': echo 'error'; break;
                                                default: echo 'hourglass_empty';
                                            }
                                            ?>
                                        </span>
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </div>
                            </div>
                            <div style="display: flex; gap: var(--space-2);">
                                <?php if ($transcription['json_path'] && file_exists($transcription['json_path'])): ?>
                                    <button onclick="viewTranscription(<?php echo $transcription['transcription_id']; ?>)" class="btn btn-sm btn-primary">
                                        <span class="material-icons-round">visibility</span>
                                        View
                                    </button>
                                    <button onclick="downloadTranscription('<?php echo htmlspecialchars($transcription['json_path']); ?>')" class="btn btn-sm btn-secondary">
                                        <span class="material-icons-round">download</span>
                                        Download
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($transcription['transcribed_text']): ?>
                            <div style="background: var(--neutral-50); border-radius: var(--radius-lg); padding: var(--space-4); margin-top: var(--space-4);">
                                <p style="color: var(--neutral-700); line-height: 1.6;">
                                    <?php echo htmlspecialchars(substr($transcription['transcribed_text'], 0, 200)) . '...'; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<script>
function viewTranscription(transcriptionId) {
    // Implement view functionality
    console.log('View transcription:', transcriptionId);
    // Could open a modal or navigate to a detail page
}

function downloadTranscription(jsonPath) {
    // Create download link
    const a = document.createElement('a');
    a.href = 'download_json.php?file=' + encodeURIComponent(jsonPath);
    a.download = 'transcription.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Add hover effect to cards
document.querySelectorAll('.transcription-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
        this.style.boxShadow = 'var(--shadow-md)';
    });

    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'var(--shadow-sm)';
    });
});
</script>

<?php include('footer.php'); ?>