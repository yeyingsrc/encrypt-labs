<?php
header('Content-Type: application/json');
require '../database.php';
session_start();
// 定义共享密钥，用于签名和验证。应在客户端和服务器端保持一致
$secretKey = "be56e057f20f883e";

// 解析 POST 数据
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username']) && isset($data['password']) && isset($data['nonce']) && isset($data['timestamp']) && isset($data['signature'])) {
    $username = $data['username'];
    $password = $data['password'];
    $nonce = $data['nonce'];
    $timestamp = $data['timestamp'];
    $clientSignature = $data['signature'];

    // 检查时间戳是否在允许的时间范围内（例如 5 分钟内）
    $currentTimestamp = time();
    if (abs($currentTimestamp - $timestamp) > 500) { // 8秒
        echo json_encode(["success" => false, "error" => "Request timeout"]);
        exit();
    }


    // 生成服务器端的签名
    $dataToSign = $username . $password . $nonce . $timestamp;
    $serverSignature = hash_hmac('sha256', $dataToSign, $secretKey);

    // 验证签名是否一致
    if (hash_equals($serverSignature, $clientSignature)) {
        // 签名匹配，继续数据库验证
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['password'] === md5($password)) {
            // 密码验证成功
            echo json_encode(["success" => true]);
        } else {
            // 密码错误
            echo json_encode(["success" => false, "error" => "Invalid username or password"]);
        }
    } else {
        // 签名不匹配，数据可能被篡改
        echo json_encode(["success" => false, "error" => "Signature mismatch - data tampered"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Missing data"]);
}

// 示例的伪函数：检查 nonce 是否已经存在于数据库
function nonceExistsInDatabase($nonce) {
    // 您可以使用数据库来存储 nonce，例如 Redis 或 SQL 表
    // 这里使用伪代码，实际实现应查询数据库
    return false;
}

// 示例的伪函数：将 nonce 存储到数据库中
function storeNonceInDatabase($nonce) {
    // 将 nonce 存储到数据库，例如 Redis 或 SQL 表
    // 使用合适的过期策略，例如删除 5 分钟前的 nonce
}
