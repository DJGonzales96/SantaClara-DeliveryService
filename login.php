<?php
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
?>


<html>
    <head>
    	<title>Log In</title>
    </head>
    <body>
    Welcome! Login with your credentials below.<br>
    <form method="post" action="login.php" enctype="multipart/form-data">
    Username:
    <input type="text" name="username"><br>
    Password:
    <input type="text" name="password"><br>
    <input type="submit" value="Login">
    </form>
	</body>
</html>
