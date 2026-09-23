<?php
// Load main configuration
require_once 'config.php';

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . ERROR_REDIRECT_CONTACT . "invalid_method");
    exit();
}

// Use configuration values
$recipient_email = RECIPIENT_EMAIL;
$from_email = FROM_EMAIL;
$website_name = WEBSITE_NAME;
$npo_reg_number = NPO_REG_NUMBER;
$success_url = SUCCESS_REDIRECT_CONTACT;
$error_url = ERROR_REDIRECT_CONTACT;

// Sanitize and validate input
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$consent = isset($_POST['consent']) ? true : false;

// Validation
$errors = [];

if (empty($name)) $errors[] = "name_required";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "invalid_email";
if (empty($subject)) $errors[] = "subject_required";
if (empty($message)) $errors[] = "message_required";
if (!$consent) $errors[] = "consent_required";

if (!empty($errors)) {
    header("Location: " . $error_url . implode(",", $errors));
    exit();
}

// Prepare email content for admin
$admin_subject = "New Contact Enquiry - " . $website_name . ": " . $subject;
$admin_message = "A new contact enquiry has been submitted from the Manatha Foundation website.\n\n";
$admin_message .= "Contact Details:\n";
$admin_message .= "---------------\n";
$admin_message .= "Name: " . $name . "\n";
$admin_message .= "Email: " . $email . "\n";
$admin_message .= "Subject: " . $subject . "\n";
$admin_message .= "Date Submitted: " . date("Y-m-d H:i:s") . "\n\n";
$admin_message .= "Message:\n";
$admin_message .= "-------\n";
$admin_message .= $message . "\n\n";
$admin_message .= "---\n";
$admin_message .= $website_name . " | NPO REG NO " . $npo_reg_number . "\n";
$admin_message .= "Registered 16 April 2024 | South Africa";

// Email headers
$headers = "From: " . $website_name . " <" . $from_email . ">\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email to admin
$admin_email_sent = send_email($recipient_email, $admin_subject, $admin_message, $name, $email);

// Prepare auto-reply to sender
$autoresponse_subject = "Thank you for contacting " . $website_name;
$autoresponse_message = "Dear " . $name . ",\n\n";
$autoresponse_message .= "Thank you for contacting Manatha Foundation (NPO REG NO " . $npo_reg_number . "). ";
$autoresponse_message .= "We deeply appreciate your message. Our team has received your enquiry and we will get back to you as soon as possible.\n\n";
$autoresponse_message .= "Your submitted message:\n";
$autoresponse_message .= "---------------------\n";
$autoresponse_message .= "Subject: " . $subject . "\n";
$autoresponse_message .= $message . "\n\n";
$autoresponse_message .= "---\n";
$autoresponse_message .= "Official Confirmation: " . $website_name . " | NPO REG NO " . $npo_reg_number . "\n";
$autoresponse_message .= REGISTRATION_DATE . " | " . COUNTRY;

// Send auto-reply
send_email($email, $autoresponse_subject, $autoresponse_message);

// Log the submission
$log_data = $name . " | " . $email . " | " . $subject;
log_submission('contact', $log_data);

// Redirect to success page
header("Location: " . $success_url);
exit();
?>