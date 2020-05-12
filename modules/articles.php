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
            $this->articlesPage = 'articles.php';
            $this->tableSetup = array(
                'id'=>array(
                    "type"=>'nochange'
                ),
                'title'=>array(
                    "type"=>'simple',
                    "input"=>'text',
                    "size"=>40
                ),
                'cont'=>array(
                    "type"=>'simple',
                    "input"=>'textarea',
                    'additional'=>array('ckeditor')
                ),
                'date'=>array(
                    "type"=>'simple',
                    "input"=>'text',
                    "size"=>40
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
                    "input"=>'text',
                    "size"=>200
                )
            );
            $this->tableActions = '
                <a href="?action=edit&id={{{id}}}" class="btn btn-blue">Action</a>
                <a href="#" class="btn btn-red"">Action</a>
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

        function getEdit($id, $fields = null){
            $workingFields = array();
            //GET FIELDS WHICH WILL BE EDITED
            if($fields == null){
                foreach($this->tableSetup as $key=>$fieldVal){
                    if($fieldVal['type'] != 'nochange'){
                        $workingFields[$key] = $fieldVal;
                    }
                }
            }else {
                $f = explode(',',$fields);
                foreach($f as $val){
                    if($this->tableSetup[$val]['type'] != 'nochange'){
                        $workingFields[$val] = $this->tableSetup[$val];
                    }else{
                        trigger_error('NOCHANGE field "'.$val.'" can not be edited! ');
                    }
                }
            }
            //GET CURRENT VALUES
            $id = intval(htmlspecialchars($id));
            $selectFields = ($fields == null)?'*':$fields;
            $data = $this->db->query('select '.$selectFields.' from '.$this->tableName.' where id=?', $id)->fetchArray();
            //RETURN STRING
            $returnFields = '';
            foreach($workingFields as $name=>$value){
                //SIZE MANIPULATION
                $size = '';
                $labelSize = '';
                if(isset($value['size'])){
                    $size = 'maxlength='.$value['size'];
                    $labelSize = '&nbsp;&nbsp;&nbsp;<small>'.strlen($data[$name]).'/'.$value['size'].'</small>';
                }
                $returnFields .= "<div class='inputGroup'><label>".ucfirst($name).$labelSize."</label>";
                if($value['type'] == 'simple'){
                    
                    if($value['input'] == 'textarea'){
                        $add = '';
                        if(isset($value['additional'])){
                            $add = join(' ', $value['additional']);
                        }
                        $returnFields .= '<textarea class="'.$add.'" id="textarea'.ucfirst($name).'">'.$data[$name].'</textarea>';

                    }else{

                        $returnFields .= '<input type="'.$value['input'].'" placeholder="..." value="'.$data[$name].'" '.$size.'>';
                    }
                }else if($value['type'] == 'array'){
                    if($value['mode'] == 'dynamic'){
                        $returnFields .= '<input type="text" class="tag" size="1" placeholder="...">';
                    }
                }
                $returnFields .= "</div>";
            }
            return $returnFields;
        }
    }
?>