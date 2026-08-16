<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please complete all fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

$mail = new PHPMailer(true);

try {
    $gmailAppPassword = 'tqbi msnz vpmt fokk';
    $gmailAppPassword = str_replace(' ', '', trim($gmailAppPassword));

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'inquire.adeel@gmail.com';
    $mail->Password = $gmailAppPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('inquire.adeel@gmail.com', 'Portfolio Contact Form');
    $mail->addReplyTo($email, $name);
    $mail->addAddress('inquire.adeel@gmail.com', 'Muhammad Adeel Shariff');

    $mail->isHTML(true);
    $mail->Subject = 'New Portfolio Inquiry from ' . $name;

    $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 24px; border: 1px solid #e5e7eb; border-radius: 12px;'>
                <h2 style='margin-bottom: 16px; color: #111827;'>New Contact Inquiry</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Message:</strong></p>
                <div style='padding: 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #10b981;'>
                    " . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "
                </div>
                <p style='margin-top: 20px; font-size: 12px; color: #6b7280;'>This email was sent from your portfolio website contact form.</p>
            </div>
        </body>
        </html>
    ";

    $mail->AltBody = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}";

    $mail->send();

    echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent successfully.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to send email right now. Please try again later.']);
}
