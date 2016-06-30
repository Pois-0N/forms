<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: Mysql.Class.php 2008-3-19 aming$
*/


!defined('IN_MOOPHP') && exit('Access Denied');


class MooMySQL
{
	var $queryCount = 0;
	var $conn;
	var $result;
	var $rsType = MYSQL_ASSOC;
	//note:��ѯʱ��
	var $queryTimes = 0;
	
	/**
	 * �������ݿ�
	 *
	 * @param string $dbhost
	 * @param string $dbdata
	 * @param string $dbuser
	 * @param string $dbpass
	 * @param blooean $dbOpenType
	 * @param string $dbcharset
	 */
	function connect($dbhost = '', $dbuser = '', $dbpass = '', $dbdata = '', $dbOpenType = false ,$dbcharset = 'utf8') {
		if($dbOpenType) {
			$this->conn = mysql_pconnect($dbhost, $dbuser, $dbpass) or die(mysql_errno()." : ".mysql_error());
		} else {
			$this->conn = mysql_connect($dbhost, $dbuser, $dbpass) or die(mysql_errno()." : ".mysql_error());
		}

		$mysql_version = $this->getMysqlVersion();

		if($mysql_version > '4.1') {
				global $charset, $dbcharset;
				$dbcharset = $dbcharset2 ? $dbcharset2 : $dbcharset;
				$dbcharset = !$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $dbcharset;
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $mysql_version > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && mysql_query("SET $serverset", $this->conn);
		}

		@mysql_select_db($dbdata, $this->conn);
	}
	
	/**
	 * �ر����ݿ����ӣ�����ʹ�ó�������ʱ�ù���ʧЧ
	 *
	 */
	function close() {
		return mysql_close($this->conn);
	}
	
	/**
	 * ���Ͳ�ѯ���
	 *
	 * @param string $sql
	 * @param string $type
	 * @return blooean
	 */
	function query($sql, $type = "ASSOC") {
		$this->rsType = $type != "ASSOC" ? ($type == "NUM" ? MYSQL_NUM : MYSQL_BOTH) : MYSQL_ASSOC;
		$this->result = mysql_query($sql, $this->conn);
		$this->queryCount++;
		if($this->result) {
			return $this->result;
		} else {
			return false;
		}
	}
	
	/**
	 * �������Ƚϴ������²�ѯ
	 *
	 * @param string $sql
	 * @param string $type
	 * @return blooean
	 */
	function bigQuery($sql, $type = "ASSOC") {
		$this->rsType = $type != "ASSOC" ? ($type == "NUM" ? MYSQL_NUM : MYSQL_BOTH) : MYSQL_ASSOC;
		$this->result = mysql_unbuffered_query($sql, $this->conn);
		$this->queryCount++;
		if($this->result) {
			return $this->result;
		}
		else {
			return false;
		}
	}
	
	/**
	 * ��ȡȫ������
	 *
	 * @param string $sql
	 * @param blooean $nocache
	 * @return array
	 */
	function getAll($sql = "", $nocache = false) {
		if($sql) {
			if($nocache) {
				$this->bigQuery($sql);
			} else {
				$this->query($sql);
			}
		}
		$rows = array();
		while($row = mysql_fetch_array($this->result, $this->rsType)) {
			$rows[] = $row;
		}
		return $rows;
	}
	
	/**
	 * ��ȡ��������
	 *
	 * @param string $sql
	 * @return array
	 */
	function getOne($sql = "") {
		if($sql) {
			$this->query($sql);
		}
		$rows = mysql_fetch_array($this->result, $this->rsType);
		return $rows;
	}
	
	/**
	 * mysql_fetch_array
	 *
	 * @param string $sql
	 * @return 
	 */
	function fetchArray($query) {
		return mysql_fetch_array($query, $this->rsType);
	}

	/**
	 * ȡ����һ�� INSERT ���������� ID 
	 *
	 * @param string $sql
	 * @return integer
	 */
	function insertId($sql = "") {
		if($sql) {
			$row = $this->getOne($sql);
			return $row;
		} else {
			return mysql_insert_id($this->conn);
		}
	}
	
	function insert($sql) {
		$this->result = $this->query($sql);
		$id = $this->insertId();
		return $id;
	}
	
	/**
	 * ȡ���е���Ŀ
	 *
	 * @param string $sql
	 * @return integer
	 */
	function numRows($sql = "") {
		if($sql) {
			$this->query($sql);
			unset($sql);
		}
		$row = mysql_num_rows($this->result);
		return $row;
	}
	
	/**
	 * ȡ�ý�������ֶε���Ŀ
	 *
	 * @param string $sql
	 * @return integer
	 */
	function numFields($sql = "") {
		if($sql) {
			$this->query($sql);
		}
		return @mysql_num_fields($this->result);
	}
	
	/**
	 * ȡ�ý����ָ���ֶε��ֶ��� 
	 *
	 * @param string $data
	 * @param string $table
	 * @return array
	 */
	function listFields($data, $table) {
		$row = @mysql_list_fields($data, $table, $this->conn);
		$count = mysql_num_fields($row);
		for($i = 0; $i < $count; $i++) {
			$rows[] = @mysql_field_name($row,$i);
		}
		return $rows;
	}
	
	/**
	 * �г����ݿ��еı�
	 *
	 * @return array
	 */
	function listTables($data) {
		$query = mysql_list_tables($data);
		$rows = array();
		while($row = @mysql_fetch_array($query)) {
			$rows[] = $row[0];
		}
		return $rows;
	}
	
	/**
	 * ȡ�ñ���
	 *
	 * @param string $table_list
	 * @param integer $i
	 * @return string
	 */
	function tableName($table_list, $i) {
		return @mysql_tablename($table_list, $i);
	}
	
	/**
	 * ת���ַ������ڲ�ѯ
	 *
	 * @param string $char
	 * @return string
	 */
	function escapeString($char) {
		if(!$char) {
			return false;
		}
		return @mysql_escape_string($char);
	}
	
	/**
	 * ȡ�����ݿ�汾��Ϣ
	 *
	 * @return string
	 */
	function getMysqlVersion() {
		return mysql_get_server_info();
	}
}