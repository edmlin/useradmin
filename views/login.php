<?php
class loginView
{
	function render($data=array())
	{
?>
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
if(isset($data['message']))
{
	print "<div class='alert alert-danger' role='alert' id=login-message>{$data['message']}</div>";
}
?>
      <form class="form-signin" role="form" action='?c=user&a=login' method=post>
        <h2 class="form-signin-heading">Please sign in/请登录</h2>
        <input type="text" name=login_name value='<?php if(isset($_COOKIE['login_name'])) echo $_COOKIE['login_name'];?>' class="form-control" placeholder="User Name/用户名" required autofocus>
        <input type="password" name=password class="form-control" placeholder="Password/密码" required>
        <div class="checkbox">
          <label>
            <input type="checkbox" name=remember_me value="remember-me" <?php if(isset($_COOKIE['login_name'])) echo 'checked';?>> Remember me/保持登录状态
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in/登录</button>
      </form>

    </div> <!-- /container -->


  </body>
</html>
<?php
	}
}
return new loginView;