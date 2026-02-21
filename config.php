
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


/**
 * Database Configuration File
 * IPSC 2026 Registration System
 * 
 * IMPORTANT: Update these values with your actual database credentials
 */

// Database configuration


// Database configuration for XAMPP
define('DB_HOST', 'localhost');          // Database host (usually localhost for XAMPP)
define('DB_NAME', 'admin_ipsc');
define('DB_USER', 'admin_ipsc');           // XAMPP default
define('DB_PASS', '9mCMNmULaqKzdk3');               // XAMPP default is blank
define('DB_CHARSET', 'utf8mb4');


// Security
define('ENABLE_CSRF', false); // disable for now locally

// define('MAX_ATTEMPTS', 20);        // rate limit attempts
// define('BLOCK_DURATION', 300); 
// Site configuration
define('SITE_URL', ' https://ipsc2026.iiti.ac.in');
define('SITE_NAME', 'IPSC 2026');
define('ADMIN_EMAIL', 'ipsc2026@iiti.ac.in');

// Email configuration (disable SMTP for local testing)
define('SMTP_ENABLED', false);       // Set to false for testing
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_SECURE', 'tls');

// Rest of the config stays the same...




// define('DB_HOST', 'localhost');          // Database host (usually localhost for Virtualmin)
// define('DB_NAME', 'ipsc2026');           // Database name
// define('DB_USER', 'ipsc2026_user');      // Database username
// define('DB_PASS', 'your_password_here'); // Database password
// define('DB_CHARSET', 'utf8mb4');

// Site configuration
// define('SITE_URL', 'https://yourdomain.com'); // Your website URL
// define('SITE_NAME', 'IPSC 2026');
// define('ADMIN_EMAIL', 'admin@yourdomain.com'); // Admin email for notifications

// Email configuration (for sending confirmation emails)
// define('SMTP_ENABLED', false);           // Set to true to use SMTP
// define('SMTP_HOST', 'smtp.gmail.com');
// define('SMTP_PORT', 587);
// define('SMTP_USERNAME', 'your-email@gmail.com');
// define('SMTP_PASSWORD', 'your-app-password');
// define('SMTP_SECURE', 'tls');            // tls or ssl

// Security settings
define('ENABLE_RATE_LIMIT', true);       // Enable rate limiting
define('MAX_ATTEMPTS', 5);               // Max form submissions per hour
define('BLOCK_DURATION', 3600);          // Block duration in seconds (1 hour)

// File upload settings (if needed in future)
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5242880);        // 5MB in bytes

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', 0);     // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Lax');

?>