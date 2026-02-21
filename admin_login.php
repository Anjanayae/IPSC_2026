<?php
/**
 * Admin Login Handler
 * IPSC 2026 Registration System
 */

session_start();

require_once 'config.php';
require_once 'Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please enter username and password']);
    exit;
}

try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT * FROM admin_users WHERE username = ?", [$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        // Login successful
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        
        // Update last login
        $db->update('admin_users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
        
        // Log activity
        $db->logActivity('admin_login', null, null, $user['id'], "Admin logged in: {$username}");
        
        echo json_encode(['success' => true]);
    } else {
        // Login failed
        sleep(1); // Prevent brute force
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>