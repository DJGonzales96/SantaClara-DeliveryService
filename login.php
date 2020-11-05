<?php
$title="Login - Santa Clara Menus";
require 'includes/conn.php';
require 'includes/session.php';

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    if(isset($_POST["username"]))
    {   // Sanitize username eventually
        $username = $_POST['username'];
    }

    if(isset($_POST["password"]))
    {   // Sanitize, salt & hash password eventually
        $password = $_POST['password'];
        $token = hash('ripemd128', "$password");
    }

    // Validate username exists
    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = $conn->query($query);
    if(!$result) die($conn->error);

    elseif($result->num_rows)
    {
        $row = $result->fetch_array(MYSQLI_NUM);
        $result->close();

        // Validate password is correct
        if($token == $row[3])
        {
        	// password is correct
          $user_id = $row[0];
          $isRestaurant = $row[4];
          $_SESSION['user_id'] = $user_id;  // store ID of the signed in user
          $_SESSION['isRestaurant'] = $isRestaurant;
          $_SESSION['authenticated'] = true;
          $salt = substr (md5($password), 0, 2);
          $cookie = base64_encode ("$username:" . md5 ($token, $salt));
          setcookie ('scd-secret-cookie', $cookie, time() + (86400 * 30), '/');
        	// Redirect to main page
            header("location: index.php");
        }
         else
        {
            die("Invalid username/password combination.");
        }
    }
    else
    {
        die("Invalid username/password combination.");
    }
}
else {
  if($_SESSION['authenticated'] == true)
  {
      header("location: index.php");
  }
}
$highlight="LOGIN";
include 'includes/header.php';
?>


<html>
<main class="page login-page" style= "height: 100%;">
    <section class="clean-block clean-form dark" style="height: 100%;">
        <div class="container">
            <div class="block-heading">
                <h2 class="text-info">Welcome! Login with your credentials below.</h2>
            </div>
            <form method="post" action="login.php" enctype="multipart/form-data">
                <div class="form-group"><label for="username">User Name</label><input class="form-control item" type="text" name="username"></div>
                <div class="form-group"><label for="password">Password</label><input class="form-control" type="text" name="password"></div>
                <div class="form-group">
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="checkbox"><label class="form-check-label" for="checkbox">Remember me</label></div>
                </div><button class="btn btn-primary btn-block" type="submit" value="Login">Log In</button><br>
                <div class="etc-login-form">
                   <p>forgot your password? <a href="#">click here</a></p>
                   <p>new user? <a href="signup.php">create new account</a></p>
                </div></form>
        </div>
    </section>
</main>
</html>
