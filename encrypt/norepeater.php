<?php
header('Content-Type: application/json; charset=utf-8');

// 引入数据库连接文件
require_once '../database.php';  // 这个文件包含了数据库连接

// 处理 POST 请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 获取请求的 JSON 数据
    $data = json_decode(file_get_contents('php://input'), true);

    // 验证请求数据是否包含所需的字段
    if (!isset($data['username']) || !isset($data['password']) || !isset($data['timestamp'])) {
        echo json_encode(['success' => false, 'error' => 'buwanzheng']);
        exit;
    }

    $username = $data['username'];
    $password = $data['password'];
    $timestamp = $data['timestamp'];

    // 1. 防止时间戳重放攻击（设置一个时间窗口，如 30秒）
    $currentTimestamp = time() * 1000;  // 当前时间戳（毫秒）
    $timeWindow = 3000;  // 30秒的时间窗口

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
