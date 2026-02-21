<?php
/**
 * Admin Actions Handler
 * Approve/Reject registrations
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once 'config.php';
require_once 'Database.php';
require_once 'EmailHandler.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$action = $_POST['action'] ?? '';
$registrationId = $_POST['registration_id'] ?? 0;
$reason = $_POST['reason'] ?? '';

if (!in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

if (empty($registrationId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid registration ID']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Get registration details
    $stmt = $db->query("SELECT * FROM registrations WHERE id = ?", [$registrationId]);
    $registration = $stmt->fetch();
    
    if (!$registration) {
        echo json_encode(['success' => false, 'message' => 'Registration not found']);
        exit;
    }
    
    if ($registration['approval_status'] !== 'pending') {
        echo json_encode(['success' => false, 'message' => 'Registration has already been processed']);
        exit;
    }
    
    $emailHandler = new EmailHandler();
    
    if ($action === 'approve') {
        // Update status to approved
        $updateData = [
            'approval_status' => 'approved',
            'approved_by' => $_SESSION['admin_id'],
            'approved_at' => date('Y-m-d H:i:s')
        ];
        
        $db->update('registrations', $updateData, 'id = ?', [$registrationId]);
        
        // Send approval email
        $emailSent = $emailHandler->sendApprovalEmail($registration);
        
        // Log activity
        $db->logActivity(
            'registration_approved',
            'registrations',
            $registrationId,
            $_SESSION['admin_id'],
            "Approved registration for {$registration['email']}"
        );
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration approved successfully',
            'email_sent' => $emailSent
        ]);
        
    } else { // reject
        // Update status to rejected
        $updateData = [
            'approval_status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => $_SESSION['admin_id'],
            'approved_at' => date('Y-m-d H:i:s')
        ];
        
        $db->update('registrations', $updateData, 'id = ?', [$registrationId]);
        
        // Send rejection email
        $emailSent = $emailHandler->sendRejectionEmail($registration, $reason);
        
        // Log activity
        $db->logActivity(
            'registration_rejected',
            'registrations',
            $registrationId,
            $_SESSION['admin_id'],
            "Rejected registration for {$registration['email']}"
        );
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration rejected',
            'email_sent' => $emailSent
        ]);
    }
    
} catch (Exception $e) {
    error_log("Admin action error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>