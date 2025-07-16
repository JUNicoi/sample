<?php
require_once '../db.php';

// 新規管理者の情報
$name = '管理者';
$email = 'admin@example.com';
$password = 'pass1234'; // 平文パスワード（ログイン時に使う）
$role = 'admin';

// パスワードをハッシュ化
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 既に存在していなければ追加
$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
$stmt->execute([$email]);
$count = $stmt->fetchColumn();

if ($count == 0) {
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hashed_password, $role]);
    echo "管理者ユーザーを登録しました。<br>Email: $email<br>パスワード: $password";
} else {
    echo "すでにこのメールアドレスのユーザーが存在しています。";
}
?>