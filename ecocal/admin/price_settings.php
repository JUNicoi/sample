<?php
session_start();
require_once '../db.php';

// 認証チェック
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// 共通係数設定の取得
$global_settings = $db->query("SELECT * FROM global_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// 構造係数の取得
$structure_settings = $db->query("SELECT * FROM structure_multipliers")->fetchAll(PDO::FETCH_ASSOC);

// 構造係数の更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_structure'])) {
    foreach ($_POST['structure'] as $id => $ratio) {
        $stmt = $db->prepare("UPDATE structure_multipliers SET multiplier = ? WHERE id = ?");
        $stmt->execute([floatval($ratio), intval($id)]);
    }
    $structure_settings = $db->query("SELECT * FROM structure_multipliers")->fetchAll(PDO::FETCH_ASSOC);
    $message = "構造係数を更新しました。";
}

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_global'])) {
    $express = floatval($_POST['express_multiplier']);
    $multi = floatval($_POST['multi_usage_multiplier']);
    $stmt = $db->prepare("UPDATE global_settings SET express_multiplier = ?, multi_usage_multiplier = ?");
    $stmt->execute([$express, $multi]);
    $global_settings['express_multiplier'] = $express;
    $global_settings['multi_usage_multiplier'] = $multi;
    $message = "共通係数を更新しました。";
}

// 新規追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_usage'])) {
    $usage = trim($_POST['new_usage']);
    $unit_price = intval($_POST['new_unit_price']);

    if ($usage !== '' && $unit_price > 0) {
        $stmt = $db->prepare("INSERT INTO price_master (usage_name, unit_price) VALUES (?, ?)");
        $stmt->execute([$usage, $unit_price]);
        $message = "新しい価格を追加しました。";
    }
}

// 編集処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $usage = trim($_POST['edit_usage']);
    $unit_price = intval($_POST['edit_unit_price']);

    if ($usage !== '' && $unit_price > 0) {
        $stmt = $db->prepare("UPDATE price_master SET usage_name = ?, unit_price = ? WHERE id = ?");
        $stmt->execute([$usage, $unit_price, $id]);
        $message = "価格情報を更新しました。";
    }
}

// 削除処理
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM price_master WHERE id = ?");
    $stmt->execute([$id]);
    $message = "価格情報を削除しました。";
}

// 一覧取得
$prices = $db->query("SELECT * FROM price_master ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>価格マスタ管理</title>
</head>
<body>
    <h1>価格マスタ管理</h1>
    <p><a href="dashboard.php">← ダッシュボードに戻る</a></p>

    <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

    <h2>共通係数設定</h2>
    <form method="post">
        <input type="hidden" name="update_global" value="1">
        <p>
            <label>特急係数：</label>
            <input type="number" step="0.01" name="express_multiplier" value="<?= htmlspecialchars($global_settings['express_multiplier']) ?>" required>
        </p>
        <p>
            <label>複数用途係数：</label>
            <input type="number" step="0.01" name="multi_usage_multiplier" value="<?= htmlspecialchars($global_settings['multi_usage_multiplier']) ?>" required>
        </p>
        <button type="submit">共通係数を保存</button>
    </form>

    <h2>構造係数設定</h2>
    <form method="post">
        <input type="hidden" name="update_structure" value="1">
        <table border="1" cellpadding="5">
            <tr>
                <th>構造種別</th>
                <th>係数</th>
            </tr>
            <?php foreach ($structure_settings as $struct): ?>
            <tr>
                <td><?= htmlspecialchars($struct['structure']) ?></td>
                <td>
                    <input type="number" step="0.01" name="structure[<?= $struct['id'] ?>]" value="<?= htmlspecialchars($struct['multiplier']) ?>" required>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <button type="submit">構造係数を保存</button>
    </form>

    <h2>登録済みの価格</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>用途名</th>
            <th>単価（円/㎡）</th>
            <th>操作</th>
        </tr>
        <?php foreach ($prices as $price): ?>
            <tr>
                <form method="post">
                    <input type="hidden" name="edit_id" value="<?= $price['id'] ?>">
                    <td><?= $price['id'] ?></td>
                    <td><input type="text" name="edit_usage" value="<?= htmlspecialchars($price['usage_name']) ?>"></td>
                    <td><input type="number" name="edit_unit_price" value="<?= $price['unit_price'] ?>"></td>
                    <td>
                        <button type="submit">更新</button>
                        <a href="?delete=<?= $price['id'] ?>" onclick="return confirm('本当に削除しますか？')">削除</a>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>新規追加</h2>
    <form method="post">
        <p>
            <label>用途名：</label>
            <input type="text" name="new_usage" required>
        </p>
        <p>
            <label>単価（円/㎡）：</label>
            <input type="number" name="new_unit_price" required>
        </p>
        <button type="submit">追加する</button>
    </form>
</body>
</html>
