<?php
header('Content-Type: application/json');
require '../database.php';
session_start();
// 加载 RSA 私钥
$privateKey = "-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDRvA7giwinEkaTYllDYCkzujviNH+up0XAKXQot8RixKGpB7nr
8AdidEvuo+wVCxZwDK3hlcRGrrqt0Gxqwc11btlMDSj92Mr3xSaJcshZU8kfj325
L8DRh9jpruphHBfh955ihvbednGAvOHOrz3Qy3CbocDbsNeCwNpRxwjIdQIDAQAB
AoGAMek68RylFn025mQFMg90PqcXESHFMN8FrlEvH3F7/rUkc4EvMYKRf1CFsWi5
Cdj1ofyidIibiOaT7kEnS9CK//SmY+1628/eyngOvOR9ADsHN/JRlJ3dHathcBrr
1GENlCB9EmN+Fzhh7vEC2RUPrkkHCYGU2j+9rkzHUCXxLpECQQD5jgm9K7bvsOzM
82v6avdNFAV/9ILdple1xlCfcEuWgnRztxTS6fbVguDCkB95yQq/WT2XzuohUMSG
0uGGemlbAkEA1ya+aG8bRNlEC4yGiROSWZOiFBtiUhMyDGQ4E/FUifNdZSft5jSE
gqUZZYJNchyKSXWtFKyclvJjcnflKxBubwJAT7eexs4bDvA+hK3RtVnMC9Q0eY5a
64ECja9++598leSwXHKEdWeFkOjQ8XXmiBm/lCZmtYLEacYKMWNV5YZe9wJAMYM/
CnWXRu7hE+9Q/ra8VVT+VbY/mDfGqsddiGlfVSfmdGMOAo5PeGlaQNwNypb61BD6
telLWAmMDUm+OXzcjQJBAJGn+vI0JV7OI0m4QpSucn/rJ9pAYJG4HE/MOQcgHog0
AeussmDIlr+wqWr+iJxYfJHc8ikTRSeTgqavruZs2Hg=
-----END RSA PRIVATE KEY-----
";

if (!$privateKey) {
    echo json_encode(["success" => false, "error" => "服务器私钥加载失败"]);
    exit();
}

// 解析 POST 数据
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username']) && isset($data['password'])) {
    $encryptedUsername = $data['username'];
    $encryptedPassword = $data['password'];

    // 解密用户名
    if (!openssl_private_decrypt(base64_decode($encryptedUsername), $decryptedUsername, $privateKey)) {
        echo json_encode(["success" => false, "error" => "用户名解密失败"]);
        exit();
    }

    // 解密密码
    if (!openssl_private_decrypt(base64_decode($encryptedPassword), $decryptedPassword, $privateKey)) {
        echo json_encode(["success" => false, "error" => "密码解密失败"]);
        exit();
    }

    // 使用解密后的用户名和密码进行数据库验证
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $decryptedUsername]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['password'] === md5($decryptedPassword)) {
        // 密码验证成功
        echo json_encode(["success" => true]);
    } else {
        // 密码错误
        echo json_encode(["success" => false, "error" => "Invalid username or password"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Missing data"]);
}
