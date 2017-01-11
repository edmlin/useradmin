<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Signin</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/signin.css" rel="stylesheet">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">
<?php
if(isset($message))
{
	print "<div class='alert alert-danger' role='alert' id=login-message>$message</div>";
}
?>
      <form class="form-signin" role="form" action='?c=user&a=change_password' method=post>
        <h2 class="form-signin-heading">Change Password<br/>密码修改</h2>
        <input type="password" name=old_password class="form-control" placeholder="Old Password/旧密码" required autofocus>
        <input type="password" name=new_password1 class="form-control" placeholder="New Password/新密码" required>
        <input type="password" name=new_password2 class="form-control" placeholder="Confirm New Password/确认新密码" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Submit/提交</button>
      </form>

    </div> <!-- /container -->


  </body>
</html>