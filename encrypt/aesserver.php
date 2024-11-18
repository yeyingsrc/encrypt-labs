<?php
// 引入数据库连接
require '../database.php';
session_start();

// AES 解密函数
function decryptAES($encryptedData) {
    // 从会话中获取 AES 密钥和 IV
    $key = isset($_SESSION['aes_key']) ? base64_decode($_SESSION['aes_key']) : "1234567890123456"; // 替换为动态密钥
    $iv = isset($_SESSION['aes_iv']) ? base64_decode($_SESSION['aes_iv']) : "1234567890123456";   // 替换为动态 IV

    // 解码并解密数据
    $ciphertext = base64_decode($encryptedData);
    return openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取并解析 JSON 数据
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (isset($data['encryptedData'])) {
        // 解密 JSON 数据包
        $decryptedData = decryptAES($data['encryptedData']);

        // 将解密后的 JSON 数据解析为数组
        $formData = json_decode($decryptedData, true);

        // 验证数据格式和内容
        if (isset($formData['username']) && isset($formData['password'])) {
            $username = $formData['username'];
            $password = $formData['password'];

            // 查询用户信息
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 使用 MD5 验证密码
            if ($user && $user['password'] === md5($password)) {
                // 设置会话信息
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = htmlspecialchars($user['username']);

                // 返回成功响应
                echo json_encode(["success" => true]);
            } else {
                // 返回失败响应
                echo json_encode(["success" => false, "error" => "用户名或密码错误"]);
            }
        } else {
            // 输入格式错误
            echo json_encode(["success" => false, "error" => "Invalid input"]);
        }
    } else {
        // 无加密数据
        echo json_encode(["success" => false, "error" => "No encrypted data"]);
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
}
?>
