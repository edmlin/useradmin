<?php
class Controller
{
	var $view=null;
	var $data=null;
	private function checkPrivilege($user,$action)
	{
		if(empty($this->privilegesRequired)) 
		{
			return true;
		}
		if(isset($this->privilegesRequired[$action]))
		{ 
			if(empty($this->privilegesRequired[$action]) || $user->hasPrivileges($this->privilegesRequired[$action])) 
			{
				return true;
			}
			else 
			{
				print 1;exit;
				return false;
			}
		}
		else if(isset($this->privilegesRequired['*']))
		{
			if(empty($this->privilegesRequired['*']) || $user->hasPrivileges($this->privilegesRequired['*']))
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
		else
		{
			return true;
		}
	}
	function handle($action)
	{
		if(empty($action)) $action='index';
		$user=$_SESSION['user'];
		if(!$this->checkPrivilege($user,$action))
		{
			$this->view='accessdenied';
		}
		else
		{
			$this->$action($_REQUEST);
		}
		if($this->view!=null)
		{
			$view=include "views/{$this->view}.php";
			$view->render($this->data);	
		}
	}
}