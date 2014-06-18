<?php 
class Templates
{
	private $mTemplateFrame;							//模板框架
	private $mSoftVar;							//软件产品标识
	private $mBodyCode;							//<body $mBodyCode>中代码
	private $mTemplate;							// 当前调用模板
	private $mTemplatesTitle = 'default';		//模板标题
	private $mTemplateGroup = 'default';   //模板风格分组
	private $mHeaderCode = array();							//<head>$mHeaderCode</head>中代码
	private $mFooterCode = array();							// $mFootCode</body>中代码
	private $mTemplates = array();			//模板单元
	private $mTemplateDatas = array();		//模板数据
	private $mRequestData = array();		//提交数据
	private $mTemplateApi ='';		
	private $mScriptDir ='';		
	private $mTemplateVersion ='';		
	private $mProxyDomain ='';		
	function __construct($proxydomain = array())
	{
		$this->mSoftVar = SOFTVAR;
		$this->mTemplatesTitle = SOFTVAR;
		$this->mProxyDomain = $proxydomain;
		$this->setTemplateApi();
	}

	function __destruct()
	{
		$this->clearTemplateCell();
		$this->clearVar();
	}
	
	/**
	* 设置系统名
	*
	*/
	public function setSoftVar($var = '')
	{
		$this->mSoftVar = $var;
	}
	/**
	* 设置模板api
	*
	*/
	public function setTemplateVersion($version = '')
	{
		$this->mTemplateVersion = $version . '/';
	}

	/**
	* 设置模板ScriptDir
	*
	*/
	public function setScriptDir($dir = '')
	{
		$this->mScriptDir = $dir;
	}
	public function setTemplateApi($api = TEMPLATE_API)
	{
		$this->mTemplateApi = $api;
	}

	/**
	* 增加模板头部代码
	*
	*/
	public function addHeaderCode($code = '')
	{
		$this->mHeaderCode[] = $code;
	}

	/**
	* 设置<Body>代码
	*
	*/
	public function setBodyCode($code = '')
	{
		$this->mBodyCode = $code;
	}

	/**
	* 增加$mFootCode</body>中代码
	*
	*/
	public function addFooterCode($code = '')
	{
		$this->mFooterCode[] = $code;
	}

	/**
	* 设置模板组
	*
	*/
	public function setTemplateGroup($group_name = 'default')
	{
		$this->mTemplateGroup = $group_name;
	}

	/**
	* 设置模板标题
	*
	*/
	public function setTemplateTitle($title)
	{
		$this->mTemplatesTitle = $title;
	}

	/**
	* 设置模板框架
	*
	*/
	public function setTemplate($template_name = 'default')
	{
		$this->mTemplateFrame = $template_name;
	}
	
	/**
	* 清除模板设置
	*
	*/
	public function clearTemplateCell()
	{
		$this->mTemplates = array();
	}

	/**
	* 添加模板引用
	*
	*/
	public function addTemplateCell($template_name)
	{
		$this->mTemplates[] = $template_name;
	}
	
	/**
	* 添加模板变量
	*
	*/
	public function addVar($var, $value)
	{
		$this->mTemplateDatas[$var] = $value;
	}

	/**
	* 清除模板变量数据
	*
	*/
	public function clearVar()
	{
		$this->mTemplateDatas = array();
		$this->mHeaderCode = array();
		$this->mFooterCode = array();
	}

	/**
	* 初始化提交数据
	*
	*/
	public function initRequestData()
	{
		$this->mRequestData = array();
	}

