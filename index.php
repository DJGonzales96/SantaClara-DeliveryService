<?php
$highlight="HOME";
$title="Home - Santa Clara Menus";
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
include 'includes/header.php';
?>
<!DOCTYPE html>
<html>
    <head> 
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    </head>
    <main class="page landing-page;" style="width:100%;">
        <section class="scm-header clean-block clean-hero" style="background-image:url(&quot;assets/img/tech/ralph.png&quot;);color:rgba(9, 162, 255, 0.0);">
            <div class="text">
                <h2>Welcome to SCM <?php
                    if ($_SESSION['authenticated'] == true){
                        echo $username;
                    }
                    ?></h2>
        </section>
        <section class="clean-block clean-info dark">
            <div class="container">
                <div class="block-heading">
                        <p>The best UBER-for-restaurant like software. Sign up today and start working with us.</p>
                </div>
            </div>
        </section>
        <section class="clean-block clean-info dark">
            <div class="container">
            </div>
        </section>
    </main>
    <!-- for responsive menu -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>  
</html>
<?php
include 'includes/footer.php';
?>
