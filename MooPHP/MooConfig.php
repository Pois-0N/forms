<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: 2008-05-01 05:32:47Z aming $
*/

//note MySQL��������ַ��ͨ��Ϊlocalhost
$dbHost = 'localhost';

//note ϵͳʹ�õ�MySQL�����ݿ���������project_moophp
$dbName = 'forms';

//note MySQL���û���
$dbUser = 'root';

//note MySQL���û�����Ӧ������
$dbPasswd = '7EhT6QkA2YzN';

//note MySQL��ǰ׺
$tablePre = 'moo_';



//note:�������������޸�
//note MySQL���ݿ��ַ���
$dbCharset = 'GBK';
//note �Ƿ�Ϊ��������
$dbPconnect = 0;
$charset = 'GBK';
$secCode = 0;//�Ƿ�����֤�빦��0Ϊ�رգ�1Ϊ����
$db = MooAutoLoad('MooMySQL');
$db->connect($dbHost, $dbUser, $dbPasswd, $dbName, $dbPconnect, $dbCharset);
//���س��ú�����
require_once('Global.function.php');