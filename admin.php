<?php
session_start();
//note 加载MooPHP框架
require dirname(__FILE__) . '/MooPHP/MooPHP.php';
//note:加载配置文件
require dirname(__FILE__) . '/MooPHP/MooConfig.php';
$admin = $db->getOne("SELECT * FROM {$tablePre}admin WHERE id='1'");
$pass = $admin['pass'];
/*if($_SESSION['admin'] != $pass) {
	msg("请登陆","subok","login.php");
	exit;
}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<link href="style.css" rel="stylesheet" type="text/css" />
<title>易简贷表单管理系统</title>
</head>
<body>
<div align="center"><h3>易简贷表单管理系统</h3></div>
<table width="100%" class="top"><tr><td><a href="?action=formlist">行政管理</a> ｜ <a href="?action=list&fid=1">财务管理</a><!-- ｜ <a href="?action=admin">管理员管理 </a> | <a href="login.php?action=exit">退出管理 </a>--></td></tr></table>
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
	msg('表单已删除', 'subok', '?action=formlist');
} elseif($action == 'deloption') {
	if($id && $fid) {
	$db->query("DELETE FROM {$tablePre}form_type WHERE fid='$fid' AND id='$id'");
	}
	msg('表单选项已删除', 'subok', '?action=listoption&fid='.$fid);

} elseif($action == 'display') {
	$f = $db->getOne("SELECT * FROM {$tablePre}form WHERE fid='$fid'");
	$display = $f['display'] ? 0 : 1;
	$db->query("UPDATE {$tablePre}form SET display='$display' WHERE fid='$fid'");
	msg('操作成功', 'subok', '?action=formlist');

} elseif($action == 'delmsg') {
	if(count($del)) {
		$delid = implode(",", $del);
		$delid = preg_replace("/([\d]+)/", "'\\1'", $delid);
		$db->query("DELETE FROM {$tablePre}form_data WHERE id IN ($delid)");
	}
	msg('表单内容已删除', 'subok', '?action=list&fid='.$fid);

} elseif ($action == 'list') {
	$pageSize = 20;
	$currepage = ($_GET['page'] == '') ? 1 : intval($_GET['page']);
	$start = ($currepage - 1) * $pageSize;
	$num = $db->numRows("SELECT * FROM {$tablePre}form_data WHERE fid='$fid'");
	$list = $db->getAll("SELECT * FROM {$tablePre}form_data WHERE fid='$fid' ORDER BY id DESC LIMIT $start,$pageSize");
	echo showForm('formhead','?action=delmsg&fid='.$fid);
	echo '<table class="listtable" width="100%"><tr><td width="10%">批量删除▼</td><td width="85%" align="center">内容</td></tr>';
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
		echo '<tr><td></td><td width="10%"><b>提交日期</b></td><td width="90%">'.$addtime.'</td></tr></table>';

	}
	echo '</td></tr><tr><td width="10%"><!--<input type="button" id="checkall1" value="全选">--> <input type="button" id="checktog1" value="全选"> <input type="submit" name="delmsg" value="删除所选" onclick="return confirm(&quot;确认删除？（慎重操作）&quot;)"></td><td width="90%"><input type="button" value="添加表单" onclick="javascript:window.open(&quot;http://121.40.173.29/forms/index.php?fid='.$fid.'&quot;);">';
	echo '</td></tr></table>';
	echo '<div align="center">'.multi($num, $pageSize, $currepage,'admin.php?action=list&fid='.$fid).'</div><br />';
} elseif($action == 'formlist') {
	$formlist = $db->getAll("SELECT * FROM {$tablePre}form WHERE fid <> 1 ORDER BY fid DESC");
	echo '<table class="listtable" width="100%"><tr><td width="5%">fid</td><td width="10%">表单名称</td><td width="75%" align="center">操作</td><td width="10%">添加时间</td></tr>';
	foreach($formlist AS $form) {
		$fid = $form['fid'];
		$fname = $form['fname'];
		$display = $form['display'];
		$addtime = date("Y-m-d", $form['addtime']);
		$display = $display ? '禁用' : '启用';
		$num = $db->numRows("SELECT * FROM {$tablePre}form_data WHERE fid='$fid'");
		echo '<tr><td width="5%">'.$fid.'</td><td width="10%"><a href="?action=list&fid='.$fid.'">'.$fname.'</a>（'.$num.'）</td><td width="75%" align="center">';
		echo '<a target="_blank" href="index.php?fid='.$fid.'">添加表单</a> | ';
		echo '<a href="?action=listoption&fid='.$fid.'">选项列表</a></td>';// ｜ <a href="?action=display&fid='.$fid.'">'.$display.'</a> | <a href="?action=delform&fid='.$fid.'">删除</a>
		echo '<td width="10%">'.$addtime.'</td></tr>';

	}
	echo '</table>';
	echo '<br /><h3>新建表单类型</h3><table class="listtable" width="100%">';
	echo showForm('formhead', '?action=addform');
	echo showForm('text', '表单名称', '', '', '', 'fname');
	echo showForm('textarea', '表单说明', '', '', '', 'fmsg');
	echo showForm('submit', 'addform', '新建表单').'</td></tr></table>';

} elseif ($action == 'addform') {
	if(!$content['fname']) {
		msg("表单名称不能为空");
	} else {
		$fname = $content['fname'];
		$fmsg = $content['fmsg'];
		$addtime = time();
		$db->query("INSERT INTO {$tablePre}form (fname,fmsg,addtime) VALUES ('$fname', '$fmsg', '$addtime')");
		msg('添加完成','subok','?action=formlist');
	}
} elseif($action == 'editoption') {
	$optionmsg = $db->getOne("SELECT * FROM {$tablePre}form_type WHERE id='$id'");
	$type = $optionmsg['type'];
	if($type == 'text') {
		$stext = '单行文本(text)';
		$diplay = 'display:none';
		}
	if($type == 'textarea') { 
		$stext = '多行文本(textarea)';
		$diplay = "display:none";
		}
	if($type == 'select') {
		$stext = '下拉框(select)';
		$diplay = "";
		}
	if($type == 'radio') {
		$stext = '单选框(radio)';
		$diplay = "";
		}
	if($type == 'checkbox') {
		$stext = '多选框(checkbox)';
		$diplay = "";
		}
	if($type == 'pass') {
		$stext = '密码框(password)';
		$diplay = 'display:none';
		}

	if($type == 'hidden') {
		$stext = '隐藏域(hidden)';
		$diplay = 'display:none';
		}
		
	if($type == 'datetime') {
		$stext = '时间（datetime）';
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
      <td class='tablerow'><strong>选项名称</strong></td>
      <td class='tablerow'>
          <input type="text" name="title" value="<?=$optionmsg['title']?>">
	  </td>
    </tr>
    <tr>
      <td class='tablerow'><strong>选项说明</strong></td>
      <td class='tablerow'>
          <input type="text" name="msg" size="50" value="<?=$optionmsg['msg']?>">
	  </td>
    </tr>
    <tr> 
      <td class="tablerow"><strong>选项类型</strong></td>
      <td class="tablerow">
<select name="type" onchange="javascript:formtypechange(this.value)">
<option value='<?=$type?>' selected><?=$stext?></option>
<option value='text'>单行文本(text)</option>
<option value='textarea'>多行文本(textarea)</option>
<option value='select'>下拉框(select)</option>
<option value='radio'>单选框(radio)</option>
<option value='checkbox'>多选框(checkbox)</option>
<option value='pass'>密码框(password)</option>
<option value='hidden'>隐藏域(hidden)</option>
<option value='datetime'>时间(datetime)</option>
</select>
	 </td>
    </tr>
    <tr>
      <td class='tablerow'><strong>默认值</strong></td>
      <td class='tablerow'>
          <textarea name='defaultvalue' rows='1' cols='50' onkeypress="javascript:checktextarealength('defaultvalue',30);"><?=$optionmsg['defaultvalue']?></textarea>
	  </td>
    </tr>
    <tr id='trOptions' style='<?=$diplay?>'>
      <td  class='tablerow'><strong>表单选项：</strong><br>每行一个</td>
      <td class='tablerow'><textarea name='options' cols='40' rows='5' id='options'><?=$optionmsg['options']?></textarea></td>
    </tr>
       <tr>
      <td class='tablerow'><strong>排列顺序</strong></td>
      <td class='tablerow'>
          <input type="text" name="orderid" value="<?=$optionmsg['orderid']?>">
	  </td>
    </tr>
    <tr> 
      <td class="tablerow"><strong>是否必填</strong></td>
      <td class="tablerow">
是<input type="radio" name="ismust" value="1"> 否<input type="radio" name="ismust" value="0" checked>
	 </td>
    </tr>
    <tr> 
      <td class="tablerow"></td>
      <td class="tablerow"> <input type="submit" name="submit" value=" 确定 "> 
        &nbsp; <input type="reset" name="reset" value=" 清除 "> </td>
    </tr>
  </form>
</table>
	<?php

} elseif ($action == 'saveaddoption') {
	//print_r($_POST);exit;
	if($fid && $type && $title) {
		$db->query("INSERT INTO {$tablePre}form_type (fid,orderid,type,title,msg,options,defaultvalue,ismust) VALUES ('$fid', '$orderid', '$type', '$title', '$msg', '$options', '$defaultvalue', '$ismust')");
		msg('添加完成','subok','?action=listoption&fid='.$fid);
	}
} elseif ($action == 'saveeditoption') {
	if($type && $title) {
		$db->query("UPDATE {$tablePre}form_type SET orderid='$orderid',type='$type',title='$title',msg='$msg',options='$options',defaultvalue='$defaultvalue',ismust='$ismust' WHERE id='$id'");
		msg('修改成功','subok','?action=editoption&id='.$id);
	}
} elseif ($action == 'listoption') {
	$optionlist = $db->getAll("SELECT * FROM {$tablePre}form_type WHERE fid='$fid' ORDER BY orderid ASC");
	echo '<table class="listtable" width="100%"><tr><td width="10%">选项名称</td><td width="80%" align="center">操作</td></tr>';
	foreach($optionlist AS $option) {
		$id = $option['id'];
		$fid = $option['fid'];
		$title = $option['title'];
		echo '<tr><td width="10%">'.$title.'</td><td width="80%" align="center">';
		echo '<a href="?action=editoption&id='.$id.'">修改选项</a> ｜ <a href="?action=deloption&fid='.$fid.'&id='.$id.'">删除</a></td></tr>';

	}
	echo '</table>';
	?>
<br />
<h3>添加选项</h3>
<form action="?action=saveaddoption" method="post" name="myform">
<input type="hidden" name="fid" value="<?=$fid?>">
<table cellpadding="0" cellspacing="0" border="0" width="100%" height="10">
	<tr>
		<td></td>
	</tr>
</table><table cellpadding="2" cellspacing="1" class="tableborder">
    <tr>
      <td class='tablerow'><strong>选项名称</strong></td>
      <td class='tablerow'>
          <input type="text" name="title" value="">
	  </td>
    </tr>
    <tr>
      <td class='tablerow'><strong>选项说明</strong></td>
      <td class='tablerow'>
          <input type="text" name="msg" size="50" value="">
	  </td>
    </tr>
    <tr> 
      <td class="tablerow"><strong>选项类型</strong></td>
      <td class="tablerow">
<select name="type" onchange="javascript:formtypechange(this.value)">
<option value='text' selected>单行文本(text)</option>
<option value='textarea'>多行文本(textarea)</option>
<option value='select'>下拉框(select)</option>
<option value='radio'>单选框(radio)</option>
<option value='checkbox'>多选框(checkbox)</option>
<option value='password'>密码框(password)</option>
<option value='hidden'>隐藏域(hidden)</option>
<option value='datetime'>时间(datetime)</option>
</select>
	 </td>
    </tr>
    <tr>
      <td class='tablerow'><strong>默认值</strong></td>
      <td class='tablerow'>
          <textarea name='defaultvalue' rows='1' cols='50' onkeypress="javascript:checktextarealength('defaultvalue',30);"></textarea>
	  </td>
    </tr>
    <tr id='trOptions' style='display:none'>
      <td  class='tablerow'><strong>表单选项：</strong><br>每行一个</td>
      <td class='tablerow'><textarea name='options' cols='40' rows='5' id='options'></textarea></td>
    </tr>
       <tr>
      <td class='tablerow'><strong>排列顺序</strong></td>
      <td class='tablerow'>
          <input type="text" name="orderid" value="255">
	  </td>
    </tr>
    <tr> 
      <td class="tablerow"><strong>是否必填</strong></td>
      <td class="tablerow">
      是<input type="radio" name="ismust" value="1"> 否<input type="radio" name="ismust" value="0" checked>
	 </td>
    </tr>
    <tr> 
      <td class="tablerow"></td>
      <td class="tablerow"> <input type="submit" name="submit" value=" 确定 "> 
        &nbsp; <input type="reset" name="reset" value=" 清除 "> </td>
    </tr>
  </form>
</table>

	<?php

} elseif($action == 'admin') {
	$admin = $db->getOne("SELECT * FROM {$tablePre}admin WHERE id='1'");
	echo '<br /><h3>管理员</h3><table class="listtable" width="100%">';
	echo showForm('formhead', '?action=saveadmin');
	echo showForm('text', '帐号', '', $admin['name'], '', 'name');
	echo showForm('pass', '原密码', '', $admin['pass'], '', 'oldpass');
	echo showForm('pass', '新密码', '', '', '如不修改请留空', 'newpass');
	echo showForm('submit', 'addform', '提交').'</td></tr></table>';

} elseif($action == 'saveadmin') {
	$name = $content['name'];
	$newpass = $content['newpass'];
	$oldpass = $content['oldpass'];
	$pass = $newpass ? MD5($newpass) : $oldpass;
	if($name && $pass) {
		$db->query("UPDATE {$tablePre}admin SET name='$name',pass='$pass' WHERE id='1'");
		msg("修改成功","subok","?action=admin");
	} else {
		msg("信息填写不完整");
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
	echo '<table class="listtable" width="100%"><tr><td width="10%">批量删除▼</td><td width="85%" align="center"><span style="color:red;">'.$keyword.'</span>的单据</td></tr>';
	foreach($list AS $key=>$v) {
		$addtime = date("Y-m-d", $list[$key]['addtime']);
		$c = unserialize($v['content']);
	  if($c['content'][0]==$keyword){//筛选名称(前提：选项第一项为名称)
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
		echo '<tr><td></td><td width="10%"><b>提交日期</b></td><td width="90%">'.$addtime.'</td></tr></table>';
      }//筛选结束
	}
	echo '</td></tr><tr><td width="10%"><!--<input type="button" id="checkall1" value="全选">--> <input type="button" id="checktog1" value="全选"> <input type="submit" name="delmsg" value="删除所选" onclick="return confirm(&quot;确认删除？（慎重操作）&quot;)"></td><td width="90%"><input type="button" value="添加表单" onclick="javascript:window.open(&quot;http://121.40.173.29/forms/index.php?fid='.$fid.'&quot;);">';
	echo '</td></tr></table>';
	echo '<div align="center">'.multi($num, $pageSize, $currepage,'admin.php?action=list&fid='.$fid).'</div><br />';
}
?>
<div align="center">Powered by 李羿 &copy;  2014-2015 <a href="http://www.ejiandai.com">易简投资管理 Inc.</a></div> 
</body>
</html>