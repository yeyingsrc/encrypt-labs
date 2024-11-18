<?php
header('Content-Type: application/json');
require '../database.php';

$secretKey = getenv("SECRET_KEY") ?: "be56e057f20f883e"; // 从环境变量加载密钥

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username'], $data['password'], $data['timestamp'])) {
    $username = $data['username'];
    $password = $data['password'];
    $timestamp = $data['timestamp'];

    // 检查时间戳是否在允许范围内
    $currentTimestamp = time();
    if (abs($currentTimestamp - $timestamp) > 300) { // 5 分钟内
        echo json_encode(["error" => "Request timeout"]);
        exit();
    }

    // 生成签名
    $dataToSign = $username . $password . $timestamp;
    $signature = hash_hmac('sha256', $dataToSign, $secretKey);

    echo json_encode(["signature" => $signature]);
} else {
    echo json_encode(["error" => "Missing data"]);
}
