<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.
*/
//note ����MooPHP���
require dirname(__FILE__) . '/MooPHP/MooPHP.php';
//note:���������ļ�
require dirname(__FILE__) . '/MooPHP/MooConfig.php';

if($action == 'savecontent') {
	if($secCode) {
		session_start();
		$num = MD5(strtoupper($_POST['seccode']));
		$sess = $_SESSION['code'];
		if($num != $sess) {
			msg('��֤�����');
			exit;
		}
	} else {
		$titleList = $db->getAll("SELECT title,ismust FROM {$tablePre}form_type WHERE fid='$fid' ORDER BY orderid ASC");

		foreach($titleList AS $k=>$t) {
			checkMust($titleList[$k]['ismust'], $content[$k]);
			$title[] = $t['title'];
		}
		$content = MooHtmlspecialchars($content);
		$array = array('content' => $content,'title' => $title);
		$intoArr = addslashes(serialize($array));
		$time = time();
		$db->query("INSERT INTO {$tablePre}form_data (fid,content,addtime) VALUES ('$fid', '$intoArr', '$time')");
		msg('��Ϣ���ύ', 'subok', 'admin.php?action=list&fid='.$fid);
	}
} else {
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<SCRIPT LANGUAGE="JavaScript" src="js/My97DatePicker/WdatePicker.js"></SCRIPT>
<link href="style.css" rel="stylesheet" type="text/css" />
<title>�׼��������ϵͳ</title>
</head>
<body>
	<?php
	$f = $db->getOne("SELECT * FROM {$tablePre}form WHERE fid='$fid' AND display='1'");
	if(!$f) exit;
	$formList = $db->getAll("SELECT * FROM {$tablePre}form_type WHERE fid='$fid' ORDER BY orderid ASC");
	$fmsg = str_replace("\r\n", "<br />" ,$f['fmsg']);
	$option = '<div align="center"><h2>'.$f['fname'].'</h2></div>';
	$option .= '<div><h5>'.$fmsg.'</h5> ע: * ��Ϊ������</div>';
	$option .= showForm('formhead', '?action=savecontent');
	$option .= '<input type="hidden" name="fid" value="'.$fid.'" />';
	$option .= '<table width="100%"><tr><td></td></tr>';
	foreach($formList AS $k=>$form) {
		$k = (!$k) ? 0 : $k++;
		$option .= showForm($form['type'], $form['title'], $form['options'], $form['defaultvalue'], $form['msg'] ,$k, $form['ismust']);
	}
	
	if($secCode) {
		$option .= "<tr><td width=20%>��֤��</td>";
		$option .= "<td width=\"80%\"><input type=\"text\" name=\"seccode\" size=\"10\"> <img src=\"code.php\" alt=\"����������Ե������\" border=\"0\" onclick=\"this.src='code.php?update=' + Math.random()\" /></td></tr>";
	}

	$option .= showForm('submit', 'submitcontent');
	$option .= '</table>';
	echo $option;
}

?>

<div align="center">Powered by ���� &copy;  2014-2015 <a href="http://www.ejiandai.com">�׼�Ͷ�ʹ��� Inc.</a></div> 
</body>
</html>