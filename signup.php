<?php
$title="SIGNUP - Santa Clara Menus";
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
    $query = "INSERT INTO user (username, name, encrypted_password, isRestaurant) VALUES('$username', '$name', '$token', '$isRestaurant')";
    $result = $conn->query($query);
    if(!$result) die($conn->error);

    if(mysqli_affected_rows($conn))
    {   // Redirect to login page after signup is successful
        header("location: login.php");
    }
}
$highlight="SIGN UP";
include 'includes/header.php';
?>

 <html>
 <main class="page registration-page" style= "height: 100%;">
   <section class="clean-block clean-form dark" style="height: 100%;">
       <div class="container">
           <div class="block-heading">
               <h2 class="text-info">Sign Up</h2>
           </div>
           <form method="post" action="signup.php" enctype="multipart form-data">
               <div class="form-group"><label for="name">Enter Your Name</label><input class="form-control item" type="text" name="name"></div>
               <div class="form-group"><label for="username">Choose User Name</label><input class="form-control item"type="text" name="username"></div>
               <div class="form-group"><label for="password">Choose Password</label><input class="form-control item" type="password" name="password"></div>
               Are you a restaurant or a driver?<br>
               <div class="form-check form-check-inline">
                       <input type="radio" class="form-check-input" id="restaurant" name="role" value="restaurant">
                       <label class="form-check-label" for="restaurant">Restaurant</label>
                     </div>
                     <div class="form-check form-check-inline">
                       <input type="radio" class="form-check-input" id="driver" name="role" value="driver">
                       <label class="form-check-label" for="driver">Driver</label>
                     </div>
               <button class="btn btn-primary btn-block" type="submit" value="Submit">Submit</button>
               <div class="etc-login-form">
                <p>Already have an account?  <a href="login.php">Login here</a></p>
               </div>
             </form>
       </div>
   </section>
</main>
</html>
