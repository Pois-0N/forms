<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.
*/
//note ����MooPHP���
require_once dirname(__FILE__) . '/MooPHP/MooPHP.php';
//note:���������ļ�
require_once dirname(__FILE__) . '/MooPHP/MooConfig.php';


if($action == 'login') {
	SESSION_START();
	$code = $_SESSION['code'];
	$seccode = md5(strtoupper($seccode));
	$name = $content['name'];
	$pass = md5($content['pass']);
	if($code != $seccode) {
		msg("��֤�벻��ȷ");
	} else {
		$check = $db->numRows("SELECT * FROM {$tablePre}admin WHERE name='$name' AND pass='$pass'");
		if($check) {
			SESSION_START();
			SESSION_REGISTER("admin");
			$_SESSION['admin'] = $pass;
			header("Location:admin.php");
		} else {
			msg("�ʺŻ��������");
		}
	}
} elseif($action == 'exit') {
	SESSION_START();
	session_destroy();
	msg("�ɹ��˳�","subok","login.php");

} else {
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />

<link href="style.css" rel="stylesheet" type="text/css" />
<title>��ػ���ͨ�ñ�ϵͳ-�����½</title>
</head>
<body>
	<?php
	echo '<br /><h3>�����½</h3><table width="300">';
	echo showForm('formhead', '?action=login');
	echo showForm('text', '�ʺ�', '', '', '', 'name');
	echo showForm('pass', '����', '', '', '', 'pass');
	echo "<tr><td width=20%>��֤��</td>";
	echo "<td width=\"80%\"><input type=\"text\" name=\"seccode\" size=\"10\"> <img src=\"code.php\" alt=\"����������Ե������\" border=\"0\" onclick=\"this.src='code.php?update=' + Math.random()\" /></td></tr>";
	echo showForm('submit', 'login', '��½').'</td></tr></table>';
}
?>
</body>
</html>


