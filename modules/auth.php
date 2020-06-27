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
            if(!$user){
                return false;
            }else{

                print_r($user);
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
        }

        
    }
?>