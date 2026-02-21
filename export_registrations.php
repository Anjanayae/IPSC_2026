<?php
/**
 * Export Registrations to CSV
 * IPSC 2026 Registration System
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit;
}

require_once 'config.php';
require_once 'Database.php';

try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT * FROM registrations ORDER BY registration_date DESC");
    $registrations = $stmt->fetchAll();
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=ipsc2026_registrations_' . date('Y-m-d') . '.csv');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Column headers
    $headers = [
        'ID',
        'Prefix',
        'First Name',
        'Last Name',
        'Gender',
        'Phone',
        'Email',
        'Affiliation',
        'Country',
        'Submitted Paper',
        'Abstract ID',
        'Registration Type',
        'Pre-conference School',
        'Payment Reference',
        'Amount Transferred',
        'Payment Status',
        'Remarks',
        'Registration Date',
        'IP Address'
    ];
    
    fputcsv($output, $headers);
    
    // Data rows
    foreach ($registrations as $reg) {
        $row = [
            $reg['id'],
            $reg['prefix'],
            $reg['first_name'],
            $reg['last_name'],
            $reg['gender'],
            $reg['phone'],
            $reg['email'],
            $reg['affiliation'],
            $reg['country'],
            $reg['submitted_paper'],
            $reg['abstract_id'],
            $reg['registration_type'],
            $reg['pre_conference_school'],
            $reg['payment_reference'],
            $reg['amount_transferred'],
            $reg['payment_status'],
            $reg['remarks'],
            $reg['registration_date'],
            $reg['ip_address']
        ];
        
        fputcsv($output, $row);
    }
    
    fclose($output);
    
    // Log activity
    $db->logActivity(
        'export_registrations',
        'registrations',
        null,
        $_SESSION['admin_id'],
        "Exported " . count($registrations) . " registrations"
    );
    
} catch (Exception $e) {
    error_log("Export error: " . $e->getMessage());
    die("Export failed. Please contact administrator.");
}
?>