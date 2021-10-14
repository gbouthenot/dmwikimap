<?php

require_once("Gb/Db.php");
require_once("Gb/Session.php");
require_once("ini/dbparams.php");

Class Auth
{

    /**
     * @var Gb_Db
     */
    protected $_db;
    
    

    /**
     * @return Gb_Db
     */
    protected function _getDb()
    {
        if ($this->_db===null) {
            $this->_db=new Gb_Db( iniGetDbparams() );
        }
        return $this->_db;
    }
    
    
    
    /**
     * @var Auth
     */
    private static $_instance;
    
    /**
     * @return Auth
     */
    public static function singleton()
    {
        if (!isset(self::$_instance)) {
            $c=__CLASS__;
            self::$_instance=new $c;
        }
        return self::$_instance;
    }
    
    private function __construct()
    {
    }
    
    /**
     * renvoie le login de l'utilisateur ou false si non loggué
     *
     * @return string|false
     */
    public function getLogin()
    {
        $auth=Gb_Session::get("auth");
        if ($auth===false) {
            return false;
        } elseif (is_array($auth) && isset($auth["login"])) {
            return $auth["login"];
        } else {
            return false;
        }
    }
    
    /**
     * renvoie l'id de l'utilisateur ou false si non loggué
     *
     * @return string|false
     */
    public function getId()
    {
        $auth=Gb_Session::get("auth");
        if ($auth===false) {
            return false;
        } elseif (is_array($auth) && isset($auth["id"])) {
            return $auth["id"];
        } else {
            return false;
        }
    }
    
    
    /**
     * @param string $username
     * @param string $password
     * @return false|string
     */
    public function login($username, $password)
    {
        Gb_Log::logInfo("log attempt for $username", null, false);
        $db=$this->_getDb();
        
        // see wikimedia includes/User.php
        // only support for 'A' and 'B' types
        // pbkdf2 requires https://github.com/curzon01/mysql_PBKDF2 (which is SLOW !)
        $sql="
SELECT user_id, user_name
FROM user
WHERE
    LOWER(user_name)=LOWER(?)
    AND
    user_password=
    IF(substr(user_password,1,3)=':B:',
        CONCAT(':B:', SUBSTR(user_password, 4, 8), ':', MD5(CONCAT(SUBSTR(user_password, 4, 8), '-', MD5(?)))),
        IF(substr(user_password,1,3)=':A:',
            CONCAT(':A:', MD5(?)),
            IF(LENGTH(user_password)=32,
                MD5(CONCAT(user_id, '-', MD5(?))),
                MD5(?)
)))";
        $login=$db->retrieve_one($sql, array($username, $password, $password, $password, $password));
        
        if ($login===false) {
            return false;
        } else {
            Gb_Session::set("auth", array("id"=>$login["user_id"], "login"=>$login["user_name"]));
            return $login;
        }
    }

    public function logout()
    {
        Gb_Session::_unset("auth");
    }
    
}





