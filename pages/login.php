<?php
session_start();
if (isset($_SESSION['user_id'])) {
  header('Location: /index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="UTF-8">
  <title>登录</title>
  <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
  <h1>登录</h1>
  <form method="post" action="/auth/login.php" class="md3-card" style="max-width:350px;margin:2em auto;">
    <input class="md3-input" type="text" name="username" placeholder="用户名" required>
    <input class="md3-input" type="password" name="password" placeholder="密码" required>
    <button class="md3-btn" type="submit">登录</button>
    <div style="margin-top:1em;">
      没有账号？<a href="/pages/register.php">注册</a>
    </div>
  </form>
</body>
</html>
