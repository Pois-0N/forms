<?php
session_start();
//note ����MooPHP���
require dirname(__FILE__) . '/MooPHP/MooPHP.php';
//note:���������ļ�
require dirname(__FILE__) . '/MooPHP/MooConfig.php';
$admin = $db->getOne("SELECT * FROM {$tablePre}admin WHERE id='1'");
$pass = $admin['pass'];
/*if($_SESSION['admin'] != $pass) {
	msg("���½","subok","login.php");
	exit;
}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<link href="style.css" rel="stylesheet" type="text/css" />
<title>�׼��������ϵͳ</title>
</head>
<body>
<div align="center"><h3>�׼��������ϵͳ</h3></div>
<table width="100%" class="top"><tr><td><a href="?action=formlist">��������</a> �� <a href="?action=list&fid=1">�������</a><!-- �� <a href="?action=admin">����Ա���� </a> | <a href="login.php?action=exit">�˳����� </a>--></td></tr></table>
<SCRIPT LANGUAGE="JavaScript" src="js/jquery.pack.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" src="js/check.js"></SCRIPT>
<?php
if(!$action) $action = 'formlist';
if($action == 'delform') {
	$datalist = $db->getAll("SELECT id FROM {$tablePre}form_data WHERE fid=$fid ORDER BY id DESC");
	$typelist = $db->getAll("SELECT id FROM {$tablePre}form_type WHERE fid='$fid' ORDER BY id DESC");
	if(count($datalist)) {
		foreach($datalist AS $v) {
			$dataid[] = $v['id'];
		}
		$dataid = implode(",", $dataid);
		$dataid = preg_replace("/([\d]+)/", "'\\1'", $dataid);
		$db->query("DELETE FROM {$tablePre}form_data WHERE id IN ($dataid)");
	}
	if(count($typelist)) {
		foreach($typelist AS $t) {
			$typeid[] = $t['id'];
		}
		$typeid = implode(",", $typeid);
		$typeid = preg_replace("/([\d]+)/", "'\\1'", $typeid);
		$db->query("DELETE FROM {$tablePre}form_type WHERE id IN ($typeid)");
	}
	
	$db->query("DELETE FROM {$tablePre}form WHERE fid='$fid'");
	msg('����ɾ��', 'subok', '?action=formlist');
} elseif($action == 'deloption') {
	if($id && $fid) {
	$db->query("DELETE FROM {$tablePre}form_type WHERE fid='$fid' AND id='$id'");
	}
	msg('��ѡ����ɾ��', 'subok', '?action=listoption&fid='.$fid);

} elseif($action == 'display') {
	$f = $db->getOne("SELECT * FROM {$tablePre}form WHERE fid='$fid'");
	$display = $f['display'] ? 0 : 1;
	$db->query("UPDATE {$tablePre}form SET display='$display' WHERE fid='$fid'");
	msg('�����ɹ�', 'subok', '?action=formlist');

} elseif($action == 'delmsg') {
	if(count($del)) {
		$delid = implode(",", $del);
		$delid = preg_replace("/([\d]+)/", "'\\1'", $delid);
		$db->query("DELETE FROM {$tablePre}form_data WHERE id IN ($delid)");
	}
	msg('��������ɾ��', 'subok', '?action=list&fid='.$fid);

} elseif ($action == 'list') {
	$pageSize = 20;
	$currepage = ($_GET['page'] == '') ? 1 : intval($_GET['page']);
	$start = ($currepage - 1) * $pageSize;
	$num = $db->numRows("SELECT * FROM {$tablePre}form_data WHERE fid='$fid'");
	$list = $db->getAll("SELECT * FROM {$tablePre}form_data WHERE fid='$fid' ORDER BY id DESC LIMIT $start,$pageSize");
	echo showForm('formhead','?action=delmsg&fid='.$fid);
	echo '<table class="listtable" width="100%"><tr><td width="10%">����ɾ����</td><td width="85%" align="center">����</td></tr>';
	foreach($list AS $key=>$v) {
		$addtime = date("Y-m-d", $list[$key]['addtime']);
		$c = unserialize($v['content']);
		echo '<tr><td width="5%"><input name="del[]" type="checkbox" value="'.$list[$key]['id'].'"></td><td width="90%"><table width="100%">';
		if(count($c['title'])) {
			foreach($c['title'] AS $k => $title) {
				$content = $c['content'][$k];
				if(is_array($content)) {
					$content = implode(',', $content);
				}
				$content = str_replace("\r\n","<br>",$content);
				echo '<tr><td></td><td width="10%">'.$title.'</td><td width="90%">';
				if($k==0){
				echo '<a href="?action=liyi&fid='.$fid.'&keyword='.$content.'">'.$content.'</a>';
				}else{
				echo $content;
				}
				echo '</td></tr>';
				
			}
		}
		echo '<tr><td></td><td width="10%"><b>�ύ����</b></td><td width="90%">'.$addtime.'</td></tr></table>';

	}
	echo '</td></tr><tr><td width="10%"><!--<input type="button" id="checkall1" value="ȫѡ">--> <input type="button" id="checktog1" value="ȫѡ"> <input type="submit" name="delmsg" value="ɾ����ѡ" onclick="return confirm(&quot;ȷ��ɾ���������ز�����&quot;)"></td><td width="90%"><input type="button" value="��ӱ�" onclick="javascript:window.open(&quot;http://121.40.173.29/forms/index.php?fid='.$fid.'&quot;);">';
	echo '</td></tr></table>';
	echo '<div align="center">'.multi($num, $pageSize, $currepage,'admin.php?action=list&fid='.$fid).'</div><br />';
} elseif($action == 'formlist') {
	$formlist = $db->getAll("SELECT * FROM {$tablePre}form WHERE fid <> 1 ORDER BY fid DESC");
	echo '<table class="listtable" width="100%"><tr><td width="5%">fid</td><td width="10%">������</td><td width="75%" align="center">����</td><td width="10%">���ʱ��</td></tr>';
	foreach($formlist AS $form) {
		$fid = $form['fid'];
		$fname = $form['fname'];
		$display = $form['display'];
		$addtime = date("Y-m-d", $form['addtime']);
		$display = $display ? '����' : '����';
		$num = $db->numRows("SELECT * FROM {$tablePre}form_data WHERE fid='$fid'");
		echo '<tr><td width="5%">'.$fid.'</td><td width="10%"><a href="?action=list&fid='.$fid.'">'.$fname.'</a>��'.$num.'��</td><td width="75%" align="center">';
		echo '<a target="_blank" href="index.php?fid='.$fid.'">��ӱ�</a> | ';
		echo '<a href="?action=listoption&fid='.$fid.'">ѡ���б�</a></td>';// �� <a href="?action=display&fid='.$fid.'">'.$display.'</a> | <a href="?action=delform&fid='.$fid.'">ɾ��</a>
		echo '<td width="10%">'.$addtime.'</td></tr>';

	}
	echo '</table>';
	echo '<br /><h3>�½�������</h3><table class="listtable" width="100%">';
	echo showForm('formhead', '?action=addform');
	echo showForm('text', '������', '', '', '', 'fname');
	echo showForm('textarea', '��˵��', '', '', '', 'fmsg');
	echo showForm('submit', 'addform', '�½���').'</td></tr></table>';

} elseif ($action == 'addform') {
	if(!$content['fname']) {
		msg("�����Ʋ���Ϊ��");
	} else {
		$fname = $content['fname'];
		$fmsg = $content['fmsg'];
		$addtime = time();
		$db->query("INSERT INTO {$tablePre}form (fname,fmsg,addtime) VALUES ('$fname', '$fmsg', '$addtime')");
		msg('������','subok','?action=formlist');
	}
} elseif($action == 'editoption') {
	$optionmsg = $db->getOne("SELECT * FROM {$tablePre}form_type WHERE id='$id'");
	$type = $optionmsg['type'];
	if($type == 'text') {
		$stext = '�����ı�(text)';
		$diplay = 'display:none';
		}
	if($type == 'textarea') { 
		$stext = '�����ı�(textarea)';
		$diplay = "display:none";
		}
	if($type == 'select') {
		$stext = '������(select)';
		$diplay = "";
		}
	if($type == 'radio') {
		$stext = '��ѡ��(radio)';
		$diplay = "";
		}
	if($type == 'checkbox') {
		$stext = '��ѡ��(checkbox)';
		$diplay = "";
		}
	if($type == 'pass') {
		$stext = '�����(password)';
		$diplay = 'display:none';
		}

	if($type == 'hidden') {
		$stext = '������(hidden)';
		$diplay = 'display:none';
		}
		
	if($type == 'datetime') {
		$stext = 'ʱ�䣨datetime��';
		$diplay = 'display:none';
		}
	?>
<form action="?action=saveeditoption" method="post" name="myform">
<input type="hidden" name="id" value="<?=$optionmsg['id']?>">
<table cellpadding="0" cellspacing="0" border="0" width="100%" height="10">
	<tr>
		<td></td>
	</tr>
</table><table cellpadding="2" cellspacing="1" class="tableborder">
    <tr>
      <td class='tablerow'><strong>ѡ������</strong></td>
      <td class='tablerow'>
          <input type="text" name="title" value="<?=$optionmsg['title']?>">
	  </td>
    </tr>
    <tr>
      <td class='tablerow'><strong>ѡ��˵��</strong></td>
      <td class='tablerow'>
          <input type="text" name="msg" size="50" value="<?=$optionmsg['msg']?>">
	  </td>
    </tr>
    <tr> 
      <td class="tablerow"><strong>ѡ������</strong></td>
      <td class="tablerow">
<select name="type" onchange="javascript:formtypechange(this.value)">
<option value='<?=$type?>' selected><?=$stext?></option>
<option value='text'>�����ı�(text)</option>
<option value='textarea'>�����ı�(textarea)</option>
<option value='select'>������(select)</option>
<option value='radio'>��ѡ��(radio)</option>
<option value='checkbox'>��ѡ��(checkbox)</option>
<option value='pass'>�����(password)</option>
<option value='hidden'>������(hidden)</option>
<option value='datetime'>ʱ��(datetime)</option>
</select>
	 </td>
    </tr>
    <tr>
      <td class='tablerow'><strong>Ĭ��ֵ</strong></td>
      <td class='tablerow'>
          <textarea name='defaultvalue' rows='1' cols='50' onkeypress="javascript:checktextarealength('defaultvalue',30);"><?=$optionmsg['defaultvalue']?></textarea>
	  </td>
    </tr>
    <tr id='trOptions' style='<?=$diplay?>'>
      <td  class='tablerow'><strong>��ѡ�</strong><br>ÿ��һ��</td>
      <td class='tablerow'><textarea name='options' cols='40' rows='5' id='options'><?=$optionmsg['options']?></textarea></td>
    </tr>
       <tr>
      <td class='tablerow'><strong>����˳��</strong></td>
      <td class='tablerow'>
          <input type="text" name="orderid" value="<?=$optionmsg['orderid']?>">
	  </td>
    </tr>
    <tr> 
      <td class="tablerow"><strong>�Ƿ����</strong></td>
      <td class="tablerow">
��<input type="radio" name="ismust" value="1"> ��<input type="radio" name="ismust" value="0" checked>
	 </td>
    </tr>
    <tr> 
      <td class="tablerow"></td>
      <td class="tablerow"> <input type="submit" name="submit" value=" ȷ�� "> 
        &nbsp; <input type="reset" name="reset" value=" ��� "> </td>
    </tr>
  </form>
</table>
	<?php

} elseif ($action == 'saveaddoption') {
	//print_r($_POST);exit;
	if($fid && $type && $title) {
		$db->query("INSERT INTO {$tablePre}form_type (fid,orderid,type,title,msg,options,defaultvalue,ismust) VALUES ('$fid', '$orderid', '$type', '$title', '$msg', '$options', '$defaultvalue', '$ismust')");
		msg('������','subok','?action=listoption&fid='.$fid);
	}
} elseif ($action == 'saveeditoption') {
	if($type && $title) {
		$db->query("UPDATE {$tablePre}form_type SET orderid='$orderid',type='$type',title='$title',msg='$msg',options='$options',defaultvalue='$defaultvalue',ismust='$ismust' WHERE id='$id'");
		msg('�޸ĳɹ�','subok','?action=editoption&id='.$id);
	}
} elseif ($action == 'listoption') {
	$optionlist = $db->getAll("SELECT * FROM {$tablePre}form_type WHERE fid='$fid' ORDER BY orderid ASC");
	echo '<table class="listtable" width="100%"><tr><td width="10%">ѡ������</td><td width="80%" align="center">����</td></tr>';
	foreach($optionlist AS $option) {
		$id = $option['id'];
		$fid = $option['fid'];
		$title = $option['title'];
		echo '<tr><td width="10%">'.$title.'</td><td width="80%" align="center">';
		echo '<a href="?action=editoption&id='.$id.'">�޸�ѡ��</a> �� <a href="?action=deloption&fid='.$fid.'&id='.$id.'">ɾ��</a></td></tr>';

	}
	echo '</table>';
	?>
<br />
<h3>���ѡ��</h3>
<form action="?action=saveaddoption" method="post" name="myform">
<input type="hidden" name="fid" value="<?=$fid?>">
<table cellpadding="0" cellspacing="0" border="0" width="100%" height="10">
	<tr>
		<td></td>
	</tr>
</table><table cellpadding="2" cellspacing="1" class="tableborder">
    <tr>
      <td class='tablerow'><strong>ѡ������</strong></td>
      <td class='tablerow'>
          <input type="text" name="title" value="">
	  </td>
    </tr>
    <tr>
      <td class='tablerow'><strong>ѡ��˵��</strong></td>
      <td class='tablerow'>
          <input type="text" name="msg" size="50" value="">
	  </td>
    </tr>
    <tr> 
      <td class="tablerow"><strong>ѡ������</strong></td>
      <td class="tablerow">
<select name="type" onchange="javascript:formtypechange(this.value)">
<option value='text' selected>�����ı�(text)</option>
<option value='textarea'>�����ı�(textarea)</option>
<option value='select'>������(select)</option>
<option value='radio'>��ѡ��(radio)</option>
<option value='checkbox'>��ѡ��(checkbox)</option>
<option value='password'>�����(password)</option>
<option value='hidden'>������(hidden)</option>
<option value='datetime'>ʱ��(datetime)</option>
</select>
	 </td>
    </tr>
    <tr>
      <td class='tablerow'><strong>Ĭ��ֵ</strong></td>
      <td class='tablerow'>
          <textarea name='defaultvalue' rows='1' cols='50' onkeypress="javascript:checktextarealength('defaultvalue',30);"></textarea>
	  </td>
    </tr>
    <tr id='trOptions' style='display:none'>
      <td  class='tablerow'><strong>��ѡ�</strong><br>ÿ��һ��</td>
      <td class='tablerow'><textarea name='options' cols='40' rows='5' id='options'></textarea></td>
    </tr>
       <tr>
      <td class='tablerow'><strong>����˳��</strong></td>
      <td class='tablerow'>
          <input type="text" name="orderid" value="255">
	  </td>
    </tr>
    <tr> 
      <td class="tablerow"><strong>�Ƿ����</strong></td>
      <td class="tablerow">
      ��<input type="radio" name="ismust" value="1"> ��<input type="radio" name="ismust" value="0" checked>
	 </td>
    </tr>
    <tr> 
      <td class="tablerow"></td>
      <td class="tablerow"> <input type="submit" name="submit" value=" ȷ�� "> 
        &nbsp; <input type="reset" name="reset" value=" ��� "> </td>
    </tr>
  </form>
</table>

	<?php

} elseif($action == 'admin') {
	$admin = $db->getOne("SELECT * FROM {$tablePre}admin WHERE id='1'");
	echo '<br /><h3>����Ա</h3><table class="listtable" width="100%">';
	echo showForm('formhead', '?action=saveadmin');
	echo showForm('text', '�ʺ�', '', $admin['name'], '', 'name');
	echo showForm('pass', 'ԭ����', '', $admin['pass'], '', 'oldpass');
	echo showForm('pass', '������', '', '', '�粻�޸�������', 'newpass');
	echo showForm('submit', 'addform', '�ύ').'</td></tr></table>';

} elseif($action == 'saveadmin') {
	$name = $content['name'];
	$newpass = $content['newpass'];
	$oldpass = $content['oldpass'];
	$pass = $newpass ? MD5($newpass) : $oldpass;
	if($name && $pass) {
		$db->query("UPDATE {$tablePre}admin SET name='$name',pass='$pass' WHERE id='1'");
		msg("�޸ĳɹ�","subok","?action=admin");
	} else {
		msg("��Ϣ��д������");
	}
} elseif ($action == 'liyi') {
	//$keyword = iconv("utf-8","gb2312",$_GET['keyword']);
	$keyword = urldecode($_GET['keyword']);
	$pageSize = 20;
	$currepage = ($_GET['page'] == '') ? 1 : intval($_GET['page']);
	$start = ($currepage - 1) * $pageSize;
	$num = $db->numRows("SELECT * FROM {$tablePre}form_data WHERE fid='$fid'");
	$list = $db->getAll("SELECT * FROM {$tablePre}form_data WHERE fid='$fid' ORDER BY id DESC LIMIT $start,$pageSize");
	echo showForm('formhead','?action=delmsg&fid='.$fid);
	echo '<table class="listtable" width="100%"><tr><td width="10%">����ɾ����</td><td width="85%" align="center"><span style="color:red;">'.$keyword.'</span>�ĵ���</td></tr>';
	foreach($list AS $key=>$v) {
		$addtime = date("Y-m-d", $list[$key]['addtime']);
		$c = unserialize($v['content']);
	  if($c['content'][0]==$keyword){//ɸѡ����(ǰ�᣺ѡ���һ��Ϊ����)
		echo '<tr><td width="5%"><input name="del[]" type="checkbox" value="'.$list[$key]['id'].'"></td><td width="90%"><table width="100%">';
		if(count($c['title'])) {
			foreach($c['title'] AS $k => $title) {
				$content = $c['content'][$k];
				if(is_array($content)) {
					$content = implode(',', $content);
				}
				$content = str_replace("\r\n","<br>",$content);
				echo '<tr><td></td><td width="10%">'.$title.'</td><td width="90%">'.$content.'</td></tr>';
			}
		}
		echo '<tr><td></td><td width="10%"><b>�ύ����</b></td><td width="90%">'.$addtime.'</td></tr></table>';
      }//ɸѡ����
	}
	echo '</td></tr><tr><td width="10%"><!--<input type="button" id="checkall1" value="ȫѡ">--> <input type="button" id="checktog1" value="ȫѡ"> <input type="submit" name="delmsg" value="ɾ����ѡ" onclick="return confirm(&quot;ȷ��ɾ���������ز�����&quot;)"></td><td width="90%"><input type="button" value="��ӱ�" onclick="javascript:window.open(&quot;http://121.40.173.29/forms/index.php?fid='.$fid.'&quot;);">';
	echo '</td></tr></table>';
	echo '<div align="center">'.multi($num, $pageSize, $currepage,'admin.php?action=list&fid='.$fid).'</div><br />';
}
?>
<div align="center">Powered by ���� &copy;  2014-2015 <a href="http://www.ejiandai.com">�׼�Ͷ�ʹ��� Inc.</a></div> 
</body>
</html>