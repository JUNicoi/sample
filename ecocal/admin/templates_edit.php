<?php
session_start();
require_once '../db.php';

// 認証チェック（仮のセッションチェック）
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// テンプレート削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_template'])) {
    $id = $_POST['template_id'];

    $stmt = $db->prepare("DELETE FROM email_templates WHERE id = ?");
    $stmt->execute([$id]);

    $message = "テンプレートを削除しました。";
    // リダイレクトして一覧を再取得
    header("Location: templates_edit.php");
    exit;
}

// 新規テンプレート作成処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_template'])) {
    $new_name = $_POST['new_name'];
    $new_subject = $_POST['new_subject'];
    $new_body = $_POST['new_body'];

    $stmt = $db->prepare("INSERT INTO email_templates (name, subject, body) VALUES (?, ?, ?)");
    $stmt->execute([$new_name, $new_subject, $new_body]);

    $message = "新しいテンプレートを作成しました。";
}

// テンプレート更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['create_template'])) {
    $id = $_POST['template_id'];
    $subject = $_POST['subject'];
    $body = $_POST['body'];

    $stmt = $db->prepare("UPDATE email_templates SET subject = ?, body = ? WHERE id = ?");
    $stmt->execute([$subject, $body, $id]);

    $message = "テンプレートを更新しました。";
}

// テンプレート一覧取得
$templates = $db->query("SELECT id, name FROM email_templates")->fetchAll(PDO::FETCH_ASSOC);

// 編集対象のテンプレートを取得
$current_id = $_GET['id'] ?? $templates[0]['id'];
$stmt = $db->prepare("SELECT * FROM email_templates WHERE id = ?");
$stmt->execute([$current_id]);
$template = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メールテンプレート編集</title>
</head>
<body>
    <h1>メールテンプレート編集</h1>
    <p><a href="dashboard.php">← ダッシュボードに戻る</a></p>

    <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>

    <h2>新規テンプレート作成</h2>
    <form method="post">
        <input type="hidden" name="create_template" value="1">
        <p>
            <label>テンプレート名：</label><br>
            <input type="text" name="new_name" required style="width: 100%;">
        </p>
        <p>
            <label>件名：</label><br>
            <input type="text" name="new_subject" required style="width: 100%;">
        </p>
        <p>
            <label>本文：</label><br>
            <textarea name="new_body" rows="10" required style="width: 100%;"></textarea>
        </p>
        <button type="submit">作成する</button>
    </form>
    <hr>

    <form method="get">
        <label>テンプレート選択：</label>
        <select name="id" onchange="this.form.submit()">
            <?php foreach ($templates as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $t['id'] == $current_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($template): ?>
    <form method="post">
        <input type="hidden" name="template_id" value="<?= $template['id'] ?>">
        <p>
            <label>件名：</label><br>
            <input type="text" name="subject" value="<?= htmlspecialchars($template['subject']) ?>" style="width: 100%;">
        </p>
        <p>
            <label>本文：</label><br>
            <textarea name="body" rows="15" style="width: 100%;"><?= htmlspecialchars($template['body']) ?></textarea>
        </p>
        <button type="submit">保存する</button>
        <button type="submit" name="delete_template" value="1" onclick="return confirm('このテンプレートを削除しますか？')">削除する</button>
    </form>
    <?php endif; ?>
    <hr>
    <h3>使用可能な差し込みキーワード</h3>
    <ul>
        <li>メールアドレス：<code>{email}</code></li>
        <li>会社名：<code>{company}</code></li>
        <li>担当者名：<code>{person}</code></li>
        <li>電話番号：<code>{phone}</code></li>
        <li>構造種別：<code>{structure}</code></li>
        <li>計算種別（新築/増築）：<code>{calc_type}</code></li>
        <li>都道府県：<code>{prefecture}</code></li>
        <li>市区町村：<code>{city}</code></li>
        <li>希望納期：<code>{delivery_date}</code></li>
        <li>概算金額：<code>{estimate_price}</code></li>
        <li>コメント欄の内容：<code>{comment}</code></li>
        <li>用途ごとの面積一覧：<code>{usage_areas}</code></li>
    </ul>
</body>
</html>