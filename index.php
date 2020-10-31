<?php
$highlight="HOME";
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
include 'includes/header.php';
?>
<style>
@media (min-width: 576px) {
  .clean-block.clean-hero {
      min-height: 480px;
  }
  .clean-block {
      padding-bottom: 0;
  }
}
body {
  background-color: #f6f6f6;
}
.clean-block {
    padding-bottom: 0;
}
</style>
  <main class="page landing-page">
        <section class="scm-header clean-block clean-hero" style="background-image:url(&quot;assets/img/tech/image4.jpg&quot;);color:rgba(9, 162, 255, 0.85);">
            <div class="text">
                <h2>Welcome to SCM</h2>
        </section>
        <section class="clean-block clean-info dark" >
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
        <?php
        include 'includes/footer.php';
        ?>
    </main>
</body>
</html>
