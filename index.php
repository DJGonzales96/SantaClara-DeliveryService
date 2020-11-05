<?php
$highlight="HOME";
$title="Home - Santa Clara Menus";
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
include 'includes/header.php';
?>
<main class="page landing-page">
    <section class="scm-header clean-block clean-hero" style="background-image:url(&quot;assets/img/tech/image4.jpg&quot;);color:rgba(9, 162, 255, 0.85);">
        <div class="text">
            <h2>Welcome to SCM</h2>
    </section>
    <section class="clean-block clean-info dark">
        <div class="container">
            <div class="block-heading">
            <?php
               if ($_SESSION['authenticated'] == true){
                  echo "Welcome " . $username;
               } else {
                  echo '<p>The best UBER-for-restaurant like software. Sign up today and start working with us.</p>';
               }
           ?>
            </div>
        </div>
    </section>
    <section class="clean-block clean-info dark">
        <div class="container">
        </div>
    </section>
</main>
<?php
include 'includes/footer.php';
?>
