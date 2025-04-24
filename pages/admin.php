<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: /pages/login.php');
  exit;
}
$stmt = $pdo->prepare('SELECT is_admin FROM users WHERE id=?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
if (!$user || !$user['is_admin']) {
  echo "无权限访问"; exit;
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="UTF-8">
  <title>后台管理</title>
  <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
  <h1>后台管理</h1>
  <div class="md3-card" style="max-width:500px;margin:2em auto;">
    <a class="md3-btn" href="/pages/manage_questions.php">题库管理</a>
    <a class="md3-btn" href="/pages/manage_users.php" style="margin-left:1em;">用户管理</a>
    <a class="md3-btn" href="/index.php" style="margin-left:1em;">返回首页</a>
  </div>
</body>
</html>
