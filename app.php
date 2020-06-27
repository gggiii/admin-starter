<?php
    class Admin{
        public function __construct() {
            

            //DATABASE
            include('db.php');
            $this->db = new db('localhost','root','','admin','3306');

            //PROCESS ALL MODULES
            $modules = scandir('modules');
            array_shift($modules);
            array_shift($modules);
            foreach($modules as $key => $value){
                include('modules/'.$value);
                $module = explode('.',$value)[0];
                $modules[$key] = $module;
                $moduleName = ucfirst($module);
                $this->{$module} =  new $moduleName($this->db);
            }

            //SESSION
            session_start();
           
        }
        
    }
    
    
?>