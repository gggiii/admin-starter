
<?php
    include 'app.php';
    $admin = new Admin();

    $admin->auth->loginVerify();

    if(isset($_POST['loginSubmit'])){
        $username = htmlspecialchars($_POST['loginUsername']);
        $password = htmlspecialchars($_POST['loginPassword']);
        $login = $admin->auth->login($username, $password);
        if($login === true){
            header('Location: '.$admin->appHomepage);
        }
    }
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon icon -->
    <title>Admin</title>
    <!-- Custom CSS -->
   

    <link rel="stylesheet" href="assets/css/main.css">
    
</head>

    <body class="login-body">
        <div class="loginWrapper">
            <form action="#" method="POST" autocomplete="off">
                <input type="text" class="loginInput" placeholder="Username" name="loginUsername">
                <input type="password" class="loginInput" placeholder="Password" name="loginPassword">
                <input type="submit" class="loginSubmit" value="Login" name="loginSubmit">
            </form>
        </div>
    </body>
    <script src="assets/js/main.js"></script>
</html>
