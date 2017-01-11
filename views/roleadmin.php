<?php
class roleAdminView
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
<link rel="stylesheet" type="text/css" href="css/chosen.min.css"/>
<script src="js/chosen.jquery.min.js"></script>

<script>
function getRoles()
{
	$.getJSON("?c=role&a=list",function(data){
		$("#role option").remove();
		$.each(data,function(key,value){
			$("#role").append("<option value='"+key+"'>"+value+"</option>");
		});
		$("#role").change();
	});
}

function getRole(roleId)
{
	$.getJSON("?c=role&a=get&role_id="+roleId,function(data){
		$("#role_name").val(data.name);
		$("#role_desc").val(data.description);
	});
}

function getPrivileges(roleId)
{
	$("input[name='privileges[]']").prop('checked',false);
	$.getJSON("?c=role&a=getprivileges&role_id="+roleId,function(data){
		$("#privilegeselect").val(data);
		$("#privilegeselect").trigger("chosen:updated");
	});
}
function getUsers(roleId)
{
	$("#userselect option").prop("selected",false);
	$.getJSON("?c=role&a=getusers&role_id="+roleId,function(data){
		$("#userselect").val(data);
		$("#userselect").trigger("chosen:updated");
	});
}
function createRole()
{
	var name;
	name=window.prompt("Please enter the new role's name:");
	if(name===null) return;
	$.getJSON("?c=role&a=new&role_name="+name,function(data){
		$("#role").append("<option value="+data.id+">"+data.role_name+"</option>");
		$("#role").val(data.id);
		$("#role_name").val(data.role_name);
		$("#role_desc").val(data.description);
		$("#privilegeselect").val([]);
		$("#privilegeselect").trigger("chosen:updated");
		$("#userselect").val([]);
		$("#userselect").trigger("chosen:updated");
	});
}

function deleteRole()
{
	if(!window.confirm("Are you sure to delete this role?")) return;
	$.get("?c=role&a=delete&role_id="+$("#role").val(),function(data){
		$("#role option:selected").remove();
		$("#role").change();
	});
}

$(function(){
	$('input').addClass('form-control input-xs');
	$('select').addClass('form-control input-xs');
	$("#tabs").tabs();
	$("#privilegeselect").chosen({width:'100%'});
	$("#userselect").chosen({width:'100%'});
	$("#role").change(function(){
		getRole($("#role").val());
		getPrivileges($("#role").val())
		getUsers($("#role").val());
	});
	
	$(".checkall").change(function(){
		$(this).closest("table").find("[name='privileges[]']").prop('checked',$(this).prop('checked'));
	});
	getRoles();
	$("#reset").click(function(){$("#role").change();});
})
</script>
</head>
<body>
<div class='panel panel-default' id=panel >
<div class='panel-body'>
<form id='form1' class='form-inline' action="?c=role&a=submit" method=post>
Role: <select name="role" id="role">
</select>
Name:<input name="role_name" id="role_name"/>
Description:<input name="role_desc" id='role_desc'/>
<input type=button class="btn-primary" value="Create" onclick="createRole()">
<input type=button class="btn-primary" value="Delete" onclick="deleteRole()">
<div id="tabs" style='margin-top:10px;'>
<ul>
<li><a href='#privileges'>Privileges</a></li>
<li><a href="#users">Users</a></li>
</ul>
<div id='privileges'>
<div style='width:100%'>
<select multiple name='privileges[]' id='privilegeselect' style='width:95%;' data-placeholder='Please select privileges'>
<?php
foreach($data['privileges'] as $pr)
{
	print "<option value='{$pr->id}'>{$pr->privilege_name}</option>";
}
?>
</select>
</div>
</div>
<div id='users'>
<div style='width:100%'>
<select multiple name="users[]" id="userselect" style='width:95%;' data-placeholder='Please select users'>
<?php
foreach($data['users'] as $user)
{
	print "<option value='{$user->id}'>{$user->user_name}</option>";
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
return 'roleAdminView';