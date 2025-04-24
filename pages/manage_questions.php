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

// 处理新增/编辑/删除
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $type = $_POST['type'] ?? '';
  $question = $_POST['question'] ?? '';
  $options = $_POST['options'] ?? '';
  $answer = $_POST['answer'] ?? '';
  $explanation = $_POST['explanation'] ?? '';
  $id = $_POST['id'] ?? null;

  if (isset($_POST['delete']) && $id) {
    $stmt = $pdo->prepare('DELETE FROM questions WHERE id=?');
    $stmt->execute([$id]);
    header('Location: manage_questions.php'); exit;
  }

  if ($type && $question) {
    $options_json = ($type === 'blank') ? null : json_encode(array_map('trim', explode("\n", $options)), JSON_UNESCAPED_UNICODE);
    $answer_json = ($type === 'multi') ? json_encode(array_map('intval', explode(',', $answer)), JSON_UNESCAPED_UNICODE)
                 : (($type === 'choice') ? intval($answer) : json_encode(array_map('trim', explode(',', $answer)), JSON_UNESCAPED_UNICODE));
    if ($id) {
      $stmt = $pdo->prepare('UPDATE questions SET type=?, question=?, options=?, answer=?, explanation=? WHERE id=?');
      $stmt->execute([$type, $question, $options_json, $answer_json, $explanation, $id]);
    } else {
      $stmt = $pdo->prepare('INSERT INTO questions (type, question, options, answer, explanation) VALUES (?, ?, ?, ?, ?)');
      $stmt->execute([$type, $question, $options_json, $answer_json, $explanation]);
    }
    header('Location: manage_questions.php'); exit;
  }
}

// 编辑模式
$edit = null;
if (isset($_GET['edit'])) {
  $stmt = $pdo->prepare('SELECT * FROM questions WHERE id=?');
  $stmt->execute([$_GET['edit']]);
  $edit = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($edit) {
    $edit['options'] = $edit['options'] ? implode("\n", json_decode($edit['options'], true)) : '';
    if ($edit['type'] === 'multi') {
      $edit['answer'] = implode(',', json_decode($edit['answer'], true));
    } elseif ($edit['type'] === 'choice') {
      $edit['answer'] = json_decode($edit['answer'], true) ?? $edit['answer'];
    } else {
      $edit['answer'] = implode(',', json_decode($edit['answer'], true));
    }
  }
}

// 查询所有题目
$questions = $pdo->query('SELECT * FROM questions ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="UTF-8">
  <title>题库管理</title>
  <link rel="stylesheet" href="/static/css/style.css">
  <style>
    .q-table { width:100%; border-collapse:collapse; margin-bottom:2em;}
    .q-table th, .q-table td { border:1px solid #a5d6a7; padding:0.5em 0.7em; }
    .q-table th { background:#e8f5e9; }
    .q-table td pre { margin:0; font-size:0.98em; }
    .edit-btn, .del-btn { margin-right:0.5em; }
    .form-row { margin-bottom:1em; }
    textarea { width:100%; min-height:60px; }
  </style>
</head>
<body>
  <h1>题库管理</h1>
  <div class="md3-card" style="max-width:700px;margin:2em auto;">
    <form method="post">
      <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
      <div class="form-row">
        <label>题型
          <select name="type" class="md3-input" required>
            <option value="choice" <?= isset($edit)&&$edit['type']=='choice'?'selected':'' ?>>单选</option>
            <option value="multi" <?= isset($edit)&&$edit['type']=='multi'?'selected':'' ?>>多选</option>
            <option value="blank" <?= isset($edit)&&$edit['type']=='blank'?'selected':'' ?>>填空</option>
          </select>
        </label>
      </div>
      <div class="form-row">
        <label>题干<textarea name="question" required><?= htmlspecialchars($edit['question'] ?? '') ?></textarea></label>
      </div>
      <div class="form-row">
        <label>选项（每行一个，仅限选择题）<textarea name="options"><?= htmlspecialchars($edit['options'] ?? '') ?></textarea></label>
      </div>
      <div class="form-row">
        <label>答案
          <input class="md3-input" name="answer" value="<?= htmlspecialchars($edit['answer'] ?? '') ?>" required>
          <span style="font-size:0.95em;color:#888;">
            单选填下标(如0)，多选填下标逗号分隔(如0,2,3)，填空填答案逗号分隔
          </span>
        </label>
      </div>
      <div class="form-row">
        <label>解析<textarea name="explanation"><?= htmlspecialchars($edit['explanation'] ?? '') ?></textarea></label>
      </div>
      <button class="md3-btn" type="submit"><?= $edit ? '保存修改' : '添加题目' ?></button>
      <?php if ($edit): ?>
        <a class="md3-btn" href="manage_questions.php" style="margin-left:1em;">取消</a>
      <?php endif; ?>
    </form>
  </div>
  <div class="md3-card" style="max-width:900px;margin:2em auto;overflow-x:auto;">
    <div style="overflow-x:auto;">
      <table class="q-table" style="min-width:1000px;">
        <tr>
          <th>ID</th><th>题型</th><th>题干</th><th>选项</th><th>答案</th><th>解析</th><th>操作</th>
        </tr>
        <?php foreach($questions as $q): ?>
          <tr>
            <td><?= $q['id'] ?></td>
            <td><?= htmlspecialchars($q['type']) ?></td>
            <td><pre><?= htmlspecialchars($q['question']) ?></pre></td>
            <td><pre><?= htmlspecialchars($q['options']) ?></pre></td>
            <td><pre><?= htmlspecialchars($q['answer']) ?></pre></td>
            <td><pre><?= htmlspecialchars($q['explanation']) ?></pre></td>
            <td>
              <a class="edit-btn md3-btn" href="?edit=<?= $q['id'] ?>">编辑</a>
              <form method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $q['id'] ?>">
                <button class="del-btn md3-btn" name="delete" value="1" onclick="return confirm('确定删除？')">删除</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
    <a class="md3-btn" href="/pages/admin.php">返回后台</a>
  </div>
</body>
</html>
