<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) {
  http_response_code(401); exit;
}
$data = json_decode(file_get_contents('php://input'), true);
$qid = $data['question_id'] ?? 0;
$user_id = $_SESSION['user_id'];
if ($qid) {
  // 记录错题
  $stmt = $pdo->prepare('INSERT OR IGNORE INTO user_wrong (user_id, question_id) VALUES (?, ?)');
  $stmt->execute([$user_id, $qid]);
  // 记录已做
  $stmt = $pdo->prepare('INSERT OR IGNORE INTO user_done (user_id, question_id) VALUES (?, ?)');
  $stmt->execute([$user_id, $qid]);
}
echo json_encode(['ok'=>1]);
