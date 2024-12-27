<?php
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Suppress warnings and log errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_reporting(0);
header('Content-Type: application/json');

// Debug log file
$logFile = 'debug.log';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Log request method
        file_put_contents($logFile, "Request method: " . $_SERVER['REQUEST_METHOD'] . PHP_EOL, FILE_APPEND);

        // Read and decode JSON input
        $rawInput = file_get_contents('php://input');
        file_put_contents($logFile, "Raw input: $rawInput" . PHP_EOL, FILE_APPEND);

        $input = json_decode($rawInput, true);
        if (!$input) {
            echo json_encode(["status" => "error", "message" => "Invalid JSON input."]);
            exit;
        }

        // Log decoded input
        file_put_contents($logFile, "Decoded input: " . print_r($input, true) . PHP_EOL, FILE_APPEND);

        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $sessionDetails = $input['details'] ?? '';

        // Generate QR code
        $qrCode = new QrCode("Session Details:\nName: $name\nDetails: $sessionDetails");
        $qrCodePath = "qr_codes/" . uniqid() . ".png";
        $qrCode->writeFile($qrCodePath);

        // Log QR code path
        file_put_contents($logFile, "QR Code Path: $qrCodePath" . PHP_EOL, FILE_APPEND);

        // Configure and send email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '';
        $mail->Password = '';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('', 'Conference Team');
        $mail->addAddress($email, $name);
        $mail->addAttachment($qrCodePath);

        $mail->isHTML(true);
        $mail->Subject = "Session Registration Confirmation";
        $mail->Body = "
            <p>Dear $name,</p>
            <p>Thank you for registering for the session.</p>
            <p><strong>Details:</strong></p>
            <p>$sessionDetails</p>
            <p>Please find your QR code attached.</p>
        ";

        $mail->send();

        echo json_encode(["status" => "success", "message" => "Email sent successfully."]);
    } catch (Exception $e) {
        // Log exception
        file_put_contents($logFile, "Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
