<pre>
<?php
include 'app.php';
$admin = new Admin();
// print_r($admin->auth->logout());
//print_r(openssl_get_cipher_methods());
// print_r($admin->auth->login('test','test'));
print_r($admin->auth->getUsername());

?>
</pre>