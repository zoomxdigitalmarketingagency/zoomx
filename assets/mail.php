<?php
// Load PHPMailer (installed via Composer — see README.txt)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// ── Only allow POST ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// ── Sanitize inputs ──────────────────────────────────────────────────────────
$name    = isset($_POST['name'])    ? htmlspecialchars(strip_tags(trim($_POST['name'])))    : '';
$email   = isset($_POST['email'])   ? htmlspecialchars(strip_tags(trim($_POST['email'])))   : '';
$message = isset($_POST['message']) ? htmlspecialchars(strip_tags(trim($_POST['message']))) : '';

// ── Validate ─────────────────────────────────────────────────────────────────
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit;
}

// ── Gmail SMTP credentials ────────────────────────────────────────────────────
define('GMAIL_USER', 'zoomxdigitalmarketingagency@gmail.com'); // Your Gmail
define('GMAIL_PASS', 'xofpxfowoqpkihlt');                // 16-char App Password

// ── Send via PHPMailer ────────────────────────────────────────────────────────
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = GMAIL_USER;
    $mail->Password   = GMAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender & recipient
    $mail->setFrom(GMAIL_USER, 'ZoomX Media Website');
    $mail->addAddress(GMAIL_USER, 'ZoomX Digital Marketing');
    $mail->addReplyTo($email, $name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission from ' . $name;
    $mail->Body    = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;'>
            <div style='background: #111; padding: 20px 30px;'>
                <h2 style='color: #fff; margin: 0;'>New Message — ZoomX Media</h2>
            </div>
            <div style='padding: 30px; background: #f9f9f9;'>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 10px 0; font-weight: bold; color: #555; width: 100px;'>Name</td>
                        <td style='padding: 10px 0; color: #222;'>{$name}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 0; font-weight: bold; color: #555;'>Email</td>
                        <td style='padding: 10px 0; color: #222;'><a href='mailto:{$email}'>{$email}</a></td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 0; font-weight: bold; color: #555; vertical-align: top;'>Message</td>
                        <td style='padding: 10px 0; color: #222; line-height: 1.6;'>" . nl2br($message) . "</td>
                    </tr>
                </table>
            </div>
            <div style='background: #111; padding: 12px 30px; text-align: center;'>
                <small style='color: #888;'>Sent from zoomxmedia.in contact form</small>
            </div>
        </div>
    ";
    $mail->AltBody = "Name: {$name}\nEmail: {$email}\nMessage:\n{$message}";

    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully!']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Message could not be sent. Please try again later.']);
}
?>
