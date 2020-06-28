<?php
    class Items{

        protected $db, $itemsTable;
        protected $fields;
        protected $newFieldPrefix;

        public function __construct($db){
            $this->db = $db;
            $this->itemsTable = 'items';
            $this->fields = array(
                'name'=>array(
                    'type'=>'simple-text'
                ),
                'text'=>array(
                    'type'=>'textarea'
                ),
                'number'=>array(
                    'type'=>'simple-number'
                ),
            );
            $this->newFieldPrefix = 'new-';
        }
        public function getAll($id = false){
            if(!$id){
                return $this->db->query('SELECT * FROM ' . $this->itemsTable)->fetchAll();
            }elseif( !is_int($id)){
                return array(
                    'error' => 'wrong-parameter',
                    'message' => 'Paramter should be an integer or left empty'
                );
            }else{
                return $this->db->query('SELECT * FROM ' . $this->itemsTable . ' WHERE id=?', $id)->fetchArray();
            }
        }
        public function add($post){
            if(count($post) != count($this->fields)){
                return array(
                    'error' => 'wrong-parameters-count',
                    'message' => 'Number of fields in parameter does not match required number of pparamters'
                );
            }else{
                $keys = array_keys($post);
                $query = 'INSERT INTO '.$this->itemsTable.'(';
                $values = ' VALUES(';
                $valuesActual = array();
                for ($i=0; $i < count($keys); $i++) { 
                    $key = $keys[$i];
                    if(strpos($key, $this->newFieldPrefix) === false){
                        return array(
                            'error' => 'wrong-fields-format',
                            'message' => 'Some fields do not contain the correct newFieldPrefix'
                        );
                    }else{
                        $query .= explode($this->newFieldPrefix, $key)[1].',';
                        $values .= '?,';
                        $valuesActual[] = $post[$key];
                    }
                    
                }
                $query = rtrim($query, ' ,').')';
                $values = rtrim($values, ' ,').')';
                $query = $query.$values;
                
                $this->db->query($query, $valuesActual);
                return true;
            }
        }
        
    }
?>