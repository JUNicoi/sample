<?php
require_once 'db.php';

$errors = [];
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $company = $_POST['company'] ?? '';
    $person = $_POST['person'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $usage = $_POST['usage'] ?? [];
    $area_inputs = $_POST['area_per_usage'] ?? [];
    $structure = $_POST['structure'] ?? '';
    $calc_type = $_POST['calc_type'] ?? '';
    $prefecture = $_POST['prefecture'] ?? '';
    $city = $_POST['city'] ?? '';
    $delivery_date = $_POST['delivery_date'] ?? '';
    $comment = $_POST['comment'] ?? '';

    // 単価マスタをDBから取得
    $unit_prices = [];
    $stmt = $db->query("SELECT usage_name, unit_price FROM price_master");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $unit_prices[$row['usage_name']] = $row['unit_price'];
    }

    // 計算処理
    $structure_factors = [];
    $stmt = $db->query("SELECT structure, multiplier FROM structure_multipliers");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $structure_factors[$row['structure']] = $row['multiplier'];
    }
    $settings = $db->query("SELECT express_multiplier, multi_usage_multiplier FROM global_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $express_ratio = $settings['express_multiplier'] ?? 1.0;
    $multi_usage_ratio = $settings['multi_usage_multiplier'] ?? 1.0;

    $selected_usages = array_map('trim', $usage);

    // calculate base price with all coefficients
    $base_price = 0;
    foreach ($selected_usages as $u) {
        $unit_price = $unit_prices[$u] ?? 1000;
        $u_area = floatval($area_inputs[$u] ?? 0);
        $base_price += $unit_price * $u_area;
    }

    $structure_ratio = $structure_factors[$structure] ?? 1.0;
    $base_price *= $structure_ratio;

    if (!empty($delivery_date) && strtotime($delivery_date)) {
        $days_diff = (strtotime($delivery_date) - strtotime(date('Y-m-d'))) / 86400;
        $is_express = ($days_diff < 7) ? 1 : 0;
    } else {
        $is_express = 0;
    }
    if ($is_express) {
        $base_price *= $express_ratio;
    }

    // Round up after all multipliers applied
    $estimate_price = ceil($base_price / 1000) * 1000;
    if ($estimate_price < 30000) {
        $estimate_price = 30000;
    }

    // DB登録
    $stmt = $db->prepare("INSERT INTO optin_list (email, company, person, phone, `usage`, area, structure, calc_type, prefecture, city, delivery_date, is_express, estimate_price, comment, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $email,
        $company,
        $person,
        $phone,
        implode(',', $selected_usages),
        array_sum(array_map('floatval', $area_inputs)),
        $structure,
        $calc_type,
        $prefecture,
        $city,
        $delivery_date,
        $is_express,
        $estimate_price,
        $comment
    ]);

    $result = $estimate_price;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>省エネ計算フォーム</title>
  <script>
    function toggleAreaInput(checkbox) {
      const container = checkbox.parentNode.parentNode;
      const input = container.querySelector("input[type='number']");
      const unit = container.querySelector(".unit-label");
      if (checkbox.checked) {
        input.style.display = 'inline';
        if (unit) unit.style.display = 'inline';
      } else {
        input.style.display = 'none';
        input.value = '';
        if (unit) unit.style.display = 'none';
      }
    }
  </script>
  <style>
    body {
      font-family: sans-serif;
      background-color: #f9f9f9;
      padding: 20px;
    }

    h2 {
      color: #333;
    }

    form {
      background-color: #fff;
      padding: 20px;
      border-radius: 6px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      max-width: 600px;
      margin: 0 auto;
    }

    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      margin-top: 20px;
      background-color: #007BFF;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 4px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0056b3;
    }

    .unit-label {
      margin-left: 5px;
      color: #555;
    }

    .estimation-result {
      background-color: #e6f7ff;
      padding: 15px;
      border-left: 4px solid #1890ff;
      margin-top: 20px;
      font-size: 1.2em;
    }

    .disclaimer {
      color: #666;
      font-size: 0.9em;
      margin-top: 10px;
    }

    .form-section {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
<h2>省エネ計算フォーム</h2>
<div class="form-section">
<form method="post">
  <label>建物用途（複数選択可）</label><br>
  <?php
  $post_usage = $_POST['usage'] ?? [];
  $stmt = $db->query("SELECT usage_name FROM price_master ORDER BY id ASC");
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $option = $row['usage_name'];
      $checked = in_array($option, $post_usage) ? "checked" : "";
      echo "<div style='margin-bottom:8px;'>";
      echo "<label><input type='checkbox' name='usage[]' value='" . htmlspecialchars($option) . "' $checked onchange='toggleAreaInput(this)'> " . htmlspecialchars($option) . "</label> ";
      $usage_area = htmlspecialchars($_POST['area_per_usage'][$option] ?? '');
      $display = in_array($option, $post_usage) ? 'inline' : 'none';
      echo "<input type='number' name='area_per_usage[" . htmlspecialchars($option) . "]' placeholder='面積' step='0.1' value='$usage_area' style='display:$display'>";
      echo " <span class='unit-label' style='display:$display'>㎡</span>";
      echo "</div>";
  }
  ?>

  <label>構造種別</label><br>
  <select name="structure" required>
    <option value="RC" <?= (($_POST['structure'] ?? '') === 'RC') ? 'selected' : '' ?>>RC</option>
    <option value="S" <?= (($_POST['structure'] ?? '') === 'S') ? 'selected' : '' ?>>S</option>
    <option value="木造" <?= (($_POST['structure'] ?? '') === '木造') ? 'selected' : '' ?>>木造</option>
    <option value="混構造" <?= (($_POST['structure'] ?? '') === '混構造') ? 'selected' : '' ?>>混構造</option>
  </select><br>

  <label>計算種別</label><br>
  <select name="calc_type" required>
    <option value="新築" <?= (($_POST['calc_type'] ?? '') === '新築') ? 'selected' : '' ?>>新築</option>
    <option value="増築" <?= (($_POST['calc_type'] ?? '') === '増築') ? 'selected' : '' ?>>増築</option>
  </select><br>

  <label>都道府県</label><br>
  <input type="text" name="prefecture" required value="<?= htmlspecialchars($_POST['prefecture'] ?? '') ?>"><br>

  <label>市区町村</label><br>
  <input type="text" name="city" required value="<?= htmlspecialchars($_POST['city'] ?? '') ?>"><br>

  <label>希望納期（日付選択）</label><br>
  <input type="date" name="delivery_date" required value="<?= htmlspecialchars($_POST['delivery_date'] ?? date('Y-m-d', strtotime('+21 days'))) ?>"><br>

  <label>会社名</label><br>
  <input type="text" name="company" required value="<?= htmlspecialchars($_POST['company'] ?? '') ?>"><br>

  <label>担当者名</label><br>
  <input type="text" name="person" required value="<?= htmlspecialchars($_POST['person'] ?? '') ?>"><br>

  <label>電話番号</label><br>
  <input type="tel" name="phone" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"><br>

  <label>メールアドレス</label><br>
  <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"><br><br>

  <button type="submit">計算する</button>
