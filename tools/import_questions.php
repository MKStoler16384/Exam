<?php
require_once '../db.php';
$json = file_get_contents('../questions.json');
$questions = json_decode($json, true);
foreach ($questions as $q) {
    $stmt = $pdo->prepare('INSERT INTO questions (question, options, answer, type, explanation) VALUES (?, ?, ?, ?, ?)');
    $options = isset($q['options']) ? json_encode($q['options'], JSON_UNESCAPED_UNICODE) : null;
    $answer = isset($q['answer']) ? json_encode($q['answer'], JSON_UNESCAPED_UNICODE) : null;
    $stmt->execute([
        $q['question'],
        $options,
        $answer,
        $q['type'],
        $q['explanation'] ?? ''
    ]);
}
echo "导入完成";
