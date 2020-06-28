<?php
class Auth
{
    function __construct($db)
    {

        $this->db = $db;
        $this->usersTable = 'users';
        $this->sessionName = 'adminLogin';
        $this->sessionExpiration = 200; //seconds
        $this->multipleSessions = false;
        $this->overrideUserSession = false;
    }

    function login($username, $password)
    {

        $users = $this->db->query('SELECT * FROM ' . $this->usersTable . ' WHERE USERNAME=?', $username)->fetchAll();
        if (count($users) == 0) {
            return array(
                'error' => 'no-user',
                'message' => 'User does not exist'
            );
        } else {
            if (!password_verify($password, $users[0]['password'])) {
                return array(
                    'error' => 'wrong-pass',
                    'message' => 'Wrong user password'
                );
            } else {
                if ($this->multipleSessions) {
                    //TODO:Add functionality
                } else {
                   
                    $newHash = md5($_COOKIE['PHPSESSID'] . $_SERVER['HTTP_USER_AGENT']);
                    $sinceLastVerified = time() - strtotime($users[0]['lastVerified']);

                    if ($sinceLastVerified > $this->sessionExpiration || $this->overrideUserSession) {
                        //UPDATE SESSION INFO
                        $this->db->query('UPDATE ' . $this->usersTable . ' SET hash=?,lastVerified=now() WHERE id=?', $newHash, $users[0]['id']);
                        $_SESSION[$this->sessionName] = $users[0]['id'];
                        return true;

                    } else {
                        //OLDER SESSION STILL LASTS
                        return array(
                            'error' => 'session-running',
                            'message' => 'A login session is allready running'
                        );
                        
                    }
                }
            }
        }
    }
}
