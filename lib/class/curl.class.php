<?php
/***************************************************************************
* LivSNS 0.1
* (C)2004-2010 HOGE Software.
*
* $Id: curl.class.php 26553 2013-07-29 02:52:20Z tong $
***************************************************************************/

class curl
{
	var $mRequestType = 'http';
	var $mReturnType = 'json';
	var $mSubmitType = 'get';
	var $mUrlHost = 'localhost';
	var $mApiDir = 'livsns/api/';
	var $mAuthKey = 'aldkj12321aasd';
	var $mFile = '';
	var $mCharset = 'UTF-8';
	var $mCookies = array();
	var $mRequestData = array();
	var $globalConfig = array();
	var $input = array();
	var $isSetTimeOut = 30;//设置curl超时时间
	function __construct($host = '', $apidir = '', $authkey = 'aldkj12321aasd', $stype = 'get', $request_type = 'http')
	{
		global $gGlobalConfig, $_INPUT;
		$this->globalConfig = $gGlobalConfig;
		$this->input = $_INPUT;
		$this->setUrlHost($host, $apidir);
		$this->setClient($authkey);
		$this->setRequestType($request_type);
		$this->setSubmitType($stype);
		//$this->setUser();
	}

	function __destruct()
	{
	}

	public function setCharset($charset)
	{
		if ($charset)
		{
			$this->mCharset = $charset;
		}
	}

	public function setUser()
	{
		global $gUser;

		if($gUser)
		{
			$pass = $this->input['pass'] ? $this->input['pass'] :hg_get_cookie('pass');
			$this->addCookie('user', $gUser['username']);
			$this->addCookie('pass', $pass);
		}
		else
		{
			$user = $this->input['user'] ? $this->input['user'] : hg_get_cookie('user');
			$pass = $this->input['pass'] ? $this->input['pass'] : hg_get_cookie('pass');
			$this->addCookie('user', $user);
			$this->addCookie('pass', $pass);
		}
	}

	public function initPostData()
	{
		$this->mRequestData = array();
	}

	public function setReturnFormat($format)
	{
		if (!in_array($format, array('json', 'xml', 'str')))
		{
			$format = 'json';
		}
		$this->mReturnType = $format;
	}

	public function setUrlHost($host, $apidir)
	{
		if (!$host)
		{
			global $gApiConfig;
			$host = $gApiConfig['host'];
			$apidir = $gApiConfig['apidir'];
		}
		$this->mUrlHost = $host;
		$this->mApiDir = $apidir;
	}

	public function setClient($authkey)
	{
		$this->mAuthKey = $authkey;
	}

	public function setAuthKey($authkey)
	{
		$this->mAuthKey = $authkey;
	}

	public function setRequestType($type)
	{
		$this->mRequestType = $type;
	}

	public function setSubmitType($type)
	{
		$this->mSubmitType = $type;
	}

	public function addCookie($name, $value)
	{
		$this->mCookies[] = $this->globalConfig['cookie_prefix'] . $name . '=' . $value;
	}

	public function addFile($file)
	{
		if(isset($file))
		{
			foreach ($file as $var => $val)
			{
				if (is_array($val['tmp_name']))
				{
					foreach ($val['tmp_name'] as $k=>$fname)
					{
						if ($fname)
						{
							$this->mRequestData[$var . "[$k]"] = "@".$fname . ';type=' . $val['type'][$k] . ';filename=' . urlencode($val['name'][$k]);
						}
					}
				}
				else
				{
					if ($val['tmp_name'])
					{
							$this->mRequestData[$var] = "@".$val['tmp_name'] . ';type=' . $val['type'] . ';filename=' . urlencode($val['name']);
					}
				}
			}
		}
	}

	public function addRequestData($name, $value)
	{
		$this->mRequestData[$name] = $value;//urlencode
	}
	

	public function mPostContentType($type)
	{
		$this->mPostContentType = $type;
	}
	
