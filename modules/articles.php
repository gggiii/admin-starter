<?php
    class Articles{
        function __construct($db) {
            //DB
            $this->db = $db;  //mandatory database library
            $this->usersTable = 'users';    //table with users
            $this->authDuration = 300*60;    //login session duration
            $this->defaultLoginPage = 'index.php';  //defaulat login page
            $this->username = 'loginUsrnm'; 
            //SETUP
            $this->tableName = 'articles';
            $this->tableSetup = array(
                'id'=>array(
                    "type"=>'nochange'
                ),
                'title'=>array(
                    "type"=>'simple',
                    "size"=>40
                ),
                'cont'=>array(
                    "type"=>'simple',
                    'additional'=>array('ckeditor')
                ),
                'date'=>array(
                    "type"=>'simple'
                ),
                'tags'=>array(
                    "type"=>'array',
                    "mode"=>'dynamic'
                ),
                'category'=>array(
                    "type"=>'array',
                    "mode"=>'static',
                    "data"=>'myCats'
                ),
                'excerpt'=>array(
                    "type"=>'simple',
                    "size"=>200
                )
            );
            $this->tableActions = '
                <a href="#" class="btn btn-green" data-test="{{{id}}}">Action</a>
                <a href="#" class="btn btn-red" data-test="{{{title}}}">Action</a>
                <a href="#" class="btn btn-blue">Action</a>
            ';
        }
        function getTable(string $fields, string $where = ''){
            /*
                args
                    which fields to get, string separated by commas e.g. id,title,cont


            */
            //FIELDS
            $finalFields = '';
            $fieldsArray = explode(',',$fields);
            foreach ($fieldsArray as $key => $value) {
                $finalFields .= '<th>'.$value.'</th>';
            }
            $finalString = ' <table class="table">
                        <tr>
                           '.$finalFields.'<th>ACTIONS</th>
                        </tr>';
            //GET VALUES FROM DATABASE
            $all = $this->db->query('select '.$fields.' from '.$this->tableName)->fetchAll();
            
            foreach ($all as $key => $article) {
                $finalString .= '<tr>';

                foreach ($article as $field => $val) {
                    $finalString .= '<td>'.$val.'</td>';
                    
                }
                /*

                        REPLACES VARIABLE IS CURLY PARENTHESEES WITH CORRESPONDING VALUES

                */
                $actionButtons = preg_replace_callback('/{{{[a-z]+}}}/',function ($matches) use ($article){
                    global $all;
                    $f = explode('{{{', $matches[0])[1];
                    $f = explode("}}}", $f)[0];
                    return $article[$f];
                }, $this->tableActions);
                $finalString .= '<td>'.$actionButtons.'</td>';
                $finalString .= '</tr>';
            }
            
                       
            $finalString .= '</table>';
            return $finalString;
        }
    }
?>