<?php
require_once '../db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $stmt = $pdo->prepare('SELECT id FROM users WHERE username=?');
  $stmt->execute([$username]);
  if ($stmt->fetch()) {
    echo "<script>alert('用户名已存在');history.back();</script>";
    exit;
  }
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
  $stmt->execute([$username, $hash]);
  header('Location: /pages/login.php');
  exit;
}
