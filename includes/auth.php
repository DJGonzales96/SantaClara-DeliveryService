<?php

require_once 'session.php';
$cookie = $_COOKIE['scd-secret-cookie'];
$content = base64_decode ($cookie);
list($username, $hashed_password) = explode (':', $content);
$query = "SELECT * FROM user WHERE username = '$username'";

$result = $conn->query($query);
if(!$result)
    die($conn->error);
elseif($result->num_rows) {
    $row = $result->fetch_array(MYSQLI_NUM);
    $result->close();
    $password = $row[3];
}
if (md5($password, substr(md5($password), 0, 2)) == $hashed_password) {
    $_SESSION['authenticated'] = true;
    $_SESSION['username'] = $username;
} else {
    $_SESSION['authenticated'] = false;
    $_SESSION['username'] = NULL;
    // we don't want to kill here because of index.php
    // so use inside api.php instead:
    //die("Not logged in");
}
?>