<pre>
<?php
include 'app.php';
$app = new Admin();


if(count($_FILES) > 0){    
    $app->items->add(array('new-name'=>'asdasd'), $_FILES);
}
?>
<form action="#" method="POST" enctype="multipart/form-data">
<?php
    echo $app->items->getEditField(1,'name', false,'asd');
?>
<input type="submit">
</form>
</pre>
<script src="ckeditor/ckeditor.js"></script>