	/**
	* 输出模板
	*
	*/
	public function outTemplate($hg_template_name, $callback = '')
	{
		if (!$this->mSoftVar)
		{
			echo 'Please specify SOFTVAR in configuare file.';
			return;
		}
		$fetch_template = false;
		if (!CACHE_TEMPLATE)
		{
			$fetch_template = true;
		}
		else
		{
			if (!file_exists(TEMPLATE_DIR . $this->mSoftVar . '/' . $hg_template_name . '.php'))
			{
				$fetch_template = true;
			}
		}
		if ($fetch_template)
		{
			//fetch template and cahed
			$this->localCss($hg_template_name);
			$this->fetchTemplates($hg_template_name);
		}
		foreach ($this->mTemplateDatas AS $k => $v)
		{
			$$k = $v;
		}
		$this->mHeaderCode = implode("\n", $this->mHeaderCode);
		$this->mFooterCode = implode("\n", $this->mFooterCode);
		$RESOURCE_URL = RESOURCE_URL;
		$SCRIPT_URL = SCRIPT_URL;
		$__script_dir = $this->mScriptDir;
		if (!$_REQUEST['ajax'])
		{
			ob_end_clean();
			ob_start();
			@include (TEMPLATE_DIR . $this->mSoftVar . '/' . $hg_template_name . '.php');
			$html = ob_get_contents();
			ob_end_clean();
			echo $this->add_port_out($html);
			$this->mTemplate = $hg_template_name;
			exit;
		}
		else
		{
			ob_end_clean();
			ob_start();
			@include (TEMPLATE_DIR .  $this->mSoftVar . '/' . $hg_template_name . '.php');
			$html = ob_get_contents();
			ob_end_clean();
			$html = str_replace(array("\r", "\n"), '', $html);
			$html = $this->add_port_out($html);
			$this->mTemplate = $hg_template_name;
			if ($callback)
			{
				$html = addslashes($html);
				$callback = explode(',', $callback);
				$cfunc = $callback[0];
				unset($callback[0]);
				if ($callback)
				{
					$jsstr = '';
					foreach ($callback AS $v)
					{
						$jsstr .= ",'{$v}'";
					}
				}
				$callback = $cfunc . "('$html'$jsstr)";
			}
			$data = array(
				'msg' => '',
				'callback' => $callback,
			);
			//PRINT_R($data);
			echo json_encode($data);
			exit;
		}
	}

	public function showTemplateVars()
	{
		$template_name = $this->mTemplate;
		$this->initRequestData();
		$this->addRequestData('softvar', $this->mSoftVar);
		$this->addRequestData('group', $this->mTemplateGroup);
		$this->addRequestData('template', $template_name);
		$this->addRequestData('a', 'getvars');
		$ret = $this->post($this->mTemplateApi, $this->mRequestData);
		$ret = json_decode($ret, true);
		return $ret;
	}
	
	/**
	* 本地化css
	*
	*/
	private function localCss($template_name)
	{
		$this->initRequestData();
		$this->addRequestData('softvar', $this->mSoftVar);
		$this->addRequestData('group', $this->mTemplateGroup);
		$this->addRequestData('template', $template_name);
		$this->addRequestData('a', 'getcss');
	
		$ret = $this->post($this->mTemplateApi, $this->mRequestData);
		$ret = json_decode($ret, true);

		if (!hg_mkdir(CSS_FILE_DIR . '/' . $this->mSoftVar . '/'))
		{
			exit(CSS_FILE_DIR .  '/目录创建失败，请检查目录权限.');
		}
		$absolute_url = false;
		if (substr(RESOURCE_URL, 0, 7) == 'http://')
		{
		    $absolute_url = true;
			$RESOURCE_URL = RESOURCE_URL;
		}
		else
		{
			$RESOURCE_URL = '../../../../' . str_replace(ROOT_DIR,'',RESOURCE_URL);
		}
		if (is_array($ret))
		{
			foreach ($ret AS $file => $content)
			{
				if ($file)
				{
				    $tmp_url = $RESOURCE_URL;
					if(strpos($file, '/'))
					{
						$filename = strrchr($file, '/');
						$dir = str_replace($filename, '', $file);
                        if(!$absolute_url && $dir){
                            ($count = count(explode('/', trim($dir, '/')))) && $tmp_url = str_repeat('../', $count).$tmp_url;
                        }
						if (!hg_mkdir(CSS_FILE_DIR . $this->mSoftVar . '/' . $dir . '/'))
						{
							exit(CSS_FILE_DIR . $this->mSoftVar . '/' . $dir . '/目录创建失败，请检查目录权限.');
						}
					}
					$varpreg = "/{\\$[a-zA-Z0-9_\[\]\-\'\>]+}/";
					$content = preg_replace($varpreg,  $tmp_url, $content);
					$content = $this->add_port_out($content);
					hg_file_write(CSS_FILE_DIR . $this->mSoftVar . '/' . $file, $content);
				}
			}
		}
	}

	public function recacheTemplate($template_name)
	{
		$this->localCss($template_name);
		$this->fetchTemplates($template_name);
	}
	
