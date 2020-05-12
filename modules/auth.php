<?php
    class Auth{
        function __construct($db) {
            //SETUP
            $this->db = $db;  //mandatory database library
            $this->usersTable = 'users';    //table with users
            $this->authDuration = 300*60;    //login session duration
            $this->defaultLoginPage = 'index.php';  //defaulat login page
            $this->username = 'loginUsrnm'; 
        }

        function login($username, $password){
            $user = $this->db->query("select id,password from ".$this->usersTable." where username = ?", $username)->fetchArray();
            $hash = $user['password'];
            if(password_verify($password, $hash)){
                $_SESSION[$this->username] = $username;
                $sessionId = session_id();
                $ip = $_SERVER["REMOTE_ADDR"];
                if($this->db->query("update ".$this->usersTable." set session=?, ip=?, lastVerified=now()", $sessionId, $ip)){
                    return true;
                }
            }else{
                return false;
            }
        }

        function loginVerify(){
            if(isset($_SESSION[$this->username])){
                $username = $_SESSION[$this->username];
                if($username){
                    $q = $this->db->query("select * from ".$this->usersTable." where username=?", $username)->fetchArray();
                    if($q['session'] != session_id() || $q['ip'] != $_SERVER['REMOTE_ADDR']){
                        $scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
                        if(array_pop($scriptName) != $this->defaultLoginPage){
                            header("Location: {$this->defaultLoginPage}?msg=error");
                        }
                    }else{
                        $elapsed = time() - strtotime($q['lastVerified']);
                        if($elapsed < $this->authDuration){
                            global $app;
                            $scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
                            if(array_pop($scriptName) == $this->defaultLoginPage){
                                header("Location: home.php");
                            }
                            
                        }else{
                            $scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
                            if(array_pop($scriptName) != $this->defaultLoginPage){
                                header("Location: {$this->defaultLoginPage}?msg=expired");
                            }
                        }
        
                    }
                }else{
                    $scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
                    if(array_pop($scriptName) != $this->defaultLoginPage){
                        header("Location: {$this->defaultLoginPage}");
                    }
                }
            }else{
                $scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
                if(array_pop($scriptName) != $this->defaultLoginPage){
                    header("Location: {$this->defaultLoginPage}?msg=expired");
                }
            }
            
            
        }
    }
?>