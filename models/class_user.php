<?php
require_once('adodb.inc.php');
require_once('adodb-active-record.inc.php');
require_once('autoload.php');

class User extends ADODB_Active_Record
{
	var $_table='users';
	var $password;
	var $email;
	var $lastError;
	
	function __get($name)
	{
		switch($name)
		{
			case 'userName':return $this->user_name;
			case 'loginName':return $this->login_name;
			case 'disabled':return $this->status==0;
			case 'enabled':return $this->status==1;
		}
	}
	
	function Insert()
	{
		$this->create_time=date('Y-m-d H:i:s');
		parent::Insert();
	}
	
	function Update()
	{
		$this->change_time=date('Y-m-d H:i:s');
		parent::Update();
	}
	
	function getAllPrivileges()
	{
		$privileges=array();
		$sql="select privilege_id,privilege_name,remarks from user_privilege a,privileges b 
				where user_id='{$this->id}' and a.privilege_id=b.id
			      union
			      select privilege_id,privilege_name,remarks from 
			       user_role ur,role_privilege rp,privileges p 
			      where ur.user_id='{$this->id}'
			      and ur.role_id=rp.role_id and rp.privilege_id=p.id";
		Db::Execute($sql);
		while($row=Db::FetchRow())
		{
			$privileges[]=array("id"=>$row['privilege_id'],"privilege_name"=>$row['privilege_name'],'remarks'=>$row['remarks']);
		}
		return $privileges;
	}

	function getPrivileges()
	{
		$privileges=array();
		if(empty($this->id)) return $privileges;
		$sql="select privilege_id,privilege_name,remarks from user_privilege a,privileges b 
				where user_id='{$this->id}' and a.privilege_id=b.id";
		Db::Execute($sql);

		while($row=Db::FetchRow())
		{
			$privileges[]=$row['privilege_id'];
		}
		return $privileges;
	}

	function getRoles()
	{
		$roles=array();
		if(empty($this->id)) return $roles;
		$sql="select role_id,role_name from 
			       user_role ur,roles r 
			      where ur.user_id='{$this->id}'
			      and ur.role_id=r.id";
		Db::Execute($sql);
		while($row=Db::FetchRow())
		{
			$roles[]=$row['role_id'];
		}
		return $roles;
	}
	
	function User($login_name='')
	{
		parent::__construct();
		$this->Load("login_name='$login_name'");
		return $this;
	}
	
	function Delete()
	{
		Db::Execute("delete from user_privilege where user_id='{$this->id}'");
		Db::Execute("delete from user_role where user_id='{$this->id}'");
		parent::Delete();
	}
	
	function chkPassword($pass)
	{
		if((empty($pass) && empty($this->password)) || md5($pass)==$this->password)
			return true;
		else 
			return false;
	}
	
	function setPassword($pass)
	{
		$this->password=md5($pass);
		Db::Execute("update users set password='{$this->password}' where id={$this->id}");
	}
	
	function addRole($role)
	{
		if(!is_numeric($role)) {
			$sql="select id from roles where role_name='$role'";
			Db::Execute($sql);
			if($row=Db::FetchRow()) {
				$role=$row[0];
			}
			else {
				$this->lastError='Incorrect role name.';
				return false;
			}
		}
		$sql="insert into user_role(user_id,role_id) values({$this->id},$role)";
		return Db::Execute($sql);
	}
	
	function delRole($role)
	{
		if(!is_numeric($role)) {
			$sql="select id from roles where role_name='$role'";
			Db::Execute($sql);
			if($row=Db::FetchRow()) {
				$role=$row[0];
			}
			else {
				$this->lastError='Incorrect role name.';
				return false;
			}
		}
		$sql="delete from user_role where user_id={$this->id} and role_id=$role";
		return Db::Execute($sql);
	}

	function hasRole($role)
	{
		if(is_numeric($role)) {
			$sql="select * from user_role where user_id={$this->id} and role_id=$role";
		}
		else {
			$sql="select user_id,role_id,role_name from 
			      user_role a,roles b
			      where a.user_id={$this->id}
			      and a.role_id=b.id
			      and b.role_name='$role'";
		}
		Db::Execute($sql);
		if(Db::FetchRow()) return true;
		else return false;
	}
	
	function addPrivilege($privilege)
	{
		if(!is_numeric($privilege)) {
			$sql="select id from privileges where privilege_name='$privilege'";
			Db::Execute($sql);
			if($row=Db::FetchRow()) {
				$privilege=$row[0];
			}
			else {
				$this->lastError='Incorrect privilege name.';
				return false;
			}
		}
		$sql="insert into user_privilege(user_id,privilege_id)
			      values({$this->id},$privilege";
	    return Db::Execute($sql);
	}
	
	function delPrivilege($privilege)
	{
		if(!is_numeric($privilege)) {
			$sql="select id from privileges where privilege_name='$privilege'";
			Db::Execute($sql);
			if($row=Db::FetchRow()) {
				$privilege=$row[0];
			}
			else {
				$this->lastError='Incorrect privilege name.';
				return false;
			}
		}
		$sql="delete from user_privilege where user_id={$this->id} and privilege_id=$privilege";
		return Db::Execute($sql);
	}
	
	function hasPrivilege($privilege)
	{
		if(is_numeric($privilege)) {
			$sql="select user_id,privilege_id from user_privilege where user_id={$this->id} and privilege_id=$privilege 
			      union all
			      select user_id,privilege_id from 
			       user_role ur,role_privilege rp 
			      where ur.user_id={$this->id} 
			      and ur.role_id=rp.role_id
			      and rp.privilege_id=$privilege ";
		}
		else {
			$sql="select user_id,privilege_id,privilege_name 
					from user_privilege up,privileges p
					where up.user_id={$this->id} 
				      and up.privilege_id=p.id
					  and p.privilege_name='$privilege'
			      union all
			      select user_id,privilege_id,privilege_name 
			      	from user_role ur,role_privilege rp,privileges p 
			        where ur.user_id={$this->id}
			          and ur.role_id=rp.role_id 
			          and rp.privilege_id=p.id
			          and p.privilege_name='$privilege' ";
			
		}
		Db::Execute($sql);
		if(Db::FetchRow()) return true;
		else return false;
	}
	
	function hasAllPrivileges($privileges)
	{
		if(empty($privileges)) return true;
		if(is_string($privileges))
		{
			$privileges=explode(',',$privileges);
		}
		else if(is_integer($privileges))
		{
			$privileges=array($privileges);
		}
		foreach($privileges as $pr)
		{
			if(!$this->hasPrivilege($pr)) return false;
		}
		return true;
	}

	function hasAnyPrivileges($privileges)
	{
		if(empty($privileges)) return true;
		if(is_string($privileges))
		{
			$privileges=explode(',',$privileges);
		}
		else if(is_integer($privileges))
		{
			$privileges=array($privileges);
		}
		foreach($privileges as $pr)
		{
			if($this->hasPrivilege($pr)) return true;
		}
		return false;
	}
	
	public static function newUser($login_name,$password,$user_name="",$email="")
	{
		$password=md5($password);
		$sql="insert into users(login_name,password,user_name,email,create_date,change_date)
		      values('$login_name','$password','$user_name','$email',now(),now())";
		Db::execute($sql);
	}
}

Db::Connect();
ADOdb_Active_Record::SetDatabaseAdapter(Db::$connection);
?>