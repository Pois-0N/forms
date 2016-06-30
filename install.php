<?php
header("Content-type: text/html; charset=GBK");
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.
*/
//note 加载MooPHP框架
require dirname(__FILE__) . '/MooPHP/MooPHP.php';
//note:加载配置文件
require dirname(__FILE__) . '/MooPHP/MooConfig.php';
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ob_start();
define("HR", "<hr color='#D4D4D4' size='1' />");

function install_head($title = "") {
	echo "<html>\n";
	echo "<head>\n";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
	echo "<title>";
	if ($title) {
		echo $title . " - ";
	} 
	echo "欢迎安装PHP自定义表单系统，本程序基于MooPHP框架开发";
	echo "</title>\n";
	echo "<style type='text/css'>\nbody,table,tr,td,div{font:normal 12px Tahoma,Arial,宋体;margin:0px;padding:0px;}\n";
	echo "</style>\n";
	echo "</head>";
	echo "<body><br /><br /><br /><br /><br /><br />";
} 

function install_foot() {
	echo "</body>\n";
	echo "</html>";
	ob_end_flush();
	exit;
} 

function echo_msg($left = "", $right = "", $mouse = false) {
	$mouse = $mouse ? " onmouseover=\"this.style.backgroundColor='#EEEEEE'\" onmouseout=\"this.style.backgroundColor=''\"" : "";
	echo "<div" . $mouse . ">\n";
	echo "<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	if ($right) {
		echo "<td width='220px' align='right' height='30px'>&nbsp;" . $left . "</td>\n";
		echo "<td>" . $right . "</td>";
	} else {
		echo "<td align='center'>" . $left . "</td>";
	} 
	echo "</tr>";
	echo "</table>";
	echo "</div>";
} 

function echo_start() {
	echo "<div align='center'>";
	echo "<div style='border:1px #D4D4D4 solid;width:550px;padding:5px;'>";
} 

function echo_end() {
	echo "</div>";
} 

function form_start($url = "") {
	echo "<script type='text/javascript'>\n";
	echo "function chkalert()\n{\n\tq=confirm('请检查信息是否填写正确，按“确定”继续，按“取消”返回');\n";
	echo "\tif(q == '0')\n\t{\n\t\treturn false;\n\t}\n}\n";
	echo "</script>\n";
	echo "<div style='display:none;'><form method='post' action='" . $url . "' onsubmit='return chkalert();'></div>";
} 

function form_end() {
	echo "<div style='display:none;'></form></div>";
} 

function button($type = "button", $value = "下一步") {
	if ($type == "submit") {
		$button = "<input type='submit' value='" . $value . "'>";
	} else {
		$button = "<script type='text/javascript'>\nfunction tourl(id)\n{\nwindow.location.href='install.php?act='+id;\n}\n</script>\n";
		$button .= "<input type='button' value='" . $value . "' onclick=\"tourl('" . $type . "')\">";
	} 
	return $button;
} 

function safe_html($msg = "") {
	if (empty($msg)) {
		return false;
	} 
	$msg = str_replace('&amp;', '&', $msg);
	$msg = str_replace('&nbsp;', ' ', $msg);
	$msg = str_replace("'", "&#39;", $msg);
	$msg = str_replace('"', "&quot;", $msg);
	$msg = str_replace("<", "&lt;", $msg);
	$msg = str_replace(">", "&gt;", $msg);
	$msg = str_replace("\t", "&nbsp; &nbsp; ", $msg);
	$msg = str_replace("\r", "", $msg);
	$msg = str_replace("   ", "&nbsp; &nbsp;", $msg);
	return $msg;
} 


function error($title, $return_act = "install.php", $button_name = "返回上一步") {
	install_head("提示");
	echo_start();
	echo_msg("<br /><br /><br /><span style='font-size:17px;'>" . $title . "</span><br /><br /><br />");
	echo_msg(button($return_act, $button_name) . "<br /><br /><br />");
	echo_end();
	install_foot();
} 

function runquery($sql) {
	global $dbCharset, $tablePre, $db;
	$sql = str_replace("\r", "\n", str_replace('moo_', $tablePre, $sql));
	$ret = array();
	$num = 0;
	foreach(explode(";\n", trim($sql)) as $query) {
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
		}
		$num++;
	}
	unset($sql);

	foreach($ret as $query) {
		$query = trim($query);
		if($query) {

			if(substr($query, 0, 12) == 'CREATE TABLE') {
				$name = preg_replace("/CREATE TABLE ([a-z0-9_]+) .*/is", "\\1", $query);
				$db->query(createtable($query, $dbCharset));

			} else {
				$db->query($query);
			}

		}
	}
}

function createtable($sql, $dbcharset) {
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
	(mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=$dbcharset" : " TYPE=$type");
}

$sysact = $_GET["act"];

if (!$sysact) {
	$chkConn = @mysql_connect($dbHost, $dbUser, $dbPasswd);
	if(!$chkConn) {
		error("无法连接到数据库上，请检查配置是否正确...", "");
	}
	$chkDbName = mysql_select_db($dbName);
	if (!$chkDbName) {
		error("无法连接到数据库上，请检查配置是否正确...", "");
	} else {
		error("数据库信息配置正确，按‘下一步’开始安装！", "incsql", "下一步");
	}
} elseif ($sysact == "incsql") {
	$sql = @file_get_contents("moo_forms.sql");
	runquery($sql); 
	unset($sql); 
	error("恭喜您，程序已安装成功,默认管理帐号admin 密码:admin", "admin", "进入后台");
} elseif ($sysact == "admin") {
	ob_end_clean();
	@unlink("install.php");
	header("Location:admin.php");
} 

?>