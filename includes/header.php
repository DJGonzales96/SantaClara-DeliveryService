<!DOCTYPE html>
<html style="height:100%">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title> <?php echo $title; ?> </title>
    <link rel="stylesheet" href="assets/css/scm.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i,600,600i">
    <link rel="stylesheet" href="assets/fonts/simple-line-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.10.0/baguetteBox.min.css">
    <link rel="stylesheet" href="assets/css/smoothproducts.css">
</head>

<body style="height:100%;min-height:100%">
    <nav class="navbar navbar-light navbar-expand-lg fixed-top bg-white clean-navbar">
        <div class="container"><a class="navbar-brand logo" href="#">Santa Clara Menus</a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse"
                id="navcol-1">
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link <?php if ($highlight == "HOME") echo "active"; ?> " href="index.php">HOME</a></li>
                    <?php
                    if ($_SESSION['authenticated'] == true){
                      $active = ($highlight == "APP") ? 'active' : '';
                       echo '<li class="nav-item"><a class="nav-link "' . $active . 'href="app.php">APP</a></li>
                            <li class="nav-item"><a class="nav-link" href="logout.php">LOGOUT</a></li>';
                    } else {
                    $active =($highlight =="SIGN UP")? 'active' : '';
                    $active2=($highlight == "LOGIN")? 'active' :'';
                    echo '<li class="nav-item"><a class="nav-link ' . $active . '" href="signup.php">SIGN UP</a></li>
                          <li class="nav-item"><a class="nav-link ' . $active2 . '" href="login.php">LOGIN</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
