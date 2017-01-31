<?php
class userAdminView
{
	function render($data)
	{
?>
<!doctype html>
<html>
<head>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<meta charset="UTF-8">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css"/>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/select2.min.css"/>
<script src="js/select2.min.js"></script>
<script>
function getUsers()
{
	$.getJSON("?c=user&a=getAll",function(data){
		$("#user option").remove();
		$.each(data,function(key,value){
			$("#user").append("<option value='"+value.id+"'>"+value.user_name+"</option>");
		});
		$("#user").change();
	});
}

function getUser(userId)
{
	$.getJSON("?c=user&a=getSingle&user_id="+userId,function(data){
		$("#user_name").val(data.user_name);
		$("#login_name").val(data.login_name);
		$("#email").val(data.email);
		$("#status").val(data.status);
	});
}

function getPrivileges(userId)
{
	$("input[name='privileges[]']").prop('checked',false);
	$.getJSON("?c=user&a=getprivileges&user_id="+userId,function(data){
		$("#privilegeselect").val(data);
		$("#privilegeselect").trigger("change.select2");
	});
}
function getRoles(userId)
{
	$("#userselect option").prop("selected",false);
	$.getJSON("?c=user&a=getroles&user_id="+userId,function(data){
		$("#roleselect").val(data);
		$("#roleselect").trigger("change.select2");
	});
}
function createUser()
{
	var name;
	name=window.prompt("Please enter the new user's login:");
	if(name===null) return;
	$.getJSON("?c=user&a=create&login_name="+name,function(data){
		$("#user").append("<option value="+data.id+">"+data.user_name+"</option>");
		$("#user").val(data.id);
		$("#user_name").val(data.user_name);
		$("#login_name").val(data.login_name);
		$("#email").val("");
		$("#status").val(1);
		$("#privilegeselect").val([]);
		$("#privilegeselect").trigger("change.select2");
		$("#roleselect").val([]);
		$("#roleselect").trigger("change.select2");
	});
}

function deleteUser()
{
	if(!window.confirm("Are you sure to delete this user?")) return;
	$.get("?c=user&a=delete&user_id="+$("#user").val(),function(data){
		$("#user option:selected").remove();
		$("#user").change();
	});
}

function togglePassword()
{
	if($("#change_password").is(":checked"))
	{
		$("#password1").prop("disabled",false);
		$("#password2").prop("disabled",false);
	}
	else
	{
		$("#password1").prop("disabled",true);
		$("#password2").prop("disabled",true);
	}
}
function showMessage(message)
{
	if(message!="")
	{
		$("#message div").html(message);
		if(message.match(/^error/i))
		{
			$("#message div").addClass('alert-danger');
		}
		else
		{
			$("#message div").addClass('alert-success');
		}
		$("#message").show();
	}
}
$(function(){
	$('input').addClass('form-control input-xs');
	$('select').addClass('form-control input-xs');
	$("#tabs").tabs();
	$("#privilegeselect").select2();
	$("#roleselect").select2();
	$("#user").change(function(){
		getUser($("#user").val());
		getPrivileges($("#user").val())
		getRoles($("#user").val());
		$("#change_password").prop('checked',false);
		togglePassword();
	});
	
	$(".checkall").change(function(){
		$(this).closest("table").find("[name='privileges[]']").prop('checked',$(this).prop('checked'));
	});
<?php
if(isset($data['user']))
{
?>
	$("#user").val(<?php echo $data['user']->id;?>);
<?php
}
?>
	$("#user").change();
	$("#reset").click(function(){$("#user").change();});
	$("#change_password").click(function(){
		togglePassword();
	});
	togglePassword();
	showMessage("<?php print $data['message'];?>");
})
</script>
</head>
<body>
<div id='message' style='display:none;padding:10px;'>
	<div class="alert"  role="alert">
	</div>
</div>
<div class='panel panel-default' id=panel >
<div class='panel-body'>
<form id='form1' class='form-inline' action="?c=user&a=submit" method=post>
<table>
<tr>
<td>Select User: </td>
<td><select name="user" id="user">
<?php
foreach($data['users'] as $user)
{
	print "<option value='$user->id'>$user->user_name</option>";
}
?>
</select>
<input type=button class="btn-primary" value="Create" onclick="createUser()">
<input type=button class="btn-primary" value="Delete" onclick="deleteUser()">
</td>
</tr>
<tr>
<td>Login:</td>
<td><input name="login_name" id="login_name" size=8/></td>
<td>User Name:</td>
<td><input name="user_name" id="user_name" size=15/></td>
</tr>
<tr>
<td>Email:</td>
<td><input name="email" id="email" size=15/></td>
<td>Status: </td>
<td>
<select name="status" id="status">
<option value=1>Enabled</option>
<option value=0>Disabled</option>
</select>
</td>
</tr>
<tr>
<td>
<input type=checkbox name='change_password' id='change_password' value=1/>Change Password:
</td>
<td><input type="password" name="password1" id="password1"/></td>
<td>Confirm Password:</td>
<td><input type="password" name="password2" id="password2"/></td>
</tr>
</table>
<div id="tabs" style='margin-top:10px;'>
<ul>
<li><a href="#roles">Roles</a></li>
<li><a href='#privileges'>Privileges</a></li>
</ul>
<div id='roles'>
<div style='width:100%'>
<select multiple name="roles[]" id="roleselect" style='width:100%;' data-placeholder='Please select roles'>
<?php
foreach($data['roles'] as $role)
{
	print "<option value='{$role->id}'>{$role->role_name}</option>";
}
?>
</select>
</div>
</div>
<div id='privileges'>
<div style='width:100%'>
<select multiple name='privileges[]' id='privilegeselect' style='width:100%;' data-placeholder='Please select privileges'>
<?php
foreach($data['privileges'] as $pr)
{
	print "<option value='{$pr->id}'>{$pr->privilege_name}</option>";
}
?>
</select>
</div>
</div>
<div style='text-align:center;margin-top:30px;'>
<input type="submit" class="btn-primary" value="Save">
<input type="button" class="btn-primary" id="reset" value="Reset">
</div>
</form>
</div>
</div>
</body>
</html>
<?php
	}
}
return new userAdminView;