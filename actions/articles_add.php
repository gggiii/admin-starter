<?php
    if(count($_POST) > 0){
        if($admin->articles->add($_POST) === true){
            header("Location: ".$admin->articles->articlesPage);
        }else{
            echo 'Error adding';
        }
    }
?>
<div class="card card-pad">
    <form action="#" method="POST">
        <?php
            print_r($admin->articles->getAdd());
        ?>
    </form>
</div>
