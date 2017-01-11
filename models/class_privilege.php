<?php
require_once('adodb.inc.php');
require_once('adodb-active-record.inc.php');
class Privilege extends ADODB_Active_Record
{
	var $_table='privileges';
}
Db::Connect();
ADOdb_Active_Record::SetDatabaseAdapter(Db::$connection);
?>