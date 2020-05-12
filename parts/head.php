<?php
    include('app.php');
    $admin = new Admin();
    $admin->auth->loginVerify();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>ADMIN</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/css/main.css">
        <link rel="stylesheet" href="assets/css/bootstrap-grid.min.css">
    </head>
    <body class="navHidden">
        <nav>
            <ul>
                <li><a href="home.php"><i class="fas fa-columns"></i>Dasboard</a></li>
                <li><a href="articles.php"><i class="fas fa-th-list"></i>Articles</a></li>
                
            </ul>
        </nav>
        <header>
            <ul class="headerRight">
                <li>
                    <a href="#"><i class="fas fa-power-off"></i></a>
                </li>
                <li>
                    <div class="headerDropdown headerDropdownHidden">
                        <ul>
                            <li>
                                <a href="#">Link</a>
                            </li>
                            <li>
                                <a href="#">Link</a>
                            </li>
                            <li>
                                <a href="#">Link</a>
                            </li>
                            <li>
                                <a href="#">Link</a>
                            </li>
                        </ul>
                    </div>
                    <a href="#" class="dropdown headerDropdownToggle"><i class="fas fa-user-circle"></i></a>
                </li>
            </ul>
        </header>
        <main>