	//设置curl超时(秒)
	public function setCurlTimeOut($time = 0)
	{
		$this->isSetTimeOut = $time;
	}

    public function request($file)
    {
		$para = '';
		if ('get' == $this->mSubmitType && $this->mRequestData)
		{
			foreach ($this->mRequestData AS $k => $v)
			{
				$para .= '&' . $k . '=' . ($v);
			}
		}
		if (strpos($file, '?'))
		{
			$pachar = '&';
		}
		else
		{
			$pachar = '?';
		}
		$url = $this->mRequestType . '://' . $this->mUrlHost . '/' . $this->mApiDir . $file . $pachar . 'format=' . $this->mReturnType . $para;
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        
		//curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
		//curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch,CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		if ($this->mCookies)
		{
			$cookies = implode(';', $this->mCookies);

			curl_setopt($ch, CURLOPT_COOKIE, $cookies);
		}
		if($this->mRequestType == 'https')
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		if ('post' == $this->mSubmitType)
		{
			curl_setopt($ch, CURLOPT_POST, true);
			if ($this->mPostContentType == 'string')
			{	
				$postdata = '';
				foreach ($this->mRequestData AS $k => $v)
				{
					$postdata .= '&' . $k . '=' . $v;
				}
			}
			else
			{
				$postdata = $this->mRequestData;
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		}
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if($this->isSetTimeOut)
        {
        	curl_setopt($ch, CURLOPT_TIMEOUT, $this->isSetTimeOut);
        }
        
        $ret = curl_exec($ch);
		$head_info = curl_getinfo($ch);
		$i = 0;
		while ($head_info['http_code'] != 200 && $i < 1)
		{
			$i++;
			$ret = curl_exec($ch);
			$head_info = curl_getinfo($ch);
		}
        curl_close($ch);
		if($head_info['http_code']!= 200)
		{
			if (DEBUG_MODE)
			{
				file_put_contents(ROOT_PATH . 'log/ERROR-' . date('Y-m-d',TIMENOW) . '.log', date('Y-m-d H:i:s',TIMENOW) ."\n" .  var_export($head_info,1) . "\n------------------------------------------------------------\n",FILE_APPEND);
			}
			return '';
		}
		
		if (DEBUG_MODE)
		{
			file_put_contents(ROOT_PATH . 'log/INFO-' . date('Y-m-d',TIMENOW) . '.log', date('Y-m-d H:i:s',TIMENOW) ."\n" .  var_export($head_info,1) . "\n------------------------------------------------------------\n",FILE_APPEND);
		}
		
        if ($ret == 'null')
        {
        	return '';
        }
        $func = $this->mReturnType . 'ToArray';
        $ret = $this->$func($ret);
        return $ret;
    }

    private function jsonToArray($json)
    {
    	$ret = json_decode($json,true);
		if(is_array($ret))
		{
			unset($ret['Debug']);
			/*
			if(in_array($ret['ErrorCode'], array(APP_AUTH_EXPIRED, APP_NEED_AUTH, NO_APP_INFO,NO_ACCESS_TOKEN)))
			{
				return $ret;
			}
			if ($ret['ErrorCode'])
			{
				$ret = array();
			}
			*/
			return $ret;
		}
		else
		{
			return $json;
		}
    }
    
 //用于将数组直接用json的方式提交到某一个地址
    public function curl_json($url,$data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'data='.json_encode($data));
		$response  = curl_exec($ch);
		$head_info = curl_getinfo($ch);
		if($head_info['http_code']!= 200)
		{
			$error = array('return' =>'fail');
			return json_encode($error);
		}
		curl_close($ch);//关闭
		return $response;
	}
	
	//直接提交文件到某一地址
	public function post_files($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->mRequestData);
		$response  = curl_exec($ch);
		curl_close($ch);//关闭
		return $response;
	}

    private function xmlToArray($xml)
    {
    	return $xml;
    }

    private function strToArray($str)
    {
    	return $str;
    }
}
?>