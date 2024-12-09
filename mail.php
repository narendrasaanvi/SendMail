<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Function to load and populate the email template
function loadTemplate($templateFile, $placeholders)
{
    if (!file_exists($templateFile)) {
        throw new Exception("Template file not found.");
    }

    $templateContent = file_get_contents($templateFile);
    foreach ($placeholders as $key => $value) {
        $templateContent = str_replace("{{" . $key . "}}", htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), $templateContent);
    }
    return $templateContent;
}

// Set JSON response header
header('Content-Type: application/json');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate and sanitize input
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    if (!$email) {
        echo json_encode(["message" => "Invalid email address."]);
        exit;
    }

    // Define placeholders and their values
    $placeholders = [
        'enq_name' => $name,
        'enq_mobile' => $mobile,
        'enq_email' => $email,
        'message' => $message
    ];

    $mail = new PHPMailer(true);

    try {
        // Load email template and replace placeholders
        $emailBody = loadTemplate('email_template.html', $placeholders);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'brandeducer.store';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'test@brandeducer.store';
        $mail->Password   = 'l@D83#.F?3*D';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SMTPS for port 465
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('test@brandeducer.store', 'Mailer');
        $mail->addAddress('test@brandeducer.store', 'Recipient Name');
        $mail->addBCC('test@brandeducer.store', 'BCC Recipient Name');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Enquiry';
        $mail->Body    = $emailBody;
        $mail->AltBody = strip_tags($emailBody);

        $mail->send();
        echo json_encode(["message" => "Your enquiry has been successfully sent!"]);
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        echo json_encode(["message" => $mail->ErrorInfo]);
    }
} else {
    echo json_encode(["message" => "Invalid request method"]);
}
