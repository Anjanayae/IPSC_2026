<?php
/**
 * Check Registration Status
 * IPSC 2026 Registration System
 */

require_once 'config.php';
require_once 'Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$registrationId = $_POST['registration_id'] ?? '';
$email = $_POST['email'] ?? '';

if (empty($registrationId) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Please provide both Registration ID and Email']);
    exit;
}

// Extract numeric ID from format IPSC-000123
$numericId = null;
if (preg_match('/IPSC-(\d+)/', $registrationId, $matches)) {
    $numericId = intval($matches[1]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Registration ID format. Use format: IPSC-XXXXXX']);
    exit;
}

try {
    $db = Database::getInstance();
    
    $stmt = $db->query(
        "SELECT * FROM registrations WHERE id = ? AND email = ?",
        [$numericId, $email]
    );
    
    $registration = $stmt->fetch();
    
    if (!$registration) {
        echo json_encode([
            'success' => false,
            'message' => 'Registration not found. Please check your Registration ID and Email.'
        ]);
        exit;
    }
    
    // Format data for display
    $responseData = [
        'registration_id' => 'IPSC-' . str_pad($registration['id'], 6, '0', STR_PAD_LEFT),
        'name' => $registration['prefix'] . ' ' . $registration['first_name'] . ' ' . $registration['last_name'],
        'email' => $registration['email'],
        'registration_type' => $registration['registration_type'],
        'payment_status' => $registration['payment_status'],
        'approval_status' => $registration['approval_status'],
        'registration_date' => date('d M Y, h:i A', strtotime($registration['registration_date'])),
        'rejection_reason' => $registration['rejection_reason'] ?? null
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $responseData
    ]);
    
} catch (Exception $e) {
    error_log("Status check error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while checking status. Please try again.'
    ]);
}
?>