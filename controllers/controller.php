<?php
class Controller
{
	var $view=null;
	var $data=null;
	var $actionMap=array();
	private function checkPrivilege($user,$action)
	{
		if(empty($this->privilegesRequired)) 
		{
			return true;
		}
		if(isset($this->privilegesRequired[$action]))
		{ 
			if(empty($this->privilegesRequired[$action]) || $user->hasAllPrivileges($this->privilegesRequired[$action])) 
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
			if(empty($this->privilegesRequired['*']) || $user->hasAllPrivileges($this->privilegesRequired['*']))
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
			if(isset($this->actionMap[$action])) $action=$this->actionMap[$action];
			$this->$action($_REQUEST);
		}
		if($this->view!=null)
		{
			$view=include "views/{$this->view}.php";
			$view->render($this->data);	
		}
	}
}