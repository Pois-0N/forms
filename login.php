<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.
*/
//note 加载MooPHP框架
require_once dirname(__FILE__) . '/MooPHP/MooPHP.php';
//note:加载配置文件
require_once dirname(__FILE__) . '/MooPHP/MooConfig.php';


if($action == 'login') {
	SESSION_START();
	$code = $_SESSION['code'];
	$seccode = md5(strtoupper($seccode));
	$name = $content['name'];
	$pass = md5($content['pass']);
	if($code != $seccode) {
		msg("验证码不正确");
	} else {
		$check = $db->numRows("SELECT * FROM {$tablePre}admin WHERE name='$name' AND pass='$pass'");
		if($check) {
			SESSION_START();
			SESSION_REGISTER("admin");
			$_SESSION['admin'] = $pass;
			header("Location:admin.php");
		} else {
			msg("帐号或密码错误");
		}
	}
} elseif($action == 'exit') {
	SESSION_START();
	session_destroy();
	msg("成功退出","subok","login.php");

} else {
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />

<link href="style.css" rel="stylesheet" type="text/css" />
<title>奇矩互动通用表单系统-管理登陆</title>
</head>
<body>
	<?php
	echo '<br /><h3>管理登陆</h3><table width="300">';
	echo showForm('formhead', '?action=login');
	echo showForm('text', '帐号', '', '', '', 'name');
	echo showForm('pass', '密码', '', '', '', 'pass');
	echo "<tr><td width=20%>验证码</td>";
	echo "<td width=\"80%\"><input type=\"text\" name=\"seccode\" size=\"10\"> <img src=\"code.php\" alt=\"看不清楚可以点击更换\" border=\"0\" onclick=\"this.src='code.php?update=' + Math.random()\" /></td></tr>";
	echo showForm('submit', 'login', '登陆').'</td></tr></table>';
}
?>
</body>
</html>


