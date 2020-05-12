<div>
    <div class="card card-pad">
        <form action="#" method="POST">
            <?php
            if(!isset($_GET['id'])){
                header("Location: ".$admin->articles->articlesPage);
            }
            echo '<pre>';
            print_r($admin->articles->getEdit($_GET['id']));
            echo '</pre>';
            ?>
        </form>
    </div>
</div>