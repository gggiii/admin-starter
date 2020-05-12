<?php
    include('parts/head.php');
    if(!isset($_GET['action']) || !preg_match('/^[\p{L}\p{N}]+$/u',$_GET['action'])){
        fail:
?>

<div class="row">
        <div class="col-10">
            <div class="card">
               <?php
                   print_r($admin->articles->getTable('id,title'));
               ?>
               <!--
                <table class="table">
                    <tr>
                        <th>ID</th>
                        <th>NAME</th>
                        <th>DATE</th>
                        <th></th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Textik</td>
                        <td>Velky textik</td>
                        <td>
                            <a href="#" class="btn btn-green">Action</a>
                            <a href="#" class="btn btn-red">Action</a>
                            <a href="#" class="btn btn-blue">Action</a>
                        </td>
                    </tr>
                    
                </table>
                -->
            </div>
        </div>
        <div class="col-2">
            <div class="card card-pad">
                Lorem
            </div>
        </div>
</div>




<?php
    }else{
        $action = $_GET['action'];
        $query = explode('/', $_SERVER['PHP_SELF']);
        $script = array_pop($query);
        $script = explode('.',$script);
        $script = array_shift($script);
        if(file_exists('actions/'.$script.'_'.$action.'.php')){
            include('actions/'.$script.'_'.$action.'.php');
        }else{
            goto fail;
        }
    }
include('parts/footer.php');
?>
