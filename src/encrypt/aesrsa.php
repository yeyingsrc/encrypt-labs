<?php
header('Content-Type: application/json');
require '../database.php';
session_start();
// 使用服务器的私钥解密 AES 密钥和 IV
function decryptRSA($encrypted, $privateKey) {
    openssl_private_decrypt(base64_decode($encrypted), $decrypted, $privateKey);
    return $decrypted;
}

// 解析 POST 数据
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['encryptedData']) && isset($data['encryptedKey']) && isset($data['encryptedIv'])) {
    $encryptedData = $data['encryptedData'];
    $encryptedKey = $data['encryptedKey'];
    $encryptedIv = $data['encryptedIv'];

    // 私钥，用于解密 AES 密钥和 IV
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

    // 解密 AES 密钥和 IV
    $aesKey = decryptRSA($encryptedKey, $privateKey);
    $aesIv = decryptRSA($encryptedIv, $privateKey);

    // 使用解密后的 AES 密钥和 IV 解密数据
    $decryptedData = openssl_decrypt(
        base64_decode($encryptedData),
        'aes-128-cbc',
        base64_decode($aesKey),
        OPENSSL_RAW_DATA,
        base64_decode($aesIv)
    );

    // 解密后的数据应该是一个 JSON 字符串
    $formData = json_decode($decryptedData, true);

    if (isset($formData['username']) && isset($formData['password'])) {
        $username = $formData['username'];
        $password = $formData['password'];

        // 假设数据库连接已经建立
        // 查询数据库检查用户名和密码
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // 用户名存在，检查密码
            // 假设数据库中密码使用 MD5 存储，您可以根据实际情况替换为 bcrypt 或其他哈希方法
            if ($user['password'] === md5($password)) {
                // 密码正确
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
        echo json_encode(["success" => false, "error" => "Invalid data"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Missing data"]);
}
