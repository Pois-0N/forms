<?php
//note ����MooPHP���
require dirname(__FILE__) . '/MooPHP/MooPHP.php';

$C = MooAutoLoad('MooSeccode');
//���Լ������,��,��,λ��,����ΪĬ�� 80 20 4,SESSION['Moocode']
$C->outCodeImage();
?>

