<?php
class RoleController
{
	static function handle($action)
	{
		if(empty($action)) $action='admin';
		$user=$_SESSION['user'];
		if(!$user->hasPrivilege("admin")) 
		{
			$view='accessdenied';
		}
		else
		switch($action)
		{
			case 'admin':
				$role=new Role;
				$data['roles']=$role->Find(1);
				$user=new User;
				$data['users']=$user->Find(1);
				$privilege=new Privilege;
				$data['privileges']=$privilege->Find(1);
				$view='roleadmin';
				break;
			case 'submit':
				Db::Execute("delete from role_privilege where role_id=".$_REQUEST['role']);
				foreach($_REQUEST['privileges'] as $id=>$pid)
				{
					Db::Execute("insert into role_privilege(role_id,privilege_id) values({$_REQUEST['role']},$pid)");
				}
				Db::Execute("delete from user_role where role_id=".$_REQUEST['role']);
				foreach($_REQUEST['users'] as $uid)
				{
					Db::Execute("insert into user_role(role_id,user_id) values({$_REQUEST['role']},$uid)");
				}
				$role=new Role;
				$role->Load("id=".$_REQUEST['role']);
				$role->role_name=$_REQUEST['role_name'];
				$role->description=$_REQUEST['role_desc'];
				$role->Save();
				$data['roles']=$role->Find(1);
				$user=new User;
				$data['users']=$user->Find(1);
				$privilege=new Privilege;
				$data['privileges']=$privilege->Find(1);
				$view='roleadmin';
				break;
			case 'list':
				$role=new Role;
				$result=new StdClass();
				foreach($role->Find(1) as $r)
				{
					$result->{$r->id}=$r->role_name;
				}
				print json_encode($result);
				exit;
			case 'get':
				$role=new Role;
				$role->Load("id=".$_REQUEST['role_id']);
				$result=new StdClass();
				$result->name=$role->role_name;
				$result->description=$role->description;
				print json_encode($result);
				exit;
			case 'getprivileges':
				$role=new Role;
				$role->Load("id=".$_REQUEST['role_id']);
				print json_encode($role->getPrivileges());
				exit;
			case 'getusers':
				$role=new Role;
				$role->Load("id=".$_REQUEST['role_id']);
				print json_encode($role->getUsers());
				exit;
			
			case 'new':
				$role=new Role;
				$role->role_name=$role->description=$_REQUEST['role_name'];
				$role->Save();
				print json_encode($role);
				exit;
			case 'delete':
				$role=new Role;
				$role->Load("id=".$_REQUEST['role_id']);
				$role->Delete();
				print "1";
				exit;
		}
		$view=include "views/{$view}.php";
		$view::render($data);
	}
}
return 'RoleController';