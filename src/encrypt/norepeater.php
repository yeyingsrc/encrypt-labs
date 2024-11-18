<?php
header('Content-Type: application/json; charset=utf-8');

// 引入数据库连接文件
require_once '../database.php';  // 这个文件包含了数据库连接

// 设置私钥
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

// 函数：使用 RSA 私钥解密
function rsaDecrypt($encryptedData, $privateKey) {
    $decrypted = '';
    if (openssl_private_decrypt(base64_decode($encryptedData), $decrypted, $privateKey)) {
        return $decrypted;
    }
    return null;
}

// 处理 POST 请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 获取请求的 JSON 数据
    $data = json_decode(file_get_contents('php://input'), true);

    // 验证请求数据是否包含所需的字段
    if (!isset($data['username']) || !isset($data['password']) || !isset($data['random'])) {
        echo json_encode(['success' => false, 'error' => '数据不完整']);
        exit;
    }

    $username = $data['username'];
    $password = $data['password'];
    
    // 解密 random
    $encryptedRandom = $data['random'];
    $timestamp = rsaDecrypt($encryptedRandom, $privateKey);
    if ($timestamp === null) {
        echo json_encode(['success' => false, 'error' => '随机值解密失败']);
        exit;
    }

    // 1. 防止时间戳重放攻击（设置一个时间窗口，如 3秒）
    $currentTimestamp = time() * 1000;  // 当前时间戳（毫秒）
    $timeWindow = 3000;  // 3秒的时间窗口

    // 检查时间戳是否在有效时间范围内
    if (abs($currentTimestamp - $timestamp) > $timeWindow) {
        echo json_encode(['success' => false, 'error' => 'No Repeater']);
        exit;
    }

    // 2. 创建 requestID，避免客户端伪造
    $requestID = hash('sha256', $username . $password . $timestamp . $currentTimestamp);  // 利用用户名、密码和时间戳生成唯一的 requestID

    // 3. 检查 requestID 是否已存在，防止重放攻击
    $stmt = $pdo->prepare("SELECT * FROM requests WHERE requestID = :requestID");
    $stmt->execute(['requestID' => $requestID]);
    $existingRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingRequest) {
        // 如果 requestID 已存在，拒绝请求
        echo json_encode(['success' => false, 'error' => 'requestID exist']);
        exit;
    }

    // 4. 对密码进行 MD5 加密，验证用户名和加密后的密码（假设数据库中已存储加密后的密码）
    $md5Password = md5($password); // 对输入密码进行 MD5 加密

    // 查询数据库验证用户名和加密后的密码
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute(['username' => $username, 'password' => $md5Password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 登录成功，记录新的 requestID
        $stmt = $pdo->prepare("INSERT INTO requests (requestID) VALUES (:requestID)");
        $stmt->execute(['requestID' => $requestID]);

        echo json_encode(['success' => true, 'message' => 'Login Success']);
    } else {
        // 即使登录失败，也记录 requestID 防止重放攻击
        $stmt = $pdo->prepare("INSERT INTO requests (requestID) VALUES (:requestID)");
        $stmt->execute(['requestID' => $requestID]);

        echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
    }

} else {
    // 如果请求方法不是 POST
    echo json_encode(['success' => false, 'error' => '仅支持 POST 请求']);
}
?>
