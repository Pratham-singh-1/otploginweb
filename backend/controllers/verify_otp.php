<?php
header("Access-Control-Allow-Origin: *"); // or use http://localhost:3000 for tighter control
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");

header("Content-Type: application/json");
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../utils/function.php";
require_once __DIR__ . "/../utils/sms_service.php";

$data = json_decode(file_get_contents("php://input"), true);
$mobile = sanitize($data['mobile'] ?? '');
$otp = sanitize($data['otp'] ?? '');

if (!$mobile || !$otp) jsonResponse("fail", "Mobile and OTP required");

$stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE mobile=?");
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && $row['otp'] == $otp && strtotime($row['otp_expiry']) > time()) {
    $conn->query("UPDATE users SET is_verified=1 WHERE mobile='$mobile'");
    jsonResponse("success", "OTP verified");
} else {
    jsonResponse("fail", "Invalid or expired OTP");
}
