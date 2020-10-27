<?php
require 'includes/conn.php';

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST")
{
 
    if(isset($_POST["name"]))
    {   // Sanitize name eventually
        $name = $_POST['name'];    
    }
    if(isset($_POST["username"])) 
    {   // Sanitize username eventually
        $username = $_POST['username'];
    }
    
    if(isset($_POST["password"]))
    {   // Sanitize, salt & hash password eventually
        $password = $_POST['password'];
        //$password = 'saltstring' . $password;        
        $token = hash('ripemd128', "$password");    
    }

    if(isset($_POST["role"]))
    {
    	$role = $_POST['role'];
    	// if (role == restaurant), isRestaurant = true
    	$isRestaurant = ($role == 'restaurant') ? TRUE : FALSE;
	}
    
    
    // TODO: For now, username is used in place of name
    // Attempt to insert
    $query = "INSERT INTO user (username, name, encrypted_password, isRestaurant) VALUES('$name', '$username', '$token', '$isRestaurant')";
    $result = $conn->query($query);
    if(!$result) die($conn->error);
    
    if(mysqli_affected_rows($conn))
    {   // Redirect to login page after signup is successful
        header("location: login.php");
    } 
}
?>

 <html>
 	<head>
 		<title>Sign Up</title>
 	</head>

 	<body>
    <form method="post" action="signup.php" enctype="multipart form-data">
    Enter your name:
    <input type="text" name="name"><br>
    Choose a username:
    <input type="text" name="username"><br>
    Choose a password:
    <input type="text" name="password"><br>
    Are you a restaurant or a driver?
    <input type="radio" id="restaurant" name="role" value="restaurant">
    <label for="restaurant">Restaurant</label>
    <input type="radio" id="driver" name="role" value="driver">
    <label for="driver">Driver</label><br><br>
    <input type="submit" value="Submit">
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
	</body>
</html>