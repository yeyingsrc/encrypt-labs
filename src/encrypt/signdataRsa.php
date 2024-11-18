<?php
header('Content-Type: application/json');
require '../database.php';
session_start();
// 加载 RSA 私钥文件（用于签名）
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

// 解析 POST 数据
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username']) && isset($data['password']) && isset($data['nonce']) && isset($data['timestamp'])) {
    $username = $data['username'];
    $password = $data['password'];
    $nonce = $data['nonce'];
    $timestamp = $data['timestamp'];

    // 检查时间戳是否在允许的时间范围内（例如 5 分钟内）
    $currentTimestamp = time();
    if (abs($currentTimestamp - $timestamp) > 8) { // 300 秒 = 5 分钟
        echo json_encode(["success" => false, "error" => "Request timeout"]);
        exit();
    }

    // 检查 nonce 是否已经使用过
    if (nonceExistsInDatabase($nonce)) {
        echo json_encode(["success" => false, "error" => "Duplicate request detected"]);
        exit();
    } else {
        storeNonceInDatabase($nonce); // 将新 nonce 存储到数据库，防止重复使用
    }

    // 生成待签名的数据
    $dataToSign = $username . $password . $nonce . $timestamp;

    // 使用 RSA 私钥生成签名
    openssl_sign($dataToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    $signatureBase64 = base64_encode($signature); // 将签名转换为 Base64 编码，方便传输

    // 继续进行数据库验证
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['password'] === md5($password)) {
        // 密码验证成功
        echo json_encode(["success" => true, "signature" => $signatureBase64]);
    } else {
        // 密码错误
        echo json_encode(["success" => false, "error" => "Invalid username or password"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Missing data"]);
}

// 示例的伪函数：检查 nonce 是否已经存在于数据库
function nonceExistsInDatabase($nonce) {
    // 实际实现应查询数据库，例如 Redis 或 SQL 表
    return false;
}

// 示例的伪函数：将 nonce 存储到数据库中
function storeNonceInDatabase($nonce) {
    // 实际实现应将 nonce 存储到数据库，并设置过期策略
}
