<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: 2008-05-01 05:32:47Z aming $
*/

//note MySQL的主机地址，通常为localhost
$dbHost = 'localhost';

//note 系统使用的MySQL的数据库名，比如project_moophp
$dbName = 'forms';

//note MySQL的用户名
$dbUser = 'root';

//note MySQL的用户名对应的密码
$dbPasswd = '7EhT6QkA2YzN';

//note MySQL表前缀
$tablePre = 'moo_';



//note:以下内容无需修改
//note MySQL数据库字符集
$dbCharset = 'GBK';
//note 是否为持续链接
$dbPconnect = 0;
$charset = 'GBK';
$secCode = 0;//是否开启验证码功能0为关闭，1为开启
$db = MooAutoLoad('MooMySQL');
$db->connect($dbHost, $dbUser, $dbPasswd, $dbName, $dbPconnect, $dbCharset);
//加载常用函数库
require_once('Global.function.php');