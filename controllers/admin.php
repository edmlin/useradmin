<?php
include_once("auth.php");
if(!$_SESSION["user"]->hasPrivilege("admin"))
{
	$view="accessdenied";
}
else
{
	$view="admin";
}
include "views/view_{$view}.php";