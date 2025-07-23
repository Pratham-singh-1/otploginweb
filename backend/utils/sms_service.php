<?php
require_once __DIR__ . '/config.php';
function sendSMS($mobile, $otp) {
    $apiKey = FAST2SMS_API_KEY;
    $url = "https://www.fast2sms.com/dev/bulkV2";

    $fields = [
        "message" => "Your OTP is $otp",
        "language" => "english",
        "route" => "q",
        "numbers" => $mobile
    ];

    $headers = [
        "authorization: $apiKey",
        "Content-Type: application/x-www-form-urlencoded"
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($fields),
        CURLOPT_HTTPHEADER => $headers
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Log for debugging
    file_put_contents("log.txt", "Fast2SMS RESPONSE: $response\n", FILE_APPEND);

    if ($error) {
        return false;
    }

    $res = json_decode($response, true);
    return isset($res["return"]) && $res["return"] === true;
}
