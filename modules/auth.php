<?php

/**
 * Custom PHP class for secure authentication of users
 * @author Patrik Krivulčík
 */

class Auth
{
    /**
     * Class constructor
     * @param db $db Cusomtom mysql database module
     */

    protected $db, $usersTable, $settingsTable;
    protected $sessionUserId, $sessionUserData, $sessionExpiration, $multipleSessions, $overrideUserSession;
    protected $encryptionKey, $encryptionMethod, $encryptionIV;

    function __construct($db)
    {
        /**
         * Custom mysql database module
         * @var db
         */
        $this->db = $db;
        /**
         * Table name in which users are stored
         * @var string
         */
        $this->usersTable = 'users';
        /**
         * Table name in which settings are stored
         * @var string
         */
        $this->settingsTable = 'settings';
        /**
         * Session name for storing user id
         * @var string
         */
        $this->sessionUserId = 'adminLoginId';
        /**
         * Session name for storing openssl encrypted data from the user table
         * @var string
         */
        $this->sessionUserData = 'adminData';
        /**
         * Number of seconds after which the user session expires
         * @var integer
         */
        $this->sessionExpiration = 2000;
        /**
         * Wheter more than 1 users can be logged in as one user at the same time
         * @var bool
         */
        $this->multipleSessions = false;
        /**
         * Ony if multipleSessions is false.
         * Whether a new login attemt will override current login sessions
         * @var string
         */
        $this->overrideUserSession = false;

        /**
         * Encryption key for encrypting sessionUserData
         * @var string
         */
        $this->encryptionKey = 'mykey123';
        /**
         * Encryption method for encrypting sessionUserData
         * @var string
         */
        $this->encryptionMethod = 'aes-128-cbc-hmac-sha256';
        /**
         * Encryption IV for encrypting sessionUserData
         * @var string
         */
        $this->encryptionIV = hex2bin('422d7aded49d32115968698bb5fb0f9a');
    }

    /**
     * Login function
     * 
     * @param string $username
     * @param string $password
     * @return true or cusom error on fail
     */
    public function login($username, $password)
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

                    if (($sinceLastVerified > $this->sessionExpiration || $this->overrideUserSession) ||  !isset($_SESSION[$this->sessionUserId])) {
                        //UPDATE SESSION INFO
                        $this->db->query('UPDATE ' . $this->usersTable . ' SET hash=?,lastVerified=now() WHERE id=?', $newHash, $users[0]['id']);
                        $_SESSION[$this->sessionUserId] = $users[0]['id'];
                        //ENCRYPT DATA
                        $userData = openssl_encrypt(json_encode($users[0]), $this->encryptionMethod, $this->encryptionKey, $options = 0, $this->encryptionIV);
                        $_SESSION[$this->sessionUserData] = $userData;
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
    /**
     * Logout function function
     * @return true
     */
    public function logout()
    {
        unset($_SESSION[$this->sessionUserId]);
        // $_SESSION[$this->sessionUserId] = '';
        return true;
    }
    /**
     * Validates current login session
     * 
     * @param bool $checkExpired whether the expiration time should be checked
     * @return bool whether login session is valid  or cusom error message
     */
    public function validateLogin($checkExpired = true)
    {
        if (!isset($_SESSION[$this->sessionUserId])) {
            return false;
        } else {
            //GET HASH FROM DB

            $myHash = md5($_COOKIE['PHPSESSID'] . $_SERVER['HTTP_USER_AGENT']);
            $user = $this->db->query('SELECT id,lastVerified FROM ' . $this->usersTable . ' WHERE hash=?', $myHash)->fetchArray();
            if (
                count($user) == 0 ||
                $user['id'] != $_SESSION[$this->sessionUserId] ||
                ($checkExpired &&
                    time() - strtotime($user['lastVerified']) > $this->sessionExpiration)
            ) {
                return false;
            } else {
                return true;
            }
        }
    }


/**
     * Get user role
     * 
     * @return string user role
     */
    public function getRole()
    {
        if ($this->validateLogin(true)) {
            $data = $_SESSION[$this->sessionUserData];
            $data = openssl_decrypt($data, $this->encryptionMethod, $this->encryptionKey, $options = 0, $this->encryptionIV);
            return json_decode($data, 1)['role'];
        } else {
            return array(
                'error' => 'no-login',
                'message' => 'No user login'
            );
        }
    }
    /**
     * Get user permissions
     * 
     * @return array user permissions
     */
    public function getPermissions()
    {
        if ($this->validateLogin(true)) {
            $data = $_SESSION[$this->sessionUserData];
            $data = openssl_decrypt($data, $this->encryptionMethod, $this->encryptionKey, $options = 0, $this->encryptionIV);
            $role = json_decode($data, 1)['role'];

            //GET ROLE PERMISSION
            $roles = $this->db->query('SELECT cont FROM ' . $this->settingsTable . ' WHERE name=?', 'auth_roles')->fetchArray();
            if (isset(json_decode($roles['cont'], 1)[$role])) {

                return json_decode($roles['cont'], 1)[$role]['permissions'];
            } else {
                return array(
                    'error' => 'no-role',
                    'message' => "User's role does not exist"
                );
            }
        } else {
            return array(
                'error' => 'no-login',
                'message' => 'No user login'
            );
        }
    }
    /**
     * Get username
     * 
     * @return string username
     */
    public function getUsername()
    {
        if ($this->validateLogin(true)) {
            $data = $_SESSION[$this->sessionUserData];
            $data = openssl_decrypt($data, $this->encryptionMethod, $this->encryptionKey, $options = 0, $this->encryptionIV);
            return json_decode($data, 1)['username'];
        } else {
            return array(
                'error' => 'no-login',
                'message' => 'No user login'
            );
        }
    }
    /**
     * Get user id
     * 
     * @return string id
     */
    public function getId()
    {
        if ($this->validateLogin(true)) {
            $data = $_SESSION[$this->sessionUserData];
            $data = openssl_decrypt($data, $this->encryptionMethod, $this->encryptionKey, $options = 0, $this->encryptionIV);
            return json_decode($data, 1)['id'];
        } else {
            return array(
                'error' => 'no-login',
                'message' => 'No user login'
            );
        }
    }
}
