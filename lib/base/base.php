<?php 
//接口的抽象类
abstract class InitFrm
{
	protected $input;
	protected $settings;
	protected $db;
	protected $mData = array();
	public function __construct()
	{
		global $_INPUT, $gGlobalConfig,$gDB;
		$this->input = &$_INPUT;
		$this->settings = &$gGlobalConfig;
		$this->db = &$gDB;
	}
	
	public function __destruct()
	{
		
	}
	
 	//添加数据
    protected function addItem($data) 
    {
        $this -> mData[] = $data;
    }

    //输出数据
    protected function output() 
    {
        $content_type = 'Content-Type:text/plain';
        $output = json_encode($this -> mData);
        echo $output;
    }
	
    //输出错误数据
	protected function errorOutput($errno = 'Unknow')
	{
		include(ROOT_PATH . 'conf/error.conf.php');
		$content_type = 'Content-Type: text/plain';
		$output = array(
				'ErrorCode' => $errno,
				'ErrorText' => $errorConf[$errno],
		);
		$output = json_encode($output);
		header($content_type);
		echo $output;
		exit;
	}
	
	protected function redirect($url)
	{
		$jsStr  = "<SCRIPT LANGUAGE='JavaScript'>";
		$jsStr .= "window.location.href='" .$url. "'";
		$jsStr .= "</SCRIPT>"; 
		echo $jsStr;
	}
}

//后台界面抽象类
abstract class uiBaseFrm extends InitFrm
{
	protected $tpl;
	protected $user;
	public function __construct()
	{
		global $gTpl,$gUser;
		parent::__construct();
		$this->tpl = &$gTpl;
		$this->user = &$gUser;
		
		//载入菜单配置
        $menus = include_once(ROOT_PATH . 'conf/menu.conf.php');
        $this->tpl->addVar('_menus', $menus);
        $this->tpl->addVar('_user',$this->user);
        $this->tpl->addVar('_configs',$this->settings);
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
	
	//报错
	public function reportError($errno = 'Unknow', $url = '', $delay = 1000) 
    {
        include (ROOT_PATH . 'conf/error.conf.php');
        $errtext = $errorConf[$errno];
        if (!$this->input['ajax']) 
        {
            $this->tpl->setTemplateTitle('出错了...');
            $this->tpl->addVar('message', $errtext);
            $this->tpl->addVar('url', $url);
            $this->tpl->addVar('delay', $delay);
            $this->tpl->addVar('success', 0);
            $this->tpl->outTemplate('redirect');            
        }
        else 
        {
            $data = array(
                'ErrorCode' => $errno, 
                'ErrorText' => $errorConf[$errno],
                'refterto'  => $url,
            );
            echo json_encode($data);
        }
        exit();
    }
    
    //重定向
	public function redirect($message = '', $url = '', $delay = 1000) 
    {
        if (!$url) 
        {
            $url = $_SERVER['HTTP_REFERER'];
        }
        if (!$this->input['ajax']) 
        {
            $this->tpl->setTemplateTitle('正在转向...');
            $this->tpl->addVar('message', $message);
            $this->tpl->addVar('url', $url);
            $this->tpl->addVar('delay', $delay);
            $this->tpl->addVar('success', 1);
            $this->tpl->outTemplate('redirect');            
        }
        else 
        {
            $data = array(
                'message' => $message,
                'referto' => $url,
            );
            echo json_encode($data);
        }
        exit();
    }
}

