<?php
/**
 * Registration Form Handler
 * IPSC 2026 Registration System
 */

require_once 'config.php';
require_once 'Database.php';
require_once 'FormValidator.php';
require_once 'EmailHandler.php';

header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($success, $message, $data = null, $errors = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'errors' => $errors
    ]);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method');
}

// Verify CSRF token
if (defined('ENABLE_CSRF') && ENABLE_CSRF) {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!SecurityHelper::verifyCSRFToken($csrfToken)) {
        sendResponse(false, 'Invalid security token. Please refresh the page and try again.');
    }
}

// Check rate limiting
$clientIP = SecurityHelper::getClientIP();
SecurityHelper::checkRateLimit(
    $clientIP,
    defined('MAX_ATTEMPTS') ? MAX_ATTEMPTS : 20,
    defined('BLOCK_DURATION') ? BLOCK_DURATION : 300
);

// if (!SecurityHelper::checkRateLimit($clientIP, MAX_ATTEMPTS, BLOCK_DURATION)) {
//     sendResponse(false, 'Too many registration attempts. Please try again later.');
// }

// Initialize validator
$validator = new FormValidator($_POST);

// Define validation rules
$validationRules = [
    'prefix' => [
        ['rule' => 'required', 'message' => 'Prefix is required'],
        ['rule' => 'in', 'values' => ['Prof.', 'Dr.', 'Mr.', 'Ms.', 'Mrs'], 'message' => 'Invalid prefix']
    ],
    'first_name' => [
        ['rule' => 'required', 'message' => 'First name is required'],
        ['rule' => 'min_length', 'value' => 2, 'message' => 'First name must be at least 2 characters'],
        ['rule' => 'max_length', 'value' => 100, 'message' => 'First name must not exceed 100 characters']
    ],
    'last_name' => [
        ['rule' => 'required', 'message' => 'Last name is required'],
        ['rule' => 'min_length', 'value' => 2, 'message' => 'Last name must be at least 2 characters'],
        ['rule' => 'max_length', 'value' => 100, 'message' => 'Last name must not exceed 100 characters']
    ],
    'gender' => [
        ['rule' => 'required', 'message' => 'Gender is required'],
        ['rule' => 'in', 'values' => ['Male', 'Female', 'Other'], 'message' => 'Invalid gender']
    ],
    'phone' => [
        ['rule' => 'required', 'message' => 'Phone number is required'],
        ['rule' => 'phone', 'message' => 'Invalid phone number format']
    ],
    'email' => [
        ['rule' => 'required', 'message' => 'Email is required'],
        ['rule' => 'email', 'message' => 'Invalid email address']
    ],
    'affiliation' => [
        ['rule' => 'required', 'message' => 'Affiliation is required'],
        ['rule' => 'min_length', 'value' => 10, 'message' => 'Please provide complete affiliation details']
    ],
    'country' => [
        ['rule' => 'required', 'message' => 'Country is required'],
        ['rule' => 'min_length', 'value' => 2, 'message' => 'Invalid country']
    ],
    'submitted_paper' => [
        ['rule' => 'required', 'message' => 'Please specify if you submitted a paper'],
        ['rule' => 'in', 'values' => ['Yes', 'No'], 'message' => 'Invalid selection']
    ],
    'abstract_id' => [
        ['rule' => 'required', 'message' => 'Abstract ID is required (write N/A if not applicable)']
    ],
    'registration_type' => [
        ['rule' => 'required', 'message' => 'Registration type is required'],
        ['rule' => 'in', 'values' => [
            'Students/Research Scholars (Indian)',
            'Faculty/Scientists/Research Staffs (Indian)',
            'Postdoc',
            'Industry'
        ], 'message' => 'Invalid registration type']
    ],
    'payment_reference' => [
        ['rule' => 'required', 'message' => 'Payment reference number is required']
    ],
    'amount_transferred' => [
        ['rule' => 'required', 'message' => 'Amount transferred is required'],
        ['rule' => 'numeric', 'message' => 'Amount must be a number']
    ],
    'payment_status' => [
        ['rule' => 'required', 'message' => 'Payment status is required'],
        ['rule' => 'in', 'values' => ['Already Paid'], 'message' => 'Invalid payment status']
    ]
];

