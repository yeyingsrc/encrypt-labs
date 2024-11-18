<?php
// 引入数据库连接
require '../database.php';
session_start();

function decryptAES($encryptedData) {
    $key = "1234567890123456"; // 必须与前端密钥一致
    $iv = "1234567890123456";  // 必须与前端 IV 一致

    // 解码并解密数据
    $ciphertext = base64_decode($encryptedData);
    $decrypted = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $decrypted;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取 URL 查询参数
    $encryptedData = isset($_POST['encryptedData']) ? $_POST['encryptedData'] : null;

    if ($encryptedData) {
        // 解密加密后的数据
        $decryptedData = decryptAES($encryptedData);

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
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = htmlspecialchars($user['username']);

                // 返回成功响应
                echo json_encode(["success" => true]);
            } else {
                // 返回失败响应
                echo json_encode(["success" => false]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Invalid input"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "No encrypted data"]);
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
}
?>
