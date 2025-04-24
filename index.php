<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: /pages/login.php');
  exit;
}
require_once __DIR__ . '/db.php';
$stmt = $pdo->prepare('SELECT username, is_admin FROM users WHERE id=?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="UTF-8">
  <title>随机选择题目演示</title>
  <link rel="stylesheet" href="/static/css/style.css">
  <style>
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 700px;
      margin: 1.5em auto 0 auto;
      padding: 0 1em;
    }
    .top-bar .user-info {
      color: #388e3c;
      font-weight: 500;
    }
    .top-bar .nav-links a {
      margin-left: 1.2em;
      font-size: 1em;
    }
    @media (max-width: 700px) {
      .top-bar { flex-direction: column; align-items: flex-start; }
      .top-bar .nav-links { margin-top: 0.5em; }
    }
  </style>
</head>
<body>
  <div class="top-bar">
    <div class="user-info">
      欢迎，<?= htmlspecialchars($user['username']) ?><?php if ($user['is_admin']) echo '（管理员）'; ?>
    </div>
    <div class="nav-links">
      <a href="/pages/wrong_list.php">我的错题</a>
      <?php if ($user['is_admin']): ?>
        <a href="/pages/admin.php">后台管理</a>
      <?php endif; ?>
      <a href="/pages/logout.php">退出登录</a>
    </div>
  </div>
  <h1>随机选择题目</h1>
  <div id="quiz-container"></div>
  <div class="center-btn">
    <button id="submit-all-btn" class="md3-btn" style="display:none;">提交答案</button>
    <button id="next-btn" class="md3-btn" style="margin-left:1em;">下一题</button>
  </div>
  <script>
    window.USER_ID = <?php echo json_encode($_SESSION['user_id']); ?>;
  </script>
  <script src="/static/js/quiz.js"></script>
</body>
</html>
