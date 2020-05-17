<!DOCTYPE html>
<html>
    <head>
        <title>Select file</title>
        <style>
            *{
                padding: 0px;
                margin: 0px;
                box-sizing: border-box;
            }
            table{
                width: 100vw;
            }
            td{
                width: 33%;
            }
            img{
                width: 100%;
            }
        </style>
    </head>
    <body>
        <table>
            <?php
                $path = (isset($_GET['p']))?$_GET['p']:'';
                $formats = (isset($_GET['f']))?explode(',',$_GET['f']):array();
                $dir = array_diff(scandir('uploads/'.$path), array('.','..'));
                $items = array();
                if(count($formats) > 0){
                    foreach ($dir as $key => $value) {
                        foreach ($formats as $k => $v) {
                            if(strrpos($value, $v) !== false){
                                $items[] = $value;
                            }
                            
                        }
                    }
                }else{
                    $items = $dir;
                }
                

                foreach ($items as $key => $item) {
                    echo "<tr><td><img src='uploads/{$path}/{$item}'></td><td>{$item}</td><td><button data-value='{$item}'>Select</button></td></tr>";
                }
            ?>
        </table>
    </body>
    <script>
        let btns = document.querySelectorAll('button');
        let returnto = new URL(window.location.href).searchParams.get('returnto')
        for (let i = 0; i < btns.length; i++) {
            const element = btns[i];
            element.onclick = ()=>{
                opener.document.querySelector('[name="'+returnto+'"]').value = element.getAttribute('data-value');
                self.close();
            }
        }
    </script>
</html>