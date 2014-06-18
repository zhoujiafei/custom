<?php
class admin_mode extends InitFrm 
{
    public function __construct() 
    {
        parent::__construct();
    }

    public function __destruct() 
    {
        parent::__destruct();
    }
    
    
    public function checkUser($username, $password) 
    {
        $sql = "SELECT id, username FROM ".DB_PREFIX."admin WHERE username = '".$username."' AND password='".$password."'";
        $user = $this->db->query_first($sql);
        return $user;
    }
}
?>