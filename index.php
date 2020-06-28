<pre>
<?php
include 'app.php';
$app = new Admin();
print_r($app->items->add(array(
    'new-name'=>'wohohooooo',
    'new-text'=>'asdasdasd',
    'new-number'=>'80'
)));


?>
</pre>