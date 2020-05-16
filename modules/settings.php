<?php
    class Settings{
        function __construct($db) {
            //DB
            $this->db = $db;
            //SETUp
            $this->table = 'settings';
        }
        public function get($name){
            return $this->db->query('select * from '.$this->table.' where name=?', $name)->fetchArray()['cont'];
        }
    }
?>