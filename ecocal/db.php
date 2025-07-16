<?php
// db.php
$host = 'mysql24.conoha.ne.jp';
$dbname = 'c1a73_ecocal';
$user = 'c1a73_ecocal_adm';
$password = 'ecoecocalcal25_';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('DB接続エラー: ' . $e->getMessage());
}
?>