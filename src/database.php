<?php
$host = '127.0.0.1'; // 数据库主机
$dbname = 'encryptDB';  // 数据库名称
$username = 'bachang';  // 数据库用户名
$password = 'password';      // 数据库密码

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
?>
