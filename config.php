<?php
/**
 * Manatha Foundation - Main Configuration File
 * Contains all shared configuration settings for website functionality
 */

// Foundation Core Configuration
define('WEBSITE_NAME', 'Manatha Foundation');
define('NPO_REG_NUMBER', '306-164');
define('REGISTRATION_DATE', '16 April 2024');
define('COUNTRY', 'South Africa');

// Email Configuration
define('RECIPIENT_EMAIL', 'manathafoundation@gmail.com');
define('FROM_EMAIL', 'noreply@majitatalking.co.za');
define('WEBSITE_EMAIL', 'manathafoundation@gmail.com');
define('PHONE_NUMBER', '017 826 0502');

// URLs Configuration
define('SUCCESS_REDIRECT_CONTACT', 'contact.html?success=1');
define('ERROR_REDIRECT_CONTACT', 'contact.html?error=');
define('SUCCESS_REDIRECT_VOLUNTEER', 'volunteer.html?success=1');
define('ERROR_REDIRECT_VOLUNTEER', 'volunteer.html?error=');

// File Paths
define('LOG_DIRECTORY', __DIR__ . '/submissions/');
define('CONTACT_LOG_FILE', LOG_DIRECTORY . 'contact_logs.txt');
define('VOLUNTEER_LOG_FILE', LOG_DIRECTORY . 'volunteer_logs.txt');

// Security Configuration
define('ENABLE_DEBUG', true); // Set to false in production

// Error Reporting Configuration
if (ENABLE_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

/**
 * Helper function to log submissions
 */
function log_submission($type, $data) {
    $log_entry = date("Y-m-d H:i:s") . " | " . strtoupper($type) . " | " . $data . "\n";
    $log_file = ($type === 'contact') ? CONTACT_LOG_FILE : VOLUNTEER_LOG_FILE;
    
    if (file_exists(LOG_DIRECTORY)) {
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
}

/**
 * Helper function to send emails
 */
function send_email($to, $subject, $message, $reply_to_name = '', $reply_to_email = '') {
    $headers = "From: " . WEBSITE_NAME . " <" . FROM_EMAIL . ">\r\n";
    if (!empty($reply_to_name) && !empty($reply_to_email)) {
        $headers .= "Reply-To: " . $reply_to_name . " <" . $reply_to_email . ">\r\n";
    }
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Helper function to validate required fields
 */
function validate_fields($fields, $post_data) {
    $errors = [];
    foreach ($fields as $field => $validation) {
        $value = isset($post_data[$field]) ? trim($post_data[$field]) : '';
        if (empty($value)) {
            $errors[] = $field . "_required";
        } elseif (isset($validation['email']) && $validation['email'] === true) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "invalid_" . $field;
            }
        }
    }
    return $errors;
}
?>