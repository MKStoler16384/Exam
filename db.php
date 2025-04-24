<?php
$db_file = __DIR__ . '/exam.db';
$need_init = !file_exists($db_file);

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($need_init) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                is_admin INTEGER DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS questions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                question TEXT NOT NULL,
                options TEXT,
                answer TEXT,
                type TEXT,
                explanation TEXT
            );
            CREATE TABLE IF NOT EXISTS user_wrong (
                user_id INTEGER,
                question_id INTEGER,
                PRIMARY KEY(user_id, question_id)
            );
            CREATE TABLE IF NOT EXISTS user_done (
                user_id INTEGER,
                question_id INTEGER,
                PRIMARY KEY(user_id, question_id)
            );
        ");
        // 创建管理员账号
        $admin_pass = bin2hex(random_bytes(4));
        $admin_hash = password_hash($admin_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, is_admin) VALUES (?, ?, 1)');
        $stmt->execute(['admin', $admin_hash]);
        file_put_contents(__DIR__ . '/admin_init.txt', "管理员账号：admin\n初始密码：$admin_pass\n请尽快登录后修改密码！");
    }
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