	/**
	* 获取模板信息
	*
	*/
	private function fetchTemplates($template_name)
	{
		$this->initRequestData();
		$this->addRequestData('softvar', $this->mSoftVar);
		$this->addRequestData('group', $this->mTemplateGroup);
		$this->addRequestData('template', $template_name);
	
		$ret = $this->post($this->mTemplateApi, $this->mRequestData);
		if (hg_mkdir(TEMPLATE_DIR .  $this->mSoftVar . '/'))
		{
			hg_file_write(TEMPLATE_DIR .  $this->mSoftVar . '/' . $template_name . '.php', $ret);
		}
		else
		{
			exit(TEMPLATE_DIR .  $this->mSoftVar . '/目录创建失败，请检查目录权限.');
		}
	}

	private function addRequestData($name, $value)
	{
		$this->mRequestData[$name] = urlencode($value);
	}

	/**
	* 获取模板信息
	*
	*/
    private function post($url, $post_data)
    {
		$url .= $this->mTemplateVersion;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8"));
		//print_r( $post_data);
		$post_data['version'] = $this->mTemplateVersion;
		$post_data['single'] = 1;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch,CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);
		$head_info = curl_getinfo($ch);
        curl_close($ch);
		if($head_info['http_code']!= 200)
		{
        	exit('<!-- ' . $url . ' -->模板文件不存在或网络错误,连不上ui界面服务器');
		}
        if ($ret == null)
        {
        	exit('<!-- ' . $url . ' -->模板文件不存在或网络错误,连不上ui界面服务器');
        }

        return $ret;
    }

	private function add_port_out($content)
	{
		if (!$this->mProxyDomain)
		{
			return $content;
		}
		$content = preg_replace(array_keys($this->mProxyDomain), $this->mProxyDomain, $content);
		return $content;
	}

    public function buildres($mid){
        if(!$mid){
            $softvar = 'lib';
            $group = '';
        }else{
            $application = $this->_getApplication($mid);
            $softvar = $application['softvar'];
            $group = 'default';
        }

        $postData = array('softvar' => $softvar, 'group' => $group);
        $result = $this->post($this->mTemplateApi . 'buildres.php', $postData);
        if(!result){
            exit('出错拉！！！');
        }
        $zipDir = ROOT_PATH . 'cache/buildres/zip_' . uniqid() . '/';
        $unzipDir = ROOT_PATH . 'res/' . $softvar . '/';
        exec(' rm -fR ' . $unzipDir);
        if(hg_mkdir($zipDir) && is_writeable($zipDir)){
            $zipFile = $zipDir.'buildres.zip';
            file_put_contents($zipFile, $result);

            $doComplete = false;
            if(hg_mkdir($unzipDir) && is_writeable($unzipDir)){
                exec(' unzip ' . $zipFile . ' -d ' . realpath($unzipDir));
                $cssArr = array($group);
                if($softvar != 'lib' && $group != 'default'){
                    $cssArr[] = 'default';
                }
                foreach($cssArr as $k => $v){
                    $tmp = $v ? $unzipDir . $v . '/css/' : $unzipDir . 'css/';
                    if(is_dir($tmp)){
                        $this->_changeCss($tmp);
                    }
                }
                $doComplete = true;
            }
            exec(' rm -fr '.$zipDir);
            if($doComplete){
                exit('成功！');
            }
        }
        exit('出错！');
    }

    private function _getApplication($mid){
        $db = hg_checkDB();
        $sql = "SELECT * FROM " . DB_PREFIX . "modules WHERE id=" . $mid;
        $module = $db->query_first($sql);
        if(!$module){
            exit('运行模块不存在');
        }
        $application = hg_check_application(intval($module['application_id']));
        if (!$application){
            exit('应用不存在或已被删除');
        }
        return $application;
    }

    private function _changeCss($dir, $root = false){
        if(!$root){
            $root = $dir;
        }
        if(is_dir($dir) && ($dh = opendir($dir))){
            while(false !== ($file = readdir($dh))){
                if($file != '.' && $file != '..'){
                    if(is_dir($dir . $file . '/')){
                        $this->_changeCss($dir . $file . '/', $root);
                    }else{
                        if(preg_match('/.css$/', $file)){
                            $content = file_get_contents($dir . $file);
                            $content = preg_replace("/{\\$[a-zA-Z0-9_\[\]\-\'\>]+}/", RESOURCE_URL, $content);
                            hg_file_write($dir . $file, $content);
                        }
                    }
                }
            }
            closedir($dh);
        }
    }
}
?>