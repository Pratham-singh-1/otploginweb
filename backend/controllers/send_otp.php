<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../utils/function.php";
require_once __DIR__ . "/../utils/sms_service.php";


// OPTIONAL: Load environment variables if using .env
if (file_exists(__DIR__ . "/../vendor/autoload.php")) {
    require_once __DIR__ . "/../vendor/autoload.php";
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
    $dotenv->load();
}

// Log raw input
$raw_input = file_get_contents("php://input");
file_put_contents("log.txt", "RAW INPUT: $raw_input\n", FILE_APPEND);

// Parse JSON
$data = json_decode($raw_input, true);
if (!$data || !isset($data['mobile'])) {
    jsonResponse("fail", "Invalid input");
}

$mobile = sanitize($data['mobile']);
if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
    jsonResponse("fail", "Invalid Indian mobile number.");
}

// Rate limiting: check cooldown (2 minutes)
$stmt = $conn->prepare("SELECT otp_sent_time FROM users WHERE mobile = ?");
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$current_time = time();
if ($row && strtotime($row['otp_sent_time']) + 120 > $current_time) {
    $remaining = strtotime($row['otp_sent_time']) + 120 - $current_time;
    jsonResponse("fail", "Please wait $remaining seconds before requesting a new OTP.");
}

// Generate OTP
$otp = rand(100000, 999999);
$otp_expiry = date("Y-m-d H:i:s", time() + 300); // 5 min expiry
$sent_time = date("Y-m-d H:i:s");

// Insert or update
$stmt = $conn->prepare("INSERT INTO users (mobile, otp, otp_expiry, otp_sent_time, is_verified)
    VALUES (?, ?, ?, ?, 0)
    ON DUPLICATE KEY UPDATE otp=?, otp_expiry=?, otp_sent_time=?, is_verified=0");
$stmt->bind_param("sssssss", $mobile, $otp, $otp_expiry, $sent_time, $otp, $otp_expiry, $sent_time);
$stmt->execute();

// Send SMS
if (sendSMS($mobile, $otp)) {
    jsonResponse("success", "OTP sent successfully.");
} else {
    jsonResponse("fail", "Failed to send OTP.");
}
