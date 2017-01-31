<?php
class RoleController extends Controller
{
	var $privilegesRequired=array('*'=>'permission-setting');
	function admin($param)
	{
		$role=new Role;
		$this->data['roles']=$role->Find(1);
		$user=new User;
		$this->data['users']=$user->Find(1);
		$privilege=new Privilege;
		$this->data['privileges']=$privilege->Find(1);
		$this->view='roleadmin';
	}
	function index($param)
	{
		$this->admin($param);
	}
	function submit($param)
	{
		Db::Execute("delete from role_privilege where role_id=".$param['role']);
		foreach($param['privileges'] as $id=>$pid)
		{
			Db::Execute("insert into role_privilege(role_id,privilege_id) values({$param['role']},$pid)");
		}
		Db::Execute("delete from user_role where role_id=".$param['role']);
		foreach($param['users'] as $uid)
		{
			Db::Execute("insert into user_role(role_id,user_id) values({$param['role']},$uid)");
		}
		$role=new Role;
		$role->Load("id=".$param['role']);
		$role->role_name=$param['role_name'];
		$role->description=$param['role_desc'];
		$role->Save();
		$this->data['roles']=$role->Find(1);
		$user=new User;
		$this->data['users']=$user->Find(1);
		$privilege=new Privilege;
		$this->data['privileges']=$privilege->Find(1);
		$this->data['role']=$role;
		$this->data['message']="Record updated.";
		$this->view='roleadmin';
	}
	function getAll($param)
	{
		$role=new Role;
		$result=new StdClass();
		foreach($role->Find(1) as $r)
		{
			$result->{$r->id}=$r->role_name;
		}
		print json_encode($result);
	}
	function getSingle($param)
	{
		$role=new Role;
		$role->Load("id=".$param['role_id']);
		$result=new StdClass();
		$result->name=$role->role_name;
		$result->description=$role->description;
		print json_encode($result);
	}
	function getprivileges($param)
	{
		$role=new Role;
		$role->Load("id=".$param['role_id']);
		print json_encode($role->getPrivileges());
	}
	function getusers($param)
	{
		$role=new Role;
		$role->Load("id=".$param['role_id']);
		print json_encode($role->getUsers());
	}
	function create($param)
	{
		$role=new Role;
		$role->role_name=$role->description=$param['role_name'];
		$role->Save();
		print json_encode($role);
	}
	function delete($param)
	{
		$role=new Role;
		$role->Load("id=".$param['role_id']);
		$role->Delete();
		print "1";
	}
}
return new RoleController;