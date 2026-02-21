<?php
/**
 * Form Validator Class
 * IPSC 2026 Registration System
 */

class FormValidator {
    private $errors = [];
    private $data = [];
    
    public function __construct($postData) {
        $this->data = $postData;
    }
    
    public function validate($rules) {
        foreach ($rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? '';
            
            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }
        
        return empty($this->errors);
    }
    
    private function applyRule($field, $value, $rule) {
        $ruleName = $rule['rule'];
        $message = $rule['message'] ?? "Validation failed for $field";
        
        switch ($ruleName) {
            case 'required':
                if (empty(trim($value))) {
                    $this->errors[$field][] = $message;
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = $message;
                }
                break;
                
            case 'phone':
                if (!empty($value) && !preg_match('/^[0-9+\-\s()]{8,20}$/', $value)) {
                    $this->errors[$field][] = $message;
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = $message;
                }
                break;
                
            case 'min_length':
                $min = $rule['value'] ?? 0;
                if (!empty($value) && strlen($value) < $min) {
                    $this->errors[$field][] = $message;
                }
                break;
                
            case 'max_length':
                $max = $rule['value'] ?? 1000;
                if (!empty($value) && strlen($value) > $max) {
                    $this->errors[$field][] = $message;
                }
                break;
                
            case 'in':
                $allowed = $rule['values'] ?? [];
                if (!empty($value) && !in_array($value, $allowed)) {
                    $this->errors[$field][] = $message;
                }
                break;
                
            case 'decimal':
                if (!empty($value) && !preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
                    $this->errors[$field][] = $message;
                }
                break;
        }
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getFirstError($field) {
        return $this->errors[$field][0] ?? null;
    }
    
    public function getAllErrors() {
        $allErrors = [];
        foreach ($this->errors as $field => $fieldErrors) {
            $allErrors = array_merge($allErrors, $fieldErrors);
        }
        return $allErrors;
    }
    
    public function sanitize($rules) {
        $sanitized = [];
        
        foreach ($rules as $field => $sanitizeRule) {
            $value = $this->data[$field] ?? '';
            
            switch ($sanitizeRule) {
                case 'string':
                    $sanitized[$field] = trim(strip_tags($value));
                    break;
                    
                case 'email':
                    $sanitized[$field] = filter_var($value, FILTER_SANITIZE_EMAIL);
                    break;
                    
                case 'int':
                    $sanitized[$field] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                    
                case 'float':
                    $sanitized[$field] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    break;
                    
                case 'html':
                    $sanitized[$field] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                    break;
                    
                default:
                    $sanitized[$field] = $value;
            }
        }
        
        return $sanitized;
    }
}

/**
 * Security Helper Class
 */
class SecurityHelper {
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 3600) {
        if (!ENABLE_RATE_LIMIT) {
            return true;
        }
        
        $key = 'rate_limit_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'attempts' => 1,
                'first_attempt' => time()
            ];
            return true;
        }
        
        $data = $_SESSION[$key];
        $timePassed = time() - $data['first_attempt'];
        
        if ($timePassed > $timeWindow) {
            $_SESSION[$key] = [
                'attempts' => 1,
                'first_attempt' => time()
            ];
            return true;
        }
        
        if ($data['attempts'] >= $maxAttempts) {
            return false;
        }
        
        $_SESSION[$key]['attempts']++;
        return true;
    }
    
    public static function getClientIP() {
        $ipAddress = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        }
        
        return filter_var($ipAddress, FILTER_VALIDATE_IP) ? $ipAddress : '';
    }
    
    public static function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}
?>