<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="UTF-8">
  <title>注册</title>
  <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
  <h1>注册</h1>
  <form method="post" action="/auth/register.php" class="md3-card" style="max-width:350px;margin:2em auto;">
    <input class="md3-input" type="text" name="username" placeholder="用户名" required>
    <input class="md3-input" type="password" name="password" placeholder="密码" required>
    <button class="md3-btn" type="submit">注册</button>
    <div style="margin-top:1em;">
      已有账号？<a href="/pages/login.php">登录</a>
    </div>
  </form>
</body>
</html>
