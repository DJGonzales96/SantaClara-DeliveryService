<?php
<<<<<<< HEAD
require_once 'session.php';
=======
if (!isset($_SESSION)) session_start();
>>>>>>> master
$cookie = $_COOKIE['scd-secret-cookie'];
$content = base64_decode ($cookie);
list($username, $hashed_password) = explode (':', $content);
$query = "SELECT * FROM user WHERE username = '$username'";

$result = $conn->query($query);
<<<<<<< HEAD
if(!$result)
    die($conn->error);
elseif($result->num_rows) {
    $row = $result->fetch_array(MYSQLI_NUM);
    $result->close();
    $password = $row[3];
}
if (md5($password, substr(md5($password), 0, 2)) == $hashed_password) {
=======
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
>>>>>>> master
    $_SESSION['authenticated'] = true;
    $_SESSION['username'] = $username;
} else {
    $_SESSION['authenticated'] = false;
    $_SESSION['username'] = NULL;
    // we don't want to kill here because of index.php
    // so use inside api.php instead:
    //die("Not logged in");
}
<<<<<<< HEAD
?>
=======
else {
        // you can consider use as logged in
        // do whatever you want :)
        // if want to kill here:
        // die("Not logged in");
        $_SESSION['authenticated'] = false;
    }
?>
>>>>>>> master
