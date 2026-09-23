<?php
// Load main configuration
require_once 'config.php';

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . ERROR_REDIRECT_VOLUNTEER . "invalid_method");
    exit();
}

// Use configuration values
$recipient_email = RECIPIENT_EMAIL;
$from_email = FROM_EMAIL;
$website_name = WEBSITE_NAME;
$npo_reg_number = NPO_REG_NUMBER;
$success_url = SUCCESS_REDIRECT_VOLUNTEER;
$error_url = ERROR_REDIRECT_VOLUNTEER;

// Sanitize and validate all possible volunteer form fields
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$availability = isset($_POST['availability']) ? trim($_POST['availability']) : '';
$skills = isset($_POST['skills']) ? trim($_POST['skills']) : '';
$interest = isset($_POST['interest']) ? trim($_POST['interest']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$consent = isset($_POST['consent']) ? true : false;

// Validation
$errors = [];

if (empty($fullname)) $errors[] = "name_required";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "invalid_email";
if (empty($phone)) $errors[] = "phone_required";
if (empty($availability)) $errors[] = "availability_required";
if (empty($interest)) $errors[] = "interest_required";
if (!$consent) $errors[] = "consent_required";

if (!empty($errors)) {
    header("Location: " . $error_url . implode(",", $errors));
    exit();
}

// Prepare email content for admin
$admin_subject = "New Volunteer Application - " . $website_name;
$admin_message = "A new volunteer application has been submitted from the Manatha Foundation website.\n\n";
$admin_message .= "Applicant Details:\n";
$admin_message .= "-----------------\n";
$admin_message .= "Full Name: " . $fullname . "\n";
$admin_message .= "Email: " . $email . "\n";
$admin_message .= "Phone: " . $phone . "\n";
$admin_message .= "Location: " . $location . "\n";
$admin_message .= "Date Submitted: " . date("Y-m-d H:i:s") . "\n\n";
$admin_message .= "Availability: " . $availability . "\n";
$admin_message .= "Skills: " . $skills . "\n";
$admin_message .= "Area of Interest: " . $interest . "\n\n";
$admin_message .= "Additional Message:\n";
$admin_message .= "------------------\n";
$admin_message .= $message . "\n\n";
$admin_message .= "---\n";
$admin_message .= $website_name . " | NPO REG NO " . $npo_reg_number . "\n";
$admin_message .= "Registered 16 April 2024 | South Africa";

// Email headers
$headers = "From: " . $website_name . " <" . $from_email . ">\r\n";
$headers .= "Reply-To: " . $fullname . " <" . $email . ">\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email to admin
$admin_email_sent = send_email($recipient_email, $admin_subject, $admin_message, $fullname, $email);

// Prepare auto-reply to applicant
$autoresponse_subject = "Thank you for your volunteer application - " . $website_name;
$autoresponse_message = "Dear " . $fullname . ",\n\n";
$autoresponse_message .= "Thank you for applying to volunteer with Manatha Foundation (NPO REG NO " . $npo_reg_number . "). ";
$autoresponse_message .= "We sincerely appreciate your heart, time, and dedication to serving our communities. ";
$autoresponse_message .= "Our team will review your application and contact you as soon as possible to discuss volunteering opportunities.\n\n";
$autoresponse_message .= "Your application has been received with the following details:\n";
$autoresponse_message .= "--------------------------------------------\n";
$autoresponse_message .= "Contact: " . $email . " | " . $phone . "\n";
$autoresponse_message .= "Interest Area: " . $interest . "\n\n";
$autoresponse_message .= "---\n";
$autoresponse_message .= "Official Confirmation: " . $website_name . " | NPO REG NO " . $npo_reg_number . "\n";
$autoresponse_message .= REGISTRATION_DATE . " | " . COUNTRY;

// Send auto-reply
send_email($email, $autoresponse_subject, $autoresponse_message);

// Log the submission
$log_data = $fullname . " | " . $email . " | " . $interest;
log_submission('volunteer', $log_data);

// Redirect to success page
header("Location: " . $success_url);
exit();
?>