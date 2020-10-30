<?php
if (!isset($_SESSION)) session_start();
$cookie = $_COOKIE['scd-secret-cookie'];
$content = base64_decode ($cookie);
list($username, $hashed_password) = explode (':', $content);
// Validate username exists
$query = "SELECT * FROM user WHERE username = '$username'";

$result = $conn->query($query);
if(!$result) die($conn->error);
elseif($result->num_rows)
{
    $row = $result->fetch_array(MYSQLI_NUM);
    $result->close();

    // Validate password is correct
    $password = $row[3];
}
// here you need to fetch real password from database based on username. ($password)
if (md5($password, substr(md5($password), 0, 2)) == $hashed_password)
{
    // you can consider use as logged in
    // do whatever you want :)
    $_SESSION['authenticated'] = true;
}
else {
        // you can consider use as logged in
        // do whatever you want :)
        // if want to kill here:
        // die("Not logged in");
        $_SESSION['authenticated'] = false;
    }
?>
