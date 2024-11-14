<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>encrypt靶场</title>
  <script src="js/crypto-js.min.js"></script> <!-- 引入 CryptoJS 库 -->
  <script src="js/jsencrypt.min.js"></script> <!-- 引入 JSEncrypt 库 -->

  <link rel="stylesheet" href="css/index.css"> <!-- 引入本地的 CSS 文件 -->
  <style>
    /* 弹窗样式 */
    .modal {
      display: none; /* 默认隐藏 */
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6); /* 半透明背景 */
      justify-content: center;
      align-items: center;
      z-index: 2; /* 确保弹窗显示在登录框上方 */
    }

    .modal-content {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 12px; /* 增加圆角 */
      text-align: center;
      width: 320px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); /* 添加阴影 */
      font-family: Arial, sans-serif;
    }

    .modal-content p {
      font-size: 1.1em;
      color: #333;
      margin-bottom: 20px;
    }

    .modal-content button {
      margin: 10px;
      padding: 10px 20px;
      font-size: 1em;
      color: #ffffff;
      background-color: #3c8dbc;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
    }

    .modal-content button:hover {
      background-color: #337ab7;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* 按钮悬停时阴影增加 */
    }

    .modal-content button:last-child {
      background-color: #e0e0e0; /* “取消”按钮的背景色 */
      color: #333;
    }

    .modal-content button:last-child:hover {
      background-color: #c8c8c8; /* “取消”按钮悬停时颜色变化 */
    }
    /* 归属栏样式 */
    
    footer {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      text-align: center;
      padding: 12px;
      font-size: 15px;
      color: rgba(0, 0, 0, 0.5); /* 更轻的灰色，使文字不显得突兀 */
      background: rgba(255, 255, 255, 0.4); /* 轻透明白色，融入页面背景 */
      backdrop-filter: blur(3px); /* 模糊效果，让背景更加柔和 */
      border-top: 1px solid rgba(255, 255, 255, 0.2); /* 顶部细分隔线 */
    }
    
    footer a {
      color: #66b2ff; /* 柔和的蓝色 */
      text-decoration: none;
      transition: color 0.3s;
    }
    
    footer a:hover {
      color: #3399ff; /* 悬停时稍加深 */
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <!-- 背景装饰元素 -->
  <div class="background-circle circle1"></div>
  <div class="background-circle circle2"></div>

  <!-- 登录容器 -->
  <div class="login-container">
    <h2>encrypt靶场</h2>

    <!-- 登录表单 -->
    <form id="loginForm">
      <!-- 账号输入 -->
      <div class="form-group">
        <label for="username">账号</label>
        <input type="text" id="username" name="username" placeholder="用户名" required>
      </div>

      <!-- 密码输入 -->
      <div class="form-group">
        <label for="password">密码</label>
        <input type="password" id="password" name="password" placeholder="请输入密码" required>
      </div>

      <!-- 登录按钮 -->
      <button type="submit" class="login-btn">登录</button>
    </form>
  </div>

  <!-- 弹窗 -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <p>选择数据发送接口:</p>
      <button onclick="sendDataAes('encrypt/aes.php')">AES固定Key</button>
      <!--<button onclick="sendData('encrypt/other.php')">AES随机Key</button>-->
      <button onclick="fetchAndSendDataAes('encrypt/aesserver.php')">AES服务端获取Key</button>
      <button onclick="sendEncryptedDataRSA('encrypt/rsa.php')">Rsa加密</button>
      <button onclick="sendDataAesRsa('encrypt/aesrsa.php')">AES+Rsa加密</button>
      <button onclick="encryptAndSendDataDES('encrypt/des.php')">Des规律Key</button>
      <button onclick="sendDataWithNonce('encrypt/signdata.php')">明文加签</button>
      <button onclick="sendDataWithSignatureRsa('encrypt/signdataRsa.php')">明文Rsa加签</button>
      <button onclick="sendLoginRequest('encrypt/norepeater.php')">禁止重放</button>
      <button onclick="closeModal()">取消</button>
    </div>
  </div>
    <script src="js/app.js"></script> <!-- 引入 JSEncrypt 库 -->
 <!-- 归属栏 -->
  <footer>
    本靶场由 <a href="https://github.com/SwagXz/encrypt-labs" target="_blank">Xz</a> 编写
  </footer>
</body>
</html>
