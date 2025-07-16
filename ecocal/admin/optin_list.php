<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 一括削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ids'])) {
    $ids = $_POST['delete_ids'];
    if (is_array($ids) && count($ids) > 0) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare("DELETE FROM optin_list WHERE id IN ($placeholders)");
        $stmt->execute($ids);
    }
    header("Location: optin_list.php");
    exit;
}

// 個別削除処理
if (isset($_GET['delete_id'])) {
    $stmt = $db->prepare("DELETE FROM optin_list WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: optin_list.php");
    exit;
}

// フィルター用日付取得
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

$conditions = [];
$params = [];

if ($start_date !== '' && $end_date !== '') {
    $conditions[] = "DATE(created_at) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
}

$where_clause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
$stmt = $db->prepare("SELECT * FROM optin_list $where_clause ORDER BY created_at DESC");
$stmt->execute($params);
$optins = $stmt->fetchAll(PDO::FETCH_ASSOC);

// CSV出力処理
if (isset($_GET['download']) && $_GET['download'] === '1') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="optin_list.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'メールアドレス', '会社名', '担当者名', '電話番号', '建物用途', '用途区分', '延床面積 (㎡)', '構造種別', '計算種別', '都道府県', '市区町村', '希望納期', '特急', '概算金額', 'コメント', '登録日時']);
    foreach ($optins as $row) {
        fputcsv($output, [
            $row['id'], $row['email'], $row['company'], $row['person'], $row['phone'], $row['usage'], $row['usage_type'], $row['area'], $row['structure'], $row['calc_type'], $row['prefecture'], $row['city'], $row['delivery_date'], $row['is_express'] ? 'はい' : 'いいえ', $row['estimate_price'], $row['comment'], $row['created_at']
        ]);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>オプトインリスト管理</title>
</head>
<body>
    <h1>オプトインリスト</h1>
    <p><a href="dashboard.php">← ダッシュボードに戻る</a></p>

    <form method="get">
        <label>開始日：<input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>"></label>
        <label>終了日：<input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>"></label>
        <button type="submit">フィルター</button>
        <a href="?start_date=<?= htmlspecialchars($start_date) ?>&end_date=<?= htmlspecialchars($end_date) ?>&download=1">CSV出力</a>
    </form>

<form method="post">
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th><input type="checkbox" id="select-all" onclick="toggleAll(this)"></th>
            <th>ID</th>
            <th>メールアドレス</th>
            <th>会社名</th>
            <th>担当者名</th>
            <th>電話番号</th>
            <th>建物用途</th>
            <th>用途区分</th>
            <th>延床面積 (㎡)</th>
            <th>構造種別</th>
            <th>計算種別</th>
            <th>都道府県</th>
            <th>市区町村</th>
            <th>希望納期</th>
            <th>特急</th>
            <th>概算金額</th>
            <th>コメント</th>
            <th>登録日時</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($optins as $optin): ?>
        <tr>
            <td><input type="checkbox" name="delete_ids[]" value="<?= $optin['id'] ?>"></td>
            <td><?= htmlspecialchars($optin['id']) ?></td>
            <td><?= htmlspecialchars($optin['email']) ?></td>
            <td><?= htmlspecialchars($optin['company']) ?></td>
            <td><?= htmlspecialchars($optin['person']) ?></td>
            <td><?= htmlspecialchars($optin['phone']) ?></td>
            <td><?= htmlspecialchars($optin['usage']) ?></td>
            <td><?= htmlspecialchars($optin['usage_type']) ?></td>
            <td><?= htmlspecialchars($optin['area']) ?></td>
            <td><?= htmlspecialchars($optin['structure']) ?></td>
            <td><?= htmlspecialchars($optin['calc_type']) ?></td>
            <td><?= htmlspecialchars($optin['prefecture']) ?></td>
            <td><?= htmlspecialchars($optin['city']) ?></td>
            <td><?= htmlspecialchars($optin['delivery_date']) ?></td>
            <td><?= $optin['is_express'] ? 'はい' : 'いいえ' ?></td>
            <td><?= htmlspecialchars($optin['estimate_price']) ?></td>
            <td><?= nl2br(htmlspecialchars($optin['comment'])) ?></td>
            <td><?= htmlspecialchars($optin['created_at']) ?></td>
            <td><a href="?delete_id=<?= $optin['id'] ?>" onclick="return confirm('本当に削除しますか？')">削除</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<button type="submit" onclick="return confirm('選択した項目を削除しますか？')">一括削除</button>
</form>

<script>
function toggleAll(source) {
    const checkboxes = document.querySelectorAll('input[name="delete_ids[]"]');
    checkboxes.forEach(cb => cb.checked = source.checked);
}
</script>
</body>
</html>