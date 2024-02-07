<?php require_once('../wp-load.php'); ?>
<?php require_once('./lib/function.php'); ?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow" />
	<title>増築BEI算出</title>
	<?php wp_head(); ?>
</head>

<form action="" method="post">
    <p>既存面積</p>
    <input type="text" name="ki">
    <p>増築面積</p>
    <input type="text" name="zou">
    <p>基準値</p>
    <input type="radio" name="syun" id="syun" value='1.1'>平成28 年3月31日以前の建物<br>
    <input type="radio" name="syun" id="syun" value='1.0'>平成28 年4月1日以降の建物<br>
    <input type="submit" value="submit">
    <p>係数1.2</p>
</form>

<?php
        $ki = $_POST['ki'];
        $zou = $_POST['zou'];
        $syun = $_POST['syun'];
        $sum = $ki + $zou;
        $ans = floor(((( $sum * $syun ) - ($ki * 1.2)) / $zou) *100) / 100 ;
?>
<p>既存面積</p>
<?= $ki ?>㎡
<p>増築面積</p>
<?= $zou ?>㎡
<p>増築後の延床面積</p>
<?= $sum ?>㎡
<p>省エネ基準</p>
<?= $syun ?>
<p>省エネ基準をクリアするために必要な増築部分のBEI</p>
BEI=<?= $ans ?>以下

<?php wp_footer(); ?>
</body>

</html>