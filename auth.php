<?php
session_start();
if( isset($_REQUEST['c']) && isset($_REQUEST['a']) && ($_REQUEST['c']=='user') && ($_REQUEST['a']=='login') )
{
}
else if(isset($_SESSION['user']))
{
}
else if(isset($_COOKIE['login_name']) && 
        isset($_COOKIE['hash']) && 
		($_COOKIE['hash']==md5($_COOKIE['login_name'].$_SERVER['HTTP_USER_AGENT']))
		)
{
	$user=new User($_COOKIE['login_name']);
	$_SESSION['user']=$user;
}
else
{
	header("Location: index.php?c=user&a=login");
	exit;
}
?>