<?php
class indexView
{
	static function render()
	{
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css"/>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="js/bootstrap.min.js"></script>

<style>
html,body
{
	height:100%;
}
</style>
<script>
$(function(){
	$("a.list-group-item").click(function(){
		$("a.list-group-item.active").removeClass("active");
		$(this).addClass("active");
	});
});
</script>
</head>
<body>
<div style="float:left;width:20%;max-width:200px;margin:20px;" id='menu' class='list-group'>
<a class='list-group-item' href='?c=user&a=admin' target='content'>USER ADMIN</a>
<a class='list-group-item' href='?c=role&a=admin' target='content'>ROLE ADMIN</a>
<a class='list-group-item' href='?c=user' target='_top'>LOGOUT</a>
</div>
<div style='overflow:hidden;height:100%;'>
<iframe name='content' id='content' style='margin:0;width:100%;height:100%;border:none;'></iframe>
</div>
<div style='clear:both;'></div>
</body>
</html>
<?php
	}
}
return 'indexView';