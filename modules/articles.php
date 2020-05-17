<?php

use function PHPSTORM_META\type;

class Articles{
        function __construct($db) {
            //DB
            $this->db = $db;  //mandatory database library
            //SETUP
            $this->tableName = 'articles'; //table name in database
            $this->editPrefix = 'editTest';
            $this->articlesPage = 'articles.php';
            $this->articlesImgFolder = 'articles';
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
                    "type"=>'nochange'
                ),
                'tags'=>array(
                    "type"=>'array',
                ),
                'category'=>array(
                    "type"=>'select',
                    "data"=>'article_categories'
                ),
                'file'=>array(
                    "type"=>'simple',
                    "input"=>'file'
                )
            );
            $this->tableActions = '
                <a href="?action=edit&id={{{id}}}" class="btn btn-blue">Edit</a>
                <a href="?action=delete&id={{{id}}}" class="btn btn-red"">Action</a>
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
                        $returnFields .= '<textarea class="'.$add.'" id="textarea'.ucfirst($name).'" name="'.$this->editPrefix.$name.'">'.$data[$name].'</textarea>';

                    }else if($value['input'] == 'file'){
                        $returnFields .= '<button class="openFiles" data-returnto="'.$this->editPrefix.$name.'">Choose file</button>';
                        $returnFields .= '<input type="text" placeholder="..." name="'.$this->editPrefix.$name.'" value="'.$data[$name].'">';
                    }else{

                        $returnFields .= '<input type="'.$value['input'].'" placeholder="..." value="'.$data[$name].'" '.$size.' name="'.$this->editPrefix.$name.'">';
                    }
                }else if($value['type'] == 'array'){
                  
                    $tags = json_decode($data[$name]);
                    foreach ($tags as $key => $tag) {
                        $returnFields .= '<input type="text" class="tag" size="'.strlen($tag).'" placeholder="..." value="'.$tag.'" name="'.$this->editPrefix.$name.'[]">';
                    }
                    $returnFields .= '<input type="text" class="tag" size="1" placeholder="..." name="'.$this->editPrefix.$name.'[]">';
                
                }else if($value['type'] == 'select'){
                    global $admin;
                    $returnFields .= '<select name="'.$this->editPrefix.$name.'">';
                    $options = json_decode($admin->settings->get($value['data']));
                    foreach ($options as $key => $option) {
                        $returnFields .= '<option value="'.$option.'"'.(($option == $data[$name])?'selected':'').'>'.$option.'</option>';
                    }
                    $returnFields .= '</select>';
                   
                }
                $returnFields .= "</div>";
            }
            $returnFields .= "<input type='submit' value='Save'>";
            return $returnFields;
        }

        function update($id, $fields, $data){
            $id = intval($id);
            $updateFields = array();
            $updateData = array();
            if($fields == ''){
                //UPDATE ALL
                foreach($data as $key=>$value){
                    if(count(explode($this->editPrefix, $key)) > 1){
                        $s = explode($this->editPrefix, $key)[1];
                        $updateFields[] = $s;
                        if(is_array($value)){
                            $updateData[] = json_encode(array_filter($value));
                        }else{
                            $updateData[] = $value;
                        }

                    }
                }
            }else{
                $updateFields = explode(',', $fields);
                foreach ($updateFields as $key => $value) {
                    echo is_array($data[$this->editPrefix.$value]);
                    $updateData[] = $data[$this->editPrefix.$value];
                }
            }
            $query = "update {$this->tableName} set ";
            foreach($updateFields as $key=>$val){
                $query .= $val.'=?,';
            }
            $query = substr($query, 0, -1);
            $query .= " where id={$id}";
            if($this->db->query($query, $updateData)){
                return true;
            }
        }

        function getAdd(){
            $workingFields = array();
            //GET FIELDS WHICH WILL BE EDITED
            
            foreach($this->tableSetup as $key=>$fieldVal){
                if($fieldVal['type'] != 'nochange'){
                    $workingFields[$key] = $fieldVal;
                }
            }
           
            //RETURN STRING
            $returnFields = '';
            foreach($workingFields as $name=>$value){
                //SIZE MANIPULATION
                $size = '';
                $labelSize = '';
                if(isset($value['size'])){
                    $size = 'maxlength='.$value['size'];
                    $labelSize = '&nbsp;&nbsp;&nbsp;<small>0/'.$value['size'].'</small>';
                }
                $returnFields .= "<div class='inputGroup'><label>".ucfirst($name).$labelSize."</label>";
                if($value['type'] == 'simple'){
                    
                    if($value['input'] == 'textarea'){
                        $add = '';
                        if(isset($value['additional'])){
                            $add = join(' ', $value['additional']);
                        }
                        $returnFields .= '<textarea class="'.$add.'" id="textarea'.ucfirst($name).'" name="'.$this->editPrefix.$name.'"></textarea>';

                    }else if($value['input'] == 'file'){
                        $returnFields .= '<button class="openFiles" data-returnto="'.$this->editPrefix.$name.'">Choose file</button>';
                        $returnFields .= '<input type="text" placeholder="..." name="'.$this->editPrefix.$name.'">';
                    }else{

                        $returnFields .= '<input type="'.$value['input'].'" placeholder="..." '.$size.' name="'.$this->editPrefix.$name.'">';
                    }
                }else if($value['type'] == 'array'){
                  
                    
                    $returnFields .= '<input type="text" class="tag" size="1" placeholder="..." name="'.$this->editPrefix.$name.'[]">';
                
                }else if($value['type'] == 'select'){
                    global $admin;
                    $returnFields .= '<select name="'.$this->editPrefix.$name.'">';
                    $options = json_decode($admin->settings->get($value['data']));
                    foreach ($options as $key => $option) {
                        $returnFields .= '<option value="'.$option.'">'.$option.'</option>';
                    }
                    $returnFields .= '</select>';
                   
                }
                $returnFields .= "</div>";
            }
            $returnFields .= "<input type='submit' value='Save'>";
            return $returnFields;
        }

        function add($data){
            $updateFields = array();
            $updateData = array();
          
            foreach($data as $key=>$value){
                if(count(explode($this->editPrefix, $key)) > 1){
                    $s = explode($this->editPrefix, $key)[1];
                    $updateFields[] = $s;
                    if(is_array($value)){
                        if(count(array_filter($value)) == 0){
                            $updateData[] = json_encode(array());
                        }else{
                            $updateData[] = json_encode(array_filter($value));
                        }
                    }else{
                        $updateData[] = $value;
                    }

                }
            }
            
            $query = "insert into {$this->tableName}(";
            $query .= implode(',', $updateFields).') values(';
            foreach($updateFields as $key=>$val){
               $query .= '?,';
            }
            $query = substr($query, 0, -1);
            $query .= ')';
            if($this->db->query($query, $updateData)){
                return true;
            }else{
                return false;
            }
        }

        function delete($id){
            $q = "delete from {$this->tableName} where id=?";
            if( $this->db->query($q,$id)){
                return true;
            }else{
                return false;
            }
        }
    }
?>