<?php
class UserController extends Controller
{
	var $privilegesRequired=array('index'=>'','login'=>'','change_password'=>'','*'=>'permission-setting');
	function index($param)
	{
		$this->login($param);
	}
	function login($param)
	{
		unset($_SESSION['user']);
		if(!isset($_POST['login_name'])) {
//			setcookie("login_name","",time()-3600);
			setcookie("hash","",time()-3600);
			$this->view='login';	
		}
		else
		{
			$user=new User($_POST['login_name']);
			if(empty($user->id)) {
				$this->data['message']="Error: User '{$_POST['login_name']}' does not exist.";
				$this->view='login';
			}
			else if($user->disabled)
			{
				$this->data['message']="Error: User '{$_POST['login_name']}' disabled.";
				$this->view='login';
			}
			else if(!$user->chkPassword($_POST['password'])) {
				$this->data['message']='Error: Incorrect password.';
				$this->view='login';
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
	}
	function change_password($param)
	{
		$user=$this->data['user']=$_SESSION['user'];
		if(isset($param['old_password']))
		{
			if(!$user->chkPassword($param['old_password'])) {
				$this->data['message']='Incorrect password.';
			}
			else if($param['new_password1']!=$param['new_password2']) {
				$this->data['message']='New passwords not match.';
			}
			else {
				$user->setPassword($param['new_password1']);
				$this->data['message']='Password successfully changed!';
			}
		}
		$this->view='change_password';
	}
	function admin($param)
	{
		$this->data['users']=$_SESSION['user']->Find(1);
		$role=new Role;
		$this->data['roles']=$role->Find(1);
		$privilege=new Privilege;
		$this->data['privileges']=$privilege->Find(1);
		$this->view='useradmin';
	}
	function submit($param)
	{
		$this->save($param);
	}
	function save($param)
	{
		$user=new User();
		$user->Load("id=".$param['user']);
		try
		{
			if(trim($param['login_name'])=='')
			{
				throw new Exception('Login name cannot be empty.');
			}
			if($param['change_password']==1)
			{
				if($param['password1']==$param['password2'])
				{
					$user->password=md5($param['password1']);
				}
				else
				{
					throw new Exception("Two new passwords not match.");
				}
			}
			if($user->login_name!=$param['login_name'])
			{
				$loginNames=$user->Find("login_name='{$param['login_name']}'");
				if(count($loginNames)>0)
				{
					throw new Exception("The login name has been used by another user.");
				}
				$user->login_name=$param['login_name'];
			}
			$user->user_name=$param['user_name'];
			$user->email=$param['email'];
			$user->status=$param['status'];
			$user->Save();
			
			Db::Execute("delete from user_privilege where user_id='{$user->id}'");
			Db::Execute("delete from user_role where user_id='{$user->id}'");
			
			foreach($param['privileges'] as $p)
			{
				$values[]="('{$user->id}',$p)";
			}
			if(count($values)>0)
				Db::Execute("insert into user_privilege(user_id,privilege_id) values ".join(',',$values));
			unset($values);
			foreach($param['roles'] as $r)
			{
				$values[]="('{$user->id}',$r)";
			}
			if(count($values)>0)
				Db::Execute("insert into user_role(user_id,role_id) values ".join(',',$values));
			$this->data['message']="Record updated.";
		}
		catch(Exception $e)
		{
			$this->data['message']="Error: ".$e->getMessage();
		}
		
		$this->data['users']=$_SESSION['user']->Find(1);
		$role=new Role;
		$this->data['roles']=$role->Find(1);
		$privilege=new Privilege;
		$this->data['privileges']=$privilege->Find(1);
		$this->data['user']=$user;
		$this->view='useradmin';
	}
	function delete($param)
	{
		$user=new User;
		$user->Load("id=".$param['user_id']);
		$user->Delete();
		print "1";
	}
	function create($param)
	{
		$user=new User;
		$user->user_name=$user->login_name=$param['login_name'];
		$user->status=1;
		$user->Save();
		print json_encode($user);
	}
	function getSingle($param)
	{
		$user=new User;
		$user->Load("id=".$param['user_id']);
		print json_encode($user);
	}
	function getAll($param)
	{
		$user=new User();
		print json_encode($user->Find(1));
	}
	function getprivileges($param)
	{
		$user=new User;
		$user->Load("id=".$param['user_id']);
		$privileges=$user->getPrivileges();
		print json_encode($privileges);
	}
	function getroles($param)
	{
		$user=new User;
		$user->Load("id=".$param['user_id']);
		$roles=$user->getRoles();
		print json_encode($roles);
	}

}
return new UserController;
?>