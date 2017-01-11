<?php
class UserController
{
	static function handle($action)
	{
		session_start();
		if(empty($action)) $action='login';
		if($action!='login' && $action!='change_password' && !$_SESSION['user']->hasPrivilege('admin'))
		{
			$view='accessdenied';
		}
		else
		switch($action)
		{
			case 'login':
				unset($_SESSION['user']);
				if(!isset($_POST['login_name'])) {
		//			setcookie("login_name","",time()-3600);
					setcookie("hash","",time()-3600);
					$view='login';	
				}
				else
				{
					$user=new User($_POST['login_name']);
					if(empty($user->id)) {
						$data['message']="Error: User '{$_POST['login_name']}' does not exist.";
						$view='login';
					}
					else if($user->disabled)
					{
						$data['message']="Error: User '{$_POST['login_name']}' disabled.";
						$view='login';
					}
					else if(!$user->chkPassword($_POST['password'])) {
						$data['message']='Error: Incorrect password.';
						$view='login';
					}
					else {
						$_SESSION['user']=$user;
						if(isset($_POST['remember_me']))
						{
							setcookie("login_name",$user->login_name,time()+60*60*24*365*10);
							setcookie("hash",md5($user->login_name.$_SERVER['HTTP_USER_AGENT']),time()+60*60*24*365*10);
						}
						else
						{
							setcookie("login_name","",time()-3600);
							setcookie("hash","",time()-3600);
						}
						header("Location: index.php");
						exit;
					}
				}
				break;
			case 'change_password':
				$user=$data['user']=$_SESSION['user'];
				if(isset($_REQUEST['old_password']))
				{
					if(!$user->chkPassword($_REQUEST['old_password'])) {
						$message='Incorrect password.';
					}
					else if($_REQUEST['new_password1']!=$_REQUEST['new_password2']) {
						$message='New passwords not match.';
					}
					else {
						$user->setPassword($_REQUEST['new_password1']);
						$message='Password successfully changed!';
					}
				}
				$view='change_password';
				break;
			case 'logout':
				unset($_SESSION['user']);
				$view='login';
				break;
			case 'admin':
				$data['users']=$_SESSION['user']->Find(1);
				$role=new Role;
				$data['roles']=$role->Find(1);
				$privilege=new Privilege;
				$data['privileges']=$privilege->Find(1);
				$view='useradmin';
				break;
			case 'edit':
				$view='user';
				break;
			case 'edit_single':
				$user=new User();
				$privilege=new Privilege();
				$privileges=$privilege->Find('1');
				$role=new Role();
				$roles=$role->Find('1');
				$user->Load("id='{$_REQUEST['user_id']}'");
				$uPrivileges=$user->getPrivileges();
				$uRoles=$user->getRoles();
				$view='edit_user';
				break;
			case 'submit':
			case 'save':
				$user=new User();
				$user->Load("id=".$_REQUEST['user']);
				try
				{
					if(trim($_REQUEST['login_name'])=='')
					{
						throw new Exception('Login name cannot be empty.');
					}
					if($_REQUEST['change_password']==1)
					{
						if($_REQUEST['password1']==$_REQUEST['password2'])
						{
							$user->password=md5($_REQUEST['password1']);
						}
						else
						{
							throw new Exception("Two new passwords not match.");
						}
					}
					if($user->login_name!=$_REQUEST['login_name'])
					{
						$loginNames=$user->Find("login_name='{$_REQUEST['login_name']}'");
						if(count($loginNames)>0)
						{
							throw new Exception("The login name has been used by another user.");
						}
						$user->login_name=$_REQUEST['login_name'];
					}
					$user->user_name=$_REQUEST['user_name'];
					$user->email=$_REQUEST['email'];
					$user->status=$_REQUEST['status'];
					$user->Save();
					
					Db::Execute("delete from user_privilege where user_id='{$user->id}'");
					Db::Execute("delete from user_role where user_id='{$user->id}'");
					
					foreach($_REQUEST['privileges'] as $p)
					{
						$values[]="('{$user->id}',$p)";
					}
					if(count($values)>0)
						Db::Execute("insert into user_privilege(user_id,privilege_id) values ".join(',',$values));
					unset($values);
					foreach($_REQUEST['roles'] as $r)
					{
						$values[]="('{$user->id}',$r)";
					}
					if(count($values)>0)
						Db::Execute("insert into user_role(user_id,role_id) values ".join(',',$values));
					$message="Record updated.";
				}
				catch(Exception $e)
				{
					$message="Error: ".$e->getMessage();
				}
				
				$data['users']=$_SESSION['user']->Find(1);
				$role=new Role;
				$data['roles']=$role->Find(1);
				$privilege=new Privilege;
				$data['privileges']=$privilege->Find(1);
				$data['message']=$message;
				$view='useradmin';
				break;
			case 'delete':
				$user=new User;
				$user->Load("id=".$_REQUEST['user_id']);
				$user->Delete();
				print "1";
				exit;
			case 'new':
				$user=new User;
				$user->user_name=$user->login_name=$_REQUEST['login_name'];
				$user->status=1;
				$user->Save();
				print json_encode($user);
				exit;
			case 'get':
				$user=new User;
				$user->Load("id=".$_REQUEST['user_id']);
				print json_encode($user);
				exit;
			case 'getprivileges':
				$user=new User;
				$user->Load("id=".$_REQUEST['user_id']);
				$privileges=$user->getPrivileges();
				print json_encode($privileges);
				exit;
			case 'getroles':
				$user=new User;
				$user->Load("id=".$_REQUEST['user_id']);
				$roles=$user->getRoles();
				print json_encode($roles);
				exit;
			case 'list':
				$user=new User();
				print json_encode($user->Find(1));
				exit;
		}
		$view=include "views/{$view}.php";
		$view::render($data);
	}
}
return 'UserController';
?>