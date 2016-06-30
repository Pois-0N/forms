<?php
//note 加载MooPHP框架
require dirname(__FILE__) . '/MooPHP/MooPHP.php';

$C = MooAutoLoad('MooSeccode');
//可以加入参数,宽,高,位数,留空为默认 80 20 4,SESSION['Moocode']
$C->outCodeImage();
?>

