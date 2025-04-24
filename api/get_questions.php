<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) {
  http_response_code(401); exit;
}
$user_id = $_SESSION['user_id'];
// 获取所有题目
$all = $pdo->query('SELECT * FROM questions')->fetchAll(PDO::FETCH_ASSOC);
// 获取用户已做题目id
$stmt = $pdo->prepare('SELECT question_id FROM user_done WHERE user_id=?');
$stmt->execute([$user_id]);
$done = $stmt->fetchAll(PDO::FETCH_COLUMN);
$questions = array_filter($all, fn($q) => !in_array($q['id'], $done));
echo json_encode(array_values($questions), JSON_UNESCAPED_UNICODE);
