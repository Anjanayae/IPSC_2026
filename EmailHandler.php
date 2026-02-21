<?php
/**
 * Email Handler Class
 * IPSC 2026 Registration System
 */

class EmailHandler {
    private $from_email;
    private $from_name;
    private $smtp_enabled;
    
    public function __construct() {
        $this->from_email = ADMIN_EMAIL;
        $this->from_name = SITE_NAME;
        $this->smtp_enabled = SMTP_ENABLED;
    }
    
    /**
     * Send registration confirmation email
     */
    public function sendRegistrationConfirmation($registration) {
        $to = $registration['email'];
        $subject = "Registration Confirmation - IPSC 2026";
        
        $message = $this->getConfirmationTemplate($registration);
        
        return $this->sendEmail($to, $subject, $message);
    }
    
    /**
     * Send approval notification
     */
    public function sendApprovalEmail($registration) {
        $to = $registration['email'];
        $subject = "Registration Approved - IPSC 2026";
        
        $message = $this->getApprovalTemplate($registration);
        
        return $this->sendEmail($to, $subject, $message);
    }
    
    /**
     * Send rejection notification
     */
    public function sendRejectionEmail($registration, $reason = '') {
        $to = $registration['email'];
        $subject = "Registration Status - IPSC 2026";
        
        $message = $this->getRejectionTemplate($registration, $reason);
        
        return $this->sendEmail($to, $subject, $message);
    }
    
