<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'adodb');
require_once('autoload.php');
require_once('auth.php');
require_once("controllers/controller.php");
$controller=isset($_REQUEST['c'])?$_REQUEST['c']:'';
$action=isset($_REQUEST['a'])?$_REQUEST['a']:'';
if(empty($controller))
{
	$controller='index';
}	
$controller=include "controllers/{$controller}.php";
$controller->handle($action);
?>
