<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>管理ダッシュボード</title>
</head>
<body>
    <h1>管理ダッシュボード</h1>
    <p>ようこそ、<?= htmlspecialchars($_SESSION['user_name']) ?> さん</p>

    <ul>
        <li><a href="estimate_admin.php">見積もり計算（管理者専用）</a></li>
        <li><a href="templates_edit.php">メールテンプレート編集</a></li>
        <li><a href="optin_list.php">オプトインリスト管理（準備中）</a></li>
        <li><a href="../estimate_form.php" target="_blank">計算フォーム（新しいタブで開く）</a></li>
        <li><a href="price_settings.php">価格マスタ管理</a></li>
        <li><a href="logout.php">ログアウト</a></li>
    </ul>
</body>
</html>