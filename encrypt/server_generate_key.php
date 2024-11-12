<?php
session_start();
header('Content-Type: application/json');

// 生成随机 AES 密钥和 IV，并存储在会话中
if (!isset($_SESSION['aes_key']) || !isset($_SESSION['aes_iv'])) {
    $_SESSION['aes_key'] = base64_encode(random_bytes(16)); // 128位的AES密钥
    $_SESSION['aes_iv'] = base64_encode(random_bytes(16));  // 128位的AES IV
}

// 返回 AES 密钥和 IV 给客户端（确保 HTTPS 连接）
echo json_encode([
    'aes_key' => $_SESSION['aes_key'],
    'aes_iv' => $_SESSION['aes_iv']
]);
