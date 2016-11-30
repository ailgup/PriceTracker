<?php
session_start();
if (isset($_SESSION['username'])) {
    header("location:user.php");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">  

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
  </head>

  <body>
      <?php include "header.php";?>
    <div class="container">
        <div class="col-md-4"></div>
        <div class="col-md-4">
      <form class="form-signin" name="form1" method="post" action="login/checklogin.php">
        <h2 class="form-signin-heading">Please sign in</h2>
        <input name="myusername" id="myusername" type="text" class="form-control" placeholder="Username" autofocus>
        <br>
        <input name="mypassword" id="mypassword" type="password" class="form-control" placeholder="Password">
        <!-- The checkbox remember me is not implemented yet...
        <label class="checkbox">
          <input type="checkbox" value="remember-me"> Remember me
        </label>
        -->
        <br>
        <br>
        <button name="Submit" id="submit" class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <br>
        <a href="login/signup.php" role="button"  class="btn btn-lg btn-info btn-block">Create Account</a>

        <div id="message"></div>
      </form>
        </div>
                <div class="col-md-4"></div>

    </div> <!-- /container -->


    <!-- The AJAX login script -->
    <script src="login/js/login.js"></script>

  </body>
</html>
