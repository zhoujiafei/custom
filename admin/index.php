<?php
define('SCRIPT_NAME', 'index');
require_once('global.php');
class index extends uiBaseFrm
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function __destruct()
    {
        parent::__destruct();
    }

    public function show()
    {
       //跳到首页
       $this->tpl->outTemplate('index');
    }
    
    public function showhome()
    {
    	$this->tpl->outTemplate('home');
    }
}

include (ROOT_PATH . 'lib/exec.php');

?>