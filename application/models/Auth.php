<?php

require_once("Gb/Db.php");
require_once("Gb/Session.php");

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
            $this->_db=new Gb_Db(
                array(
                    "type"=>"Mysql",
                    "host"=>"localhost",
                    "user"=>"DBUSER",
                    "pass"=>"DBPASS",
                    "name"=>"DBNAME"
                )
            );
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
        }
    }
    
    /**
     * @param string $username
     * @param string $password
     */
    public function login($username, $password)
    {
        $db=$this->_getDb();
        
        $sql="select user_name from user WHERE LOWER(user_name)=LOWER(?) AND user_password=MD5(CONCAT(user_id,'-',MD5(?)))";
        $login=$db->retrieve_one($sql, array($username, $password), "user_name");
        
        if ($login===false) {
            return false;
        } else {
            Gb_Session::set("auth", array("login"=>$login));
            return $login;
        }
    }

    public function logout()
    {
        Gb_Session::_unset("auth");
    }
    
}





?>