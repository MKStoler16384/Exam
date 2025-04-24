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

// 处理删除/设置管理员
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $uid = intval($_POST['uid'] ?? 0);
  if (isset($_POST['delete']) && $uid && $uid != $_SESSION['user_id']) {
    $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$uid]);
    $pdo->prepare('DELETE FROM user_wrong WHERE user_id=?')->execute([$uid]);
    $pdo->prepare('DELETE FROM user_done WHERE user_id=?')->execute([$uid]);
  }
  if (isset($_POST['set_admin']) && $uid) {
    $pdo->prepare('UPDATE users SET is_admin=1 WHERE id=?')->execute([$uid]);
  }
  if (isset($_POST['unset_admin']) && $uid && $uid != $_SESSION['user_id']) {
    $pdo->prepare('UPDATE users SET is_admin=0 WHERE id=?')->execute([$uid]);
  }
  header('Location: manage_users.php'); exit;
}

$users = $pdo->query('SELECT * FROM users ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="UTF-8">
  <title>用户管理</title>
  <link rel="stylesheet" href="/static/css/style.css">
  <style>
    .u-table { width:100%; border-collapse:collapse; margin-bottom:2em;}
    .u-table th, .u-table td { border:1px solid #a5d6a7; padding:0.5em 0.7em; }
    .u-table th { background:#e8f5e9; }
    .md3-btn { margin-bottom:0.2em; }
  </style>
</head>
<body>
  <h1>用户管理</h1>
  <div class="md3-card" style="max-width:700px;margin:2em auto;">
    <table class="u-table">
      <tr>
        <th>ID</th><th>用户名</th><th>管理员</th><th>操作</th>
      </tr>
      <?php foreach($users as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= $u['is_admin'] ? '是' : '否' ?></td>
          <td>
            <?php if ($u['id'] != $_SESSION['user_id']): ?>
              <form method="post" style="display:inline;">
                <input type="hidden" name="uid" value="<?= $u['id'] ?>">
                <?php if ($u['is_admin']): ?>
                  <button class="md3-btn" name="unset_admin" value="1" onclick="return confirm('确定取消管理员？')">取消管理员</button>
                <?php else: ?>
                  <button class="md3-btn" name="set_admin" value="1">设为管理员</button>
                <?php endif; ?>
                <button class="md3-btn" name="delete" value="1" onclick="return confirm('确定删除该用户？')">删除</button>
              </form>
            <?php else: ?>
              <span style="color:#888;">当前用户</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
    <a class="md3-btn" href="/pages/admin.php">返回后台</a>
  </div>
</body>
</html>
