<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: estimate_form.php');
    exit;
}

require_once 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/vendor/autoload.php';

$email = $_POST['email'] ?? '';
$company = $_POST['company'] ?? '';
$person = $_POST['person'] ?? '';
$phone = $_POST['phone'] ?? '';
$usage = $_POST['usage'] ?? [];
$usage_type = $_POST['usage_type'] ?? '';
$area = $_POST['area'] ?? '';
$structure = $_POST['structure'] ?? '';
$calc_type = $_POST['calc_type'] ?? '';
$prefecture = $_POST['prefecture'] ?? '';
$city = $_POST['city'] ?? '';
$delivery_date = $_POST['delivery_date'] ?? '';
$estimate_price = $_POST['estimate_price'] ?? '';
$comment = $_POST['comment'] ?? '';

$usage_str = implode(', ', $usage);

// 用途ごとの面積を文字列に整形
$usage_area_str = '';
if (isset($_POST['area_per_usage']) && is_array($_POST['area_per_usage'])) {
    foreach ($_POST['area_per_usage'] as $usage_name => $usage_area) {
        if (trim($usage_area) !== '') {
            $usage_area_str .= $usage_name . '：' . $usage_area . "㎡\n";
        }
    }
}

// プレースホルダー
$placeholders = [
    '{email}' => $email,
    '{company}' => $company,
    '{person}' => $person,
    '{phone}' => $phone,
    '{usage}' => $usage_str,
    '{usage_type}' => $usage_type,
    '{area}' => $area,
    '{structure}' => $structure,
    '{calc_type}' => $calc_type,
    '{prefecture}' => $prefecture,
    '{city}' => $city,
    '{delivery_date}' => $delivery_date,
    '{estimate_price}' => number_format($estimate_price),
    '{comment}' => $comment,
    '{usage_areas}' => $usage_area_str,
];

// テンプレート取得関数
function getTemplate($db, $name) {
    $stmt = $db->prepare("SELECT subject, body FROM email_templates WHERE name = ?");
    $stmt->execute([$name]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// コメントをoptin_listに反映（最新の同一メールアドレス対象）
$stmt = $db->prepare("UPDATE optin_list SET comment = ? WHERE email = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$comment, $email]);

// 管理者メールテンプレート
$admin_tpl = getTemplate($db, 'inquiry_admin');
$admin_subject = strtr($admin_tpl['subject'], $placeholders);
$admin_body = strtr($admin_tpl['body'], $placeholders);

// ユーザーメールテンプレート
$user_tpl = getTemplate($db, 'inquiry_user');
$user_subject = strtr($user_tpl['subject'], $placeholders);
$user_body = strtr($user_tpl['body'], $placeholders);

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';

try {
    // 共通設定
    $mail->isSMTP();
    $mail->Host = 'mail36.conoha.ne.jp'; // SMTPサーバーに変更
    $mail->SMTPAuth = true;
    $mail->Username = 'keisan@sm.ecocal.top'; // SMTPユーザー名に変更
    $mail->Password = 'Keisankeisan-2525'; // SMTPパスワードに変更
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('keisan@sm.ecocal.top', 'ECOcal（エコカル）');
    $mail->addReplyTo('eco@ecocal.top');

    // 管理者宛
    $mail->addAddress('eco@ecocal.top');
    $mail->Subject = $admin_subject;
    $mail->Body = $admin_body;
    $mail->send();

    // ユーザー宛
    $mail->clearAddresses();
    $mail->addAddress($email);
    $mail->Subject = $user_subject;
    $mail->Body = $user_body;
    $mail->send();
} catch (Exception $e) {
    echo "メール送信失敗: {$mail->ErrorInfo}";
    exit;
}

// 完了画面へ
header('Location: thank_you.php');
exit;
?>