</form>
</div>

<div class="form-section">
<?php if ($result !== null): ?>
  <h3 class="estimation-result">概算金額：<?= number_format($result) ?> 円（税別）</h3>
  <p class="disclaimer">
    正確な金額は図面を見ないとわかりません。<br>
    大きく金額が変わる場合もございますので、下記の「見積もり依頼」よりお気軽にお問い合わせください。
  </p>
  <form method="post" action="inquiry_send.php">
    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
    <input type="hidden" name="company" value="<?= htmlspecialchars($_POST['company'] ?? '') ?>">
    <input type="hidden" name="person" value="<?= htmlspecialchars($_POST['person'] ?? '') ?>">
    <input type="hidden" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
    <input type="hidden" name="estimate_price" value="<?= $result ?>">
    <?php foreach ((array)($post_usage ?? []) as $u): ?>
      <input type="hidden" name="usage[]" value="<?= htmlspecialchars($u) ?>">
    <?php endforeach; ?>
    <?php foreach ((array)($_POST['area_per_usage'] ?? []) as $u => $a): ?>
      <input type="hidden" name="area_per_usage[<?= htmlspecialchars($u) ?>]" value="<?= htmlspecialchars($a) ?>">
    <?php endforeach; ?>
    <input type="hidden" name="structure" value="<?= htmlspecialchars($_POST['structure'] ?? '') ?>">
    <input type="hidden" name="calc_type" value="<?= htmlspecialchars($_POST['calc_type'] ?? '') ?>">
    <input type="hidden" name="prefecture" value="<?= htmlspecialchars($_POST['prefecture'] ?? '') ?>">
    <input type="hidden" name="city" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
    <input type="hidden" name="delivery_date" value="<?= htmlspecialchars($_POST['delivery_date'] ?? '') ?>">
    <label>コメント（任意）</label><br>
    <textarea name="comment" rows="4"><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea><br>
    <button type="submit">見積もり依頼をする</button>
  </form>
<?php endif; ?>
</div>
</body>
</html>