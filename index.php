
<?php
    include 'app.php';
    $admin = new Admin();

    print_r($admin->auth->login('test','test'));
   
?>

