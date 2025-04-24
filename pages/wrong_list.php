<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: /pages/login.php');
  exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT q.* FROM questions q JOIN user_wrong uw ON q.id=uw.question_id WHERE uw.user_id=?');
$stmt->execute([$user_id]);
$wrongs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="UTF-8">
  <title>我的错题</title>
  <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
  <h1>我的错题</h1>
  <div id="quiz-container">
    <?php foreach($wrongs as $idx=>$q): ?>
      <div class="md3-card">
        <div class="question"><?= $idx+1 ?>. <?= htmlspecialchars($q['question']) ?></div>
        <div class="explanation"><?= htmlspecialchars($q['explanation'] ?? '暂无解析') ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="center-btn">
    <a href="/index.php" class="md3-btn">返回答题</a>
  </div>
</body>
</html>