// Validate form data
if (!$validator->validate($validationRules)) {
    sendResponse(false, 'Please correct the errors in the form', null, $validator->getErrors());
}

// Sanitize data
$sanitizeRules = [
    'prefix' => 'string',
    'first_name' => 'string',
    'last_name' => 'string',
    'gender' => 'string',
    'phone' => 'string',
    'email' => 'email',
    'affiliation' => 'html',
    'country' => 'string',
    'submitted_paper' => 'string',
    'abstract_id' => 'string',
    'registration_type' => 'string',
    'payment_reference' => 'string',
    'amount_transferred' => 'float',
    'remarks' => 'html',
    'payment_status' => 'string'
];

$sanitizedData = $validator->sanitize($sanitizeRules);

// Check for duplicate email
try {
    $db = Database::getInstance();
    $existing = $db->select('registrations', ['email' => $sanitizedData['email']]);
    
    if (!empty($existing)) {
        sendResponse(false, 'This email address is already registered. If you need to update your registration, please contact us.');
    }
    
    // Prepare data for insertion
    $registrationData = [
        'prefix' => $sanitizedData['prefix'],
        'first_name' => $sanitizedData['first_name'],
        'last_name' => $sanitizedData['last_name'],
        'gender' => $sanitizedData['gender'],
        'phone' => $sanitizedData['phone'],
        'email' => $sanitizedData['email'],
        'affiliation' => $sanitizedData['affiliation'],
        'country' => $sanitizedData['country'],
        'submitted_paper' => $sanitizedData['submitted_paper'],
        'abstract_id' => $sanitizedData['abstract_id'],
        'registration_type' => $sanitizedData['registration_type'],
        'payment_reference' => $sanitizedData['payment_reference'],
        'amount_transferred' => $sanitizedData['amount_transferred'],
        'remarks' => $sanitizedData['remarks'] ?? null,
        'payment_status' => $sanitizedData['payment_status'],
        'approval_status' => 'pending',
        'ip_address' => $clientIP,
        'user_agent' => SecurityHelper::getUserAgent()
    ];
    
    // Insert into database
    $registrationId = $db->insert('registrations', $registrationData);
    
    // Log activity
    $db->logActivity(
        'registration_created',
        'registrations',
        $registrationId,
        null,
        "New registration: {$sanitizedData['email']}"
    );
    
    // Send confirmation email
    $emailHandler = new EmailHandler();
    $registrationData['id'] = $registrationId;
    $emailSent = $emailHandler->sendRegistrationConfirmation($registrationData);
    
    // Generate friendly registration ID
    $friendlyId = 'IPSC-' . str_pad($registrationId, 6, '0', STR_PAD_LEFT);
    
    sendResponse(true, "Registration successful! Your Registration ID is {$friendlyId}. Please check your email for confirmation and further instructions.", [
        'registration_id' => $friendlyId,
        'email_sent' => $emailSent
    ]);
    
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    sendResponse(false, 'An error occurred during registration. Please try again or contact support.');
}

/**
 * Send confirmation email
 */
function sendConfirmationEmail($email, $firstName, $registrationId) {
    // Email implementation using PHPMailer or native mail()
    // This is a placeholder - implement based on your email requirements
    
    $subject = "Registration Confirmation - IPSC 2026";
    $message = "Dear $firstName,\n\n";
    $message .= "Thank you for registering for IPSC 2026.\n";
    $message .= "Your registration ID is: $registrationId\n\n";
    $message .= "We will contact you with further details soon.\n\n";
    $message .= "Best regards,\nIPSC 2026 Team";
    
    $headers = "From: " . ADMIN_EMAIL . "\r\n";
    $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    mail($email, $subject, $message, $headers);
}

?>