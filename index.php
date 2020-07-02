<pre>
<form action="#" method="POST" enctype="multipart/form-data">
<?php
include 'app.php';
$app = new Admin();

if (isset($_FILES['new-file'])) {
    print_r($app->items->add(array(),$_FILES));
}



//$app->items->edit(1, array('edit-name'=>'asdasd', 'edit-number'=>'1000'))
echo $app->items->getNewField('file');
?>
<input type="submit">

</form>
</pre>
<script src="ckeditor/ckeditor.js"></script>