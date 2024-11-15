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

// 检查私钥是否加载成功
if (!$privateKey) {
    echo json_encode(["success" => false, "error" => "服务器私钥加载失败"]);
    exit();
}

// 解析 POST 数据（application/x-www-form-urlencoded 格式）
$data = $_POST['data'] ?? null;

if ($data) {
    // 解密整个加密数据包
    if (!openssl_private_decrypt(base64_decode($data), $decryptedData, $privateKey)) {
        echo json_encode(["success" => false, "error" => "数据解密失败"]);
        exit();
    }

    // 解析解密后的数据包
    $dataPacket = json_decode($decryptedData, true);

    // 检查数据包中是否有用户名和密码
    if (isset($dataPacket['username']) && isset($dataPacket['password'])) {
        $decryptedUsername = $dataPacket['username'];
        $decryptedPassword = $dataPacket['password'];

        // 使用解密后的用户名进行数据库查询
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $decryptedUsername]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 验证用户名和密码
        if ($user && $user['password'] === md5($decryptedPassword)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Invalid username or password"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Missing username or password"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "No data received"]);
}
