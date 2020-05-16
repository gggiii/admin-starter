<?php
    $id = intval(htmlspecialchars($_GET['id']));
    if($admin->articles->delete($id)){
        header("Location: ".$admin->articles->articlesPage);
    }else{
        echo "errror deleting";
    }

?>