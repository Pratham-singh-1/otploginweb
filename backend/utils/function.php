<?php
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function jsonResponse($status, $message, $data = []) {
    echo json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}
