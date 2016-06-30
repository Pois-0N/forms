<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: MooPHP.php 104 2008-04-14 09:18:55Z kimi $
*/

define('IN_MOOPHP', TRUE);
//note MooPHP�ĺ��İ汾�����磺0.0.1
define('MOOPHP_VERSION', '0.0.1');
//note ���ڱ����ʵ��ļ�·�������磺D:\web\MooPHP
define('MOOPHP_ROOT', substr(__FILE__, 0, -11));
//note ���ڱ����ʵ��ļ�URL�����磺http://www.ccvita.com/MooPHP
define('MOOPHP_URL', strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], '/'))).'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
//note REQUEST_URI
define('REQUEST_URI', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : (isset($_SERVER['argv']) ? $_SERVER['PHP_SELF'].'?'.$_SERVER['argv'][0] : $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING']));
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
!defined('MOOPHP_TEMPLATE_DIR') && define('MOOPHP_TEMPLATE_DIR', 'Moo-templates');
!defined('MOOPHP_TEMPLATE_COMPLIE_DIR') && define('MOOPHP_TEMPLATE_COMPLIE_DIR', 'Moo-data/templates');
!defined('MOOPHP_TEMPLATE_URL') && define('MOOPHP_TEMPLATE_URL', MOOPHP_URL.'/template');

// ����ָʾ PHP4 �� PHP5 �ĳ���
if(substr(PHP_VERSION, 0, 1) == '5') {
	define('PHP5', true);
	define('PHP4', false);
} else {
	define('PHP5', false);
	define('PHP4', true);
}

//note ��ֹ��ȫ�ֱ���ע��
if (isset($_REQUEST['GLOBALS']) OR isset($_FILES['GLOBALS'])) {
	exit('Request tainting attempted.');
}

//note MooPHP��������������
$_MooPHP = array();
//note ��ʼ���ౣ�����
$_MooClass = array();
//note ��ʼ��Block�������
$_MooBlock = array();
//note ���ݿ���Ϣ��ʼ��
$dbHost = $dbName = $dbUser = $dbPasswd = $dbPconnect = '';

require_once MOOPHP_ROOT.'/MooConfig.php';

//note ��GPC�������а�ȫ����
if (!MAGIC_QUOTES_GPC) {
	$_GET = MooAddslashes($_GET);
	$_POST = MooAddslashes($_POST);
	$_COOKIE = MooAddslashes($_COOKIE);
	$_REQUEST = MooAddslashes($_REQUEST);
	$_SERVER = MooAddslashes($_SERVER);
	$_FILES = MooAddslashes($_FILES);
}

/**
* �Զ�����Ĭ�����ļ��������������ʼ��
* @param string $classname - ����
* @return class
*/
function MooAutoLoad($classname) {
	global $_MooClass;

	if($_MooClass[$classname]) {
		return $_MooClass[$classname];
	} else {
		require_once MOOPHP_ROOT.'/libraries/'.$classname.'.class.php';
		$_MooClass[$classname]= & new $classname;
		return $_MooClass[$classname];
	}

}

/**
* Ϊ���������������ת��
* @param string $value - �ַ��������������
* @return array
*/
function MooAddslashes($value, $force = 0) {
	return $value = is_array($value) ? array_map('MooAddslashes', $value) : addslashes($value);
}


/**
* �������ַ�ת�� HTML ��ʽ������<a href='test'>Test</a>ת��Ϊ&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;
* @param $value - �ַ��������������
* @return array
*/
function MooHtmlspecialchars($value) {
	return is_array($value) ? array_map('MooHtmlspecialchars', $value) : preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1', str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $value));
}