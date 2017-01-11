<?php
require_once('adodb.inc.php');
require_once('adodb-active-record.inc.php');
class Role extends ADODB_Active_Record
{
	var $_table='roles';
	function getPrivileges()
	{
		$result=array();
		foreach(Db::GetAll("select privilege_id from role_privilege where role_id=".$this->id) as $row)
		{
			$result[]=$row[0];
		}
		return $result;
	}
	function getUsers()
	{
		$result=array();
		foreach(Db::GetAll("select user_id from user_role where role_id=".$this->id) as $row)
		{
			$result[]=$row[0];
		}
		return $result;
	}
}
Db::Connect();
ADOdb_Active_Record::SetDatabaseAdapter(Db::$connection);
