<?php
header('Content-Type: application/json');
require '../database.php';
session_start();
// 使用 DES 解密密码
function decryptDES($encryptedData, $key, $iv) {
    // 将 Hex 格式的加密数据转换为二进制
    $ciphertext = hex2bin($encryptedData);

    // 使用 DES 解密数据
    $decrypted = openssl_decrypt($ciphertext, 'DES-CBC', $key, OPENSSL_RAW_DATA, $iv);

    // 如果解密失败，返回 null
    return $decrypted !== false ? $decrypted : null;
}

// 解析 POST 数据
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username']) && isset($data['password'])) {
    $username = $data['username'];
    $encryptedPassword = $data['password'];

    // 密钥使用用户名拼接 '66666666' 补齐至 8 位
    $key = substr($username, 0, 8) . str_repeat('6', 8 - strlen($username));

    // IV 使用 '9999' 拼接用户名前4个字符补齐至 8 位
    $iv = '9999' . substr($username, 0, 4); // IV = '9999' + 前4个字符

    // 解密密码
    $decryptedPassword = decryptDES($encryptedPassword, $key, $iv);

    // 如果密码解密失败
    if ($decryptedPassword === null) {
        echo json_encode(["success" => false, "error" => "Decryption failed"]);
        exit();
    }

    // 在这里进行数据库验证
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 验证解密后的密码
        // 假设数据库中密码使用 MD5 存储
        if ($user['password'] === md5($decryptedPassword)) {
            // 密码验证成功
            echo json_encode(["success" => true]);
        } else {
            // 密码错误
            echo json_encode(["success" => false, "error" => "Invalid username or password"]);
        }
    } else {
        // 用户名不存在
        echo json_encode(["success" => false, "error" => "User not found"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Missing data"]);
}