    /**
     * Get confirmation email template
     */
    private function getConfirmationTemplate($reg) {
        $registrationId = str_pad($reg['id'], 6, '0', STR_PAD_LEFT);
        
        $html = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
        .reg-id { font-size: 32px; font-weight: bold; color: #6366f1; text-align: center; margin: 20px 0; padding: 15px; background: white; border-radius: 8px; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #6366f1; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; color: #666; }
        .value { color: #333; }
        .status-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 8px; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Registration Received!</h1>
            <p>Indian Planetary Science Conference 2026</p>
        </div>
        <div class='content'>
            <p>Dear {$reg['prefix']} {$reg['first_name']} {$reg['last_name']},</p>
            
            <p>Thank you for registering for IPSC 2026. We have successfully received your registration.</p>
            
            <div class='reg-id'>
                Registration ID: IPSC-{$registrationId}
            </div>
            
            <p><strong>Please save this Registration ID for future reference.</strong></p>
            
            <div class='status-box'>
                <strong>⏳ Status: PENDING VERIFICATION</strong><br>
                Your registration is currently under review. We will verify your payment details and send you a confirmation email within 2-3 business days.
            </div>
            
            <div class='info-box'>
                <h3 style='margin-top: 0; color: #6366f1;'>Registration Details</h3>
                <div class='info-row'><span class='label'>Name:</span> <span class='value'>{$reg['prefix']} {$reg['first_name']} {$reg['last_name']}</span></div>
                <div class='info-row'><span class='label'>Email:</span> <span class='value'>{$reg['email']}</span></div>
                <div class='info-row'><span class='label'>Phone:</span> <span class='value'>{$reg['phone']}</span></div>
                <div class='info-row'><span class='label'>Registration Type:</span> <span class='value'>{$reg['registration_type']}</span></div>
                <div class='info-row'><span class='label'>Payment Status:</span> <span class='value'>{$reg['payment_status']}</span></div>
                <div class='info-row'><span class='label'>Payment Reference:</span> <span class='value'>{$reg['payment_reference']}</span></div>
                <div class='info-row'><span class='label'>Amount:</span> <span class='value'>Rs. {$reg['amount_transferred']}</span></div>
            </div>
            
            <div class='info-box'>
                <h3 style='margin-top: 0; color: #6366f1;'>What's Next?</h3>
                <ol>
                    <li>Our team will verify your payment details</li>
                    <li>You will receive an approval/status update email within 2-3 business days</li>
                    <li>You can check your registration status anytime at: " . SITE_URL . "/check-status.html</li>
                    <li>Use your Registration ID (IPSC-{$registrationId}) and email to check status</li>
                </ol>
            </div>
            
            <p>If you have any questions, please contact us at <a href='mailto:" . ADMIN_EMAIL . "'>" . ADMIN_EMAIL . "</a></p>
            
            <div class='footer'>
                <p><strong>IPSC 2026</strong><br>
                Department of Astronomy, Astrophysics and Space Engineering<br>
                IIT Indore</p>
            </div>
        </div>
    </div>
</body>
</html>
";
        
        return $html;
    }
    
    /**
     * Get approval email template
     */
    private function getApprovalTemplate($reg) {
        $registrationId = str_pad($reg['id'], 6, '0', STR_PAD_LEFT);
        
        $html = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
        .success-box { background: #d1fae5; border-left: 4px solid #10b981; padding: 20px; margin: 20px 0; border-radius: 8px; text-align: center; }
        .success-icon { font-size: 48px; color: #10b981; }
        .reg-id { font-size: 24px; font-weight: bold; color: #10b981; margin: 10px 0; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>✓ Registration Approved!</h1>
            <p>Indian Planetary Science Conference 2026</p>
        </div>
        <div class='content'>
            <div class='success-box'>
                <div class='success-icon'>✓</div>
                <h2 style='color: #10b981; margin: 10px 0;'>Congratulations!</h2>
                <p style='margin: 10px 0;'>Your registration has been approved</p>
                <div class='reg-id'>Registration ID: IPSC-{$registrationId}</div>
            </div>
            
            <p>Dear {$reg['prefix']} {$reg['first_name']} {$reg['last_name']},</p>
            
            <p>We are pleased to inform you that your registration for IPSC 2026 has been <strong>approved</strong>. Your payment has been verified and your seat is confirmed.</p>
            
            <div class='info-box'>
                <h3 style='margin-top: 0; color: #10b981;'>Conference Details</h3>
                <p><strong>Event:</strong> Indian Planetary Science Conference 2026<br>
                <strong>Dates:</strong> 23-25 March 2026<br>
                <strong>Venue:</strong> IIT Indore<br>
                <strong>Your Registration ID:</strong> IPSC-{$registrationId}</p>
            </div>
            
            <div class='info-box'>
                <h3 style='margin-top: 0; color: #6366f1;'>Important Information</h3>
                <ul>
                    <li>Please bring a valid ID proof and this email on the conference day</li>
                    <li>Registration desk will open at 8:00 AM on March 23, 2026</li>
                    <li>Further details about the schedule will be shared closer to the event</li>
                    <li>Visit our website for updates: " . SITE_URL . "</li>
                </ul>
            </div>
            
            <p>We look forward to seeing you at the conference!</p>
            
            <p>For any queries, please contact us at <a href='mailto:" . ADMIN_EMAIL . "'>" . ADMIN_EMAIL . "</a></p>
            
            <div class='footer'>
                <p><strong>IPSC 2026</strong><br>
                Department of Astronomy, Astrophysics and Space Engineering<br>
                IIT Indore</p>
            </div>
        </div>
    </div>
</body>
</html>
";
        
        return $html;
    }
    
    /**
     * Get rejection email template
     */
    private function getRejectionTemplate($reg, $reason) {
        $registrationId = str_pad($reg['id'], 6, '0', STR_PAD_LEFT);
        
        $html = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #ef4444; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Registration Status Update</h1>
            <p>Indian Planetary Science Conference 2026</p>
        </div>
        <div class='content'>
            <p>Dear {$reg['prefix']} {$reg['first_name']} {$reg['last_name']},</p>
            
            <p>Thank you for your interest in IPSC 2026.</p>
            
            <p>We regret to inform you that we are unable to approve your registration at this time.</p>
            
            <div class='info-box'>
                <h3 style='margin-top: 0; color: #ef4444;'>Reason:</h3>
                <p>" . ($reason ?: 'Payment verification failed or incomplete information provided.') . "</p>
            </div>
            
            <p><strong>What you can do:</strong></p>
            <ul>
                <li>If you believe this is an error, please contact us with your Registration ID: IPSC-{$registrationId}</li>
                <li>You may re-register with correct information/payment details</li>
                <li>Contact us for any clarifications</li>
            </ul>
            
            <p>Please contact us at <a href='mailto:" . ADMIN_EMAIL . "'>" . ADMIN_EMAIL . "</a> if you have any questions.</p>
            
            <div class='footer'>
                <p><strong>IPSC 2026</strong><br>
                Department of Astronomy, Astrophysics and Space Engineering<br>
                IIT Indore</p>
            </div>
        </div>
    </div>
</body>
</html>
";
        
        return $html;
    }
    
    /**
     * Send email
     */
    private function sendEmail($to, $subject, $htmlMessage) {
        if (!$this->smtp_enabled) {
            // Use PHP mail()
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: {$this->from_name} <{$this->from_email}>" . "\r\n";
            $headers .= "Reply-To: {$this->from_email}" . "\r\n";
            
            return mail($to, $subject, $htmlMessage, $headers);
        } else {
            // Use SMTP (PHPMailer implementation would go here)
            return $this->sendSMTPEmail($to, $subject, $htmlMessage);
        }
    }
    
    /**
     * Send via SMTP (basic implementation)
     */
    private function sendSMTPEmail($to, $subject, $htmlMessage) {
        // This is a placeholder for SMTP implementation
        // You would use PHPMailer or similar library here
        
        // For now, fall back to mail()
        return $this->sendEmail($to, $subject, $htmlMessage);
    }
}
?>