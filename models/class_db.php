<?php
include_once('adodb.inc.php');    # load code common to ADOdb 
include_once('config.php');
class Db
{
	static $dbhost=DBHOST;
	static $dbuser=DBUSER;
	static $dbpassword=DBPASSWORD;
	static $dbname=DBNAME;
	static $resultset;
	static $connection=NULL; 

	static function Connect()
		{
			if(empty(self::$connection))
			{
				self::$connection=ADONewConnection('mysqli');
				self::$connection->PConnect(
				self::$dbhost,
				self::$dbuser,
				self::$dbpassword,
				self::$dbname) or die(self::$connection->ErrorMsg());
				self::$connection->Execute("set names 'utf8'");
			}
		}

	static function Execute($sql)
	{
		self::Connect();
		if(!self::$resultset=self::$connection->Execute($sql)) {
			die(self::$connection->ErrorMsg().":".$sql);
		}
		return self::$resultset;
	}
	
	
	static function FetchRow()
	{
		return self::$resultset->FetchRow();
	}
	
	static function ErrorMsg()
	{
		return self::$connection->ErrorMsg();
	}
	
	static function GetOne($sql)
	{
		return self::$connection->GetOne($sql);
	}
	
	public static function __callStatic($func,$args)
	{
		if(is_callable(array(self::$connection,$func)))
		{
			return call_user_func_array(array(self::$connection,$func), $args);
		}
		else if(is_callable(array(self::$resultset,$func)))
		{
			return call_user_func_array(array(self::$resultset,$func),$args);
		}
		else
		{
			throw new Exception("Undefined function $func");
		}
	}
}