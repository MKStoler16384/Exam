<?php
session_start();
require_once '../db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $stmt = $pdo->prepare('SELECT id, password FROM users WHERE username=?');
  $stmt->execute([$username]);
  $user = $stmt->fetch();
  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    header('Location: /index.php');
    exit;
  } else {
    echo "<script>alert('用户名或密码错误');history.back();</script>";
  }
}
