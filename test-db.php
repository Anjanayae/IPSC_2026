<?php
require_once 'config.php';
require_once 'Database.php';

try {
    $db = Database::getInstance();
    echo "✓ Database connection successful!<br>";
    
    // Check if tables exist
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    echo "<br>Tables found:<br>";
    foreach ($tables as $table) {
        echo "- " . $table['Tables_in_ipsc2026'] . "<br>";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
