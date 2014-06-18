<?php
define('WITHOUT_LOGIN',TRUE);
define('SCRIPT_NAME', 'login');
require_once('global.php');
require_once(ROOT_PATH . 'mode/admin_mode.php');
class login extends uiBaseFrm
{
    private $mode;
    public function __construct()
    {
        parent::__construct();
        $this->mode = new admin_mode();
    }
    
    public function __destruct()
    {
        parent::__destruct();
    }

    //显示登陆界面
    public function show()
    {
    	//判断有没有登陆，如果登陆话就跳到首页
    	if($this->user && $this->user['id'])
    	{
    		header('Location:index.php');
    		exit;
    	}
		$this->tpl->outTemplate('login');
    }

    //执行登陆
    public function dologin()
    {
    	if($_POST['submit'])
    	{
    		$username = $this->input['username'];
    		$password = $this->input['password'];
	    	if(!$this->input['username'])
	        {
	        	$this->reportError(NO_USERNAME);
	        }
	        
    		if(!$this->input['password'])
	        {
	        	$this->reportError(NOPASSWORD);
	        }
	        
	        //验证用户存不存在
	        $user = $this->mode->checkUser($username,$password);
	        if (!$user['id'])
            {
                $this->reportError(NOACCESS);
            }
            else 
            {
            	session_start();
                $this->user = $_SESSION['custom_userinfo'] = $user;
                header('Location:index.php');
            }
    	}
    	else
    	{
    		header('Location:login.php');
    	}
    }
    
    //退出登陆
    public function logout() 
    {
    	session_start();
     	$this->user = $_SESSION['custom_userinfo'] = array();
     	header('Location:login.php');
    }
}

include (ROOT_PATH . 'lib/exec.php');

?>