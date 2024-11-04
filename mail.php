<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Function to load and populate the email template
function loadTemplate($templateFile, $placeholders)
{
    $templateContent = file_get_contents($templateFile);
    foreach ($placeholders as $key => $value) {
        $templateContent = str_replace("{{" . $key . "}}", htmlspecialchars($value), $templateContent);
    }
    return $templateContent;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $mobile = filter_var($_POST['mobile'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $product = filter_var($_POST['product'], FILTER_SANITIZE_STRING);

    // Define placeholders and their values
    $placeholders = [
        'enq_name' => $name,
        'enq_mobile' => $mobile,
        'enq_email' => $email,
        'product' => $product
    ];

    $mail = new PHPMailer(true);

    try {
        // Load email template and replace placeholders
        $emailBody = loadTemplate('email_template.html', $placeholders);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'test@nakodagems.com';
        $mail->Password   = 'Narendra@12345';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; // Use 587 for TLS

        // Recipients
        $mail->setFrom('test@nakodagems.com', 'Mailer');
        $mail->addAddress('narendrask786@gmail.com', 'Recipient Name');
        $mail->addBCC('info@tickbytickdata.com', 'BCC Recipient Name');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Enquiry';
        $mail->Body    = $emailBody;
        $mail->AltBody = strip_tags($emailBody);

        $mail->send();
        echo json_encode(["message" => "Your enquiry has been successfully sent!"]);
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        echo json_encode(["message" => "An error occurred while sending your enquiry. Please try again later."]);
    }
} else {
    echo json_encode(["message" => "Invalid request method"]);
}
