<?php
    class Auth{
        function __construct($db) {
            
            $this->db = $db;
            $this->usersTable = 'users';
            $this->sessionName = 'adminLogin';
            $this->multipleSessions = false;


        }

        function login($username, $password){
            
            $users = $this->db->query('SELECT * FROM ' . $this->usersTable . ' WHERE USERNAME=?', $username)->fetchAll();
            if( count($users) == 0){
                return array(
                    'error'=> 'no-user',
                    'message' => 'User does not exist'
                );
            }else{
                if( ! password_verify($password,$users[0]['password']) ){
                    return array(
                        'error'=> 'wrong-pass',
                        'message' => 'Wrong user password'
                    );
                }else{
                    if($this->multipleSessions){
                        //TODO:Add functionality
                    }else{

                        if( $users[0]['hash'] == ''){
                            $newHash = md5($_COOKIE['PHPSESSID'].$_SERVER['HTTP_USER_AGENT']);
                            $this->db->query('UPDATE ' . $this->usersTable . ' SET hash=?,lastVerified=now() WHERE id=?', $newHash, $users[0]['id']);

                            $_SESSION[$this->sessionName] = $users[0]['id'];
                            return true;
                        }else{
                            if(md5($_COOKIE['PHPSESSID'].$_SERVER['HTTP_USER_AGENT']) == $users[0]['hash']){
                                $this->db->query('UPDATE ' . $this->usersTable . ' SET lastVerified=now() WHERE id=?', $users[0]['id']);
                                return true;
                            }else{
                                return array(
                                    'error'=> 'sesssion-allready-running',
                                    'message' => 'Another user session is allready running'
                                );
                            }
                            
                        }
                    }
                    
                }
            }

        }

        
    }
?>