<?php
header('Content-Type: application/json');
require '../database.php';

$secretKey = getenv("SECRET_KEY") ?: "be56e057f20f883e"; // 从环境变量加载密钥

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username'], $data['password'], $data['timestamp'], $data['signature'])) {
    $username = $data['username'];
    $password = $data['password'];
    $timestamp = $data['timestamp'];
    $clientSignature = $data['signature'];

    // 重新生成签名
    $dataToSign = $username . $password . $timestamp;
    $serverSignature = hash_hmac('sha256', $dataToSign, $secretKey);

    // 验证签名是否一致
    if (hash_equals($serverSignature, $clientSignature)) {
        // 签名匹配，继续验证用户名和密码
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 使用 MD5 验证密码
        $hashedPassword = md5($password); // 对输入密码进行 MD5 哈希
        if ($user && $hashedPassword === $user['password']) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Invalid username or password"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Signature mismatch"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Missing data"]);
}
