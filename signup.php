<?php
$title="SIGNUP - Santa Clara Menus";
require 'includes/conn.php';
include 'model.php';

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST")
{

    if(isset($_POST["name"]))
    {   // Sanitize name eventually
        $name = $_POST['name'];
        if(!ctype_alpha($name))
            die("Invalid name. Please enter a valid alphabetical name.");
    }
    if(isset($_POST["username"]))
    {   // Sanitize username eventually
        $username = $_POST['username'];
        if(!ctype_alnum($username))
            die("Invalid username. Please choose an alphanumeric username.");
    }

    if(isset($_POST["password"]))
    {   // Sanitize, salt & hash password eventually
        $password = $_POST['password'];
        if(!ctype_alnum($password) || length($password) < 8)
            die("Invalid password. Please enter an 8+ character, alphanumeric password.");
        //$password = 'saltstring' . $password;
        $token = password_hash("$password", PASSWORD_DEFAULT);
    }

    if(isset($_POST["role"]))
    {
        $role = $_POST['role'];
        $isRestaurant = ($role == 'restaurant' ? "1": "0");
    }

    if(isset($_POST["restaurantAddr"]))
    {
        $address = $_POST["restaurantAddr"];
        if(preg_match('/[\w #,-.:;\']/', $address))
            die("Invalid address. Please enter a valid address.");
        $mapsArray = getMapsLocationFromFriendlyAddress($address);
        $lat = $mapsArray[0];
        $long = $mapsArray[1];
    }

    // TODO: For now, username is used in place of name
    // Attempt to insert
    $query = "INSERT INTO user (username, name, encrypted_password, isRestaurant) VALUES('$username', '$name', '$token', '$isRestaurant')";
    $result = $conn->query($query);
    if(!$result) die($conn->error);

    if(mysqli_affected_rows($conn))
    {
        if($isRestaurant)
        {// If this is a restaurant user, set their starting address and t_id
            $new_user_info = dbUserGetByUsername($username);
            restaurantUpdateAddress($new_user_info[0], $lat, $long, $address);
        }
        // Redirect to login page after signup is successful
        header("location: login.php");
    }
}
$highlight="SIGN UP";
include 'includes/header.php';
?>

<html>
<main class="page registration-page" style= "height: 100%;">
    <section class="clean-block clean-form dark">
        <div class="container">
            <div class="block-heading">
                <h2 class="text-info">Sign Up</h2>
            </div>
            <form method="post" action="signup.php" enctype="multipart form-data">
                <div class="form-group"><label for="name">Enter Your Name</label><input placeholder="name"class="form-control item" type="text" name="name" id ="name"></div>

                <div class="form-group" id ="restaurantAddr" style="display:none; margin-bottom:0"><label for="restaurantAddr">Enter Your Address</label><input placeholder="e.g. 1 Washington Sq, San Jose, CA 95192" class="form-control item"  type="text" name="restaurantAddr" id ="userAddr">
                <p style="color:#6c757d; margin-top:0;font-size:12px"><i class="fas fa-lightbulb" style="color:#6c757d;"></i> No non-address symbols (‘!’, ‘?’, ‘$’, ‘^’, etc.)</p>
                </div>

                <div class="form-group" style="margin-bottom:0"><label for="username">Choose User Name</label><input placeholder="e.g. happyhummus160"class="form-control item"type="text" name="username" id ="userName"></div>
                <p style="color:#6c757d; margin-top:0; font-size:12px"><i class="fas fa-lightbulb" style="color:#6c757d;"></i> Alpha-numeric symbols only</p>

                <div class="form-group" style="margin-bottom:0"><label for="password">Choose Password</label><input placeholder="password" class="form-control item" type="password" name="password" id ="password"></div>
                <p style="color:#6c757d; margin-top:0; margin-bottom:0 ;font-size:12px"><i class="fas fa-lightbulb" style="color:#6c757d;"></i> Alpha-numeric symbols only</p>
                <p style="color:#6c757d; margin-top:0 ;font-size:12px"><i class="fas fa-lightbulb" style="color:#6c757d;"></i> Password must be more than 8 characters</p>

                Are you a restaurant or a driver?<br>
                <div class="form-check form-check-inline">
                    <input type="radio" class="form-check-input" id="restaurant" name="role" value="restaurant">
                    <label class="form-check-label" for="restaurant">Restaurant</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" class="form-check-input" id="driver" name="role" value="driver">
                    <label class="form-check-label" for="driver">Driver</label>
                </div>
                <button class="btn btn-primary btn-block" type="submit" value="Submit" id="submit" disabled>Submit</button>
                <div class="etc-login-form">
                    <p>Already have an account?  <a href="login.php">Login here</a></p>
                </div>
            </form>
        </div>
    </section>
</main>
<!-- for responsive menu -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="https://kit.fontawesome.com/9d89888b17.js" crossorigin="anonymous"></script>
<script>
    var $radio=$("input:radio");
    var userType=null;
    $radio.change(function(){
        if($(this).val()=="restaurant")
        {
            $("#restaurantAddr").css("display","block");
            userType="restaurant";
        }
        else
        {
            $("#restaurantAddr").css("display","none");
            userType="driver";
        }
        checkstate();
    });

    var $name = $("#name");
    $name.keyup(checkstate);
    var $address = $("#userAddr");
    $address.keyup(checkstate);
    var $username = $("#userName");
    $username.keyup(checkstate);
    var $password = $("#password");
    $password.keyup(checkstate);

    function checkstate(){
        if(userType=="restaurant")
        {
            if($name.val().length > 0 && $address.val().length>0&&$username.val().length>0&&$password.val().length>0){
              $("#submit").removeAttr("disabled");
            }
            else {
              $("#submit").attr("disabled", "disabled");
            }
        }
        else if(userType=="driver")
        {
            if($name.val().length > 0 && $username.val().length>0&&$password.val().length>0)
            {
                $("#submit").removeAttr("disabled");
            }
            else {
              {
                  $("#submit").attr("disabled", "disabled");
              }
            }
        }
        else {
          {
              $("#submit").attr("disabled", "disabled");
          }
        }
    }
</script>
</html>
