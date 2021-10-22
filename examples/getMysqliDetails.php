<?php
require __DIR__ . '/../vendor/autoload.php';

$phpsql = new GamerboyTR\PhpSql();
$details = $phpsql->getMysqliDetails();
?>
<p>Mysqli User = <?php echo $details['user'] ?></p>
<p>All Settings :</p>
<br>
<?php
foreach ($details as $key => $val) {
    echo "<p>$key = $val</p><br>";
}