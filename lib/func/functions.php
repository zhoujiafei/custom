<?php
/***************************************************************************
* LivCMS5.0
* (C)2004-2010 HOGE Software.
*
* $Id: functions.php 3561 2013-07-04 07:59:39Z develop_tong $
***************************************************************************/

/**
 * 初始化用户输入
 */
function hg_init_input()
{	
	$return = array();
	foreach(array($_GET, $_POST) AS $type)
	{
		if (is_array($type))
		{
			foreach ($type as $k => $v)
			{
				$k = hg_clean_key($k);
				if (is_array($v))
				{
					foreach ($v as $k1 => $v1)
					{
						$k1 = hg_clean_key($k1);
						$return[$k][$k1] = hg_clean_value($v1);
					}
				}
				else
				{
					$return[$k] = hg_clean_value($v);
				}
			}
		}
	}
	return $return;
}


/**
 * 清理用户输入数据
 * @param $key 指定需要清理的数据
 * @param $input 用户输入的数据，默认为$_INPUT
 * @return Array 清理后的数据
 */
function hg_clean_input($key = array(), $input = array())  
{
	if (!$input)
	{
		global $_INPUT;
		$input = $_INPUT;
	}
	foreach ($key AS $k)
	{
		if (!is_array($input[$k]) && $input[$k])
		{
			$input[$k] = hg_clean_value($input[$k]);
		}
	}
	return $input;
}

/**
 * 过滤数组索引
 */
function hg_clean_key($key)
{
	if (is_numeric($key))
	{
		return $key;
	}
	else if (empty($key))
	{
		return '';
	}

	if (strpos($key, '..') !== false)
	{
		$key = str_replace('..', '', $key);
	}

	if (strpos($key, '__') !== false)
	{
		$key = preg_replace('/__(?:.+?)__/', '', $key);
	}

	return preg_replace('/^([\w\.\-_]+)$/', '\\1', $key);
}

/**
 * 过滤输入的数据
 *
 * @param unknown_type $val
 * @return unknown
 */
function hg_clean_value($val)
{
	if ($_REQUEST['html'])
	{
		$val = preg_replace("/<script/i", "&#60;script", $val);
		return $val;
	}
	if (is_numeric($val))
	{
		return $val;
	}
	else if (empty($val))
	{
		return is_array($val) ? array() : '';
	}
	$val = preg_replace("/<script/i", "&#60;script", $val);

	$pregfind = array('&#032;', '<!--', '-->', '>', '<', '"', '!', "'", "\n", '$', "\r");
	$pregreplace = array(' ', '&#60;&#33;--', '--&#62;', '&gt;', '&lt;', '&quot;', '&#33;', '&#39;', "\n", '&#036;', '');
	$val = str_replace($pregfind, $pregreplace, $val);

	return preg_replace('/\\\(&amp;#|\?#)/', '&#092;', $val);
}

/**
 * 按给定的字串长度截取原字符串
 * @param $chars 原字符串
 * @param $limitlen 指定的字串长度
 * @param $cut_suffix 截取后剩余部分替代值
 * @param $doubletoone 英文数字是否2个字符做1长度处理
 * @return 截取后的字符串
 */
function hg_cutchars($chars, $limitlen = '6', $cut_suffix = '…', $doubletoone = false)
{
	$val = hg_csubstr($chars, $limitlen, $doubletoone);
	return $val[1] ? $val[0] . $cut_suffix : $val[0];
}

/**
 * 剪切字符
 *
 * @param string $text
 * @param int $limit
 * @return array
 */
function hg_csubstr($text, $limit = 12, $doubletoone = false)
{
	if (function_exists('mb_substr') && !$doubletoone)
	{
		$more = (mb_strlen($text, 'UTF-8') > $limit) ? TRUE : FALSE;
		if($more)
		{
			$text = mb_substr($text, 0, $limit, 'UTF-8');
		}
		return array($text, $more);
	}
	elseif (function_exists('iconv_substr') && !$doubletoone)
	{
		$more = (iconv_strlen($text, 'UTF-8') > $limit) ? TRUE : FALSE;
		if($more)
		{
			$text = iconv_substr($text, 0, $limit, 'UTF-8');
		}
		return array($text, $more);
	}
	else
	{
		preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $text, $ar);
		$len = 0;
		$more = false;
		$ar = $ar[0];
		if (count($ar) <= $limit)
		{
			return array($text, $more);
		}
		$new_ar = array();
		$temp = '';
		foreach ($ar AS $k => $v)
		{  
			if ($len >= $limit)
			{
				$more = true;
				break;
			}
			$sbit  =  ord($v);         
			if($sbit  <  128)  
			{
				$temp .= $v;
				if (strlen($temp) == 2)
				{
					$new_ar[$len] = $temp;
					$temp = '';
					$len++;
				}
			}
			elseif($sbit  >  223  &&  $sbit  <  240)  
			{   
				$new_ar[$len] = $temp . $v; 
				$temp = '';
				$len++;      
			}
		}
		$text = implode('', $new_ar);
		return array($text, $more);
	}
}

/**
* 将utf8字符转换为unicode编码
* $c 需要转换的字符
* 返回转换后的编码
*/
function hg_utf8_unicode($c) 
{
	switch(strlen($c)) 
	{
		case 1:
			$n = ord($c);
		break;
		case 2:
			$n = (ord($c[0]) & 0x3f) << 6;
			$n += ord($c[1]) & 0x3f;
		break;
		case 3:
			$n = (ord($c[0]) & 0x1f) << 12;
			$n += (ord($c[1]) & 0x3f) << 6;
			$n += ord($c[2]) & 0x3f;
		break;
		case 4:
			$n = (ord($c[0]) & 0x0f) << 18;
			$n += (ord($c[1]) & 0x3f) << 12;
			$n += (ord($c[2]) & 0x3f) << 6;
			$n += ord($c[3]) & 0x3f;
		break;
	}
	return dechex($n);
}

/**
 * 检查 Email 格式是否正确
 */
function hg_clean_email($email = '')
{
	$email = trim($email);
	$email = str_replace(' ', '', $email);
	$email = preg_replace('#[\;\#\n\r\*\'\"<>&\%\!\(\)\{\}\[\]\?\\/\s]#', '', $email);
	if (preg_match('/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/', $email))
	{
		return $email;
	}
	return '';
}


/**
 * 生成随机数字
 *
 * @param intager $length 干扰码长度
 */
function hg_rand_num($length = 5)
{
	$randstr = '0123456789';
	$rlength = strlen($randstr);
	$salt = '';
	for ($i = 0; $i < $length; $i++)
	{
		$n = mt_rand(0, ($rlength-1));
		if(!$randstr[$n] && !$i)
		{
		 $randstr[$n] = '3';
		}
		$salt .= $randstr[$n];
	}
	return $salt;
}


/**
 * 写文件
 *
 * @return intager 写入数据的字节数
 */
function hg_file_write($filename, $content, $mode = 'rb+')
{
	$length = strlen($content);
	@touch($filename);
	if (!is_writeable($filename))
	{
		@chmod($filename, 0666);
	}

	if (($fp = @fopen($filename, $mode)) === false)
	{
		trigger_error('hg_file_write(' . $filename . ') failed to open stream: Permission denied', E_USER_WARNING);
		
		return false;
	}

	flock($fp, LOCK_EX | LOCK_NB);

	$bytes = 0;
	if (($bytes = @fwrite($fp, $content)) === false)
	{
		$errormsg = sprintf('file_write(' . $filename . ') Failed to write %d bytes to %s', $length, $filename);
		trigger_error($errormsg, E_USER_WARNING);
		return false;
	}

	if ($mode == 'rb+')
	{
		@ftruncate($fp, $length);
	}

	@fclose($fp);

	// 检查是否写入了所有的数据
	if ($bytes != $length)
	{
		$errormsg = sprintf('file_write(' . $filename . ') Only %d of %d bytes written, possibly out of free disk space.', $bytes, $length);
		trigger_error($errormsg, E_USER_WARNING);
		return false;
	}

	// 返回长度
	return $bytes;
}

/**
 * 格式化数字
 *
 * @param boolean $bytesize 是否带字节单位
 */
function hg_fetch_number_format($number, $bytesize = false)
{
	$decimals = 0;
	$type = '';
	if ($bytesize)
	{
		if ($number >= 1073741824)
		{
			$decimals = 2;
			$number = round($number / 1073741824 * 100) / 100;
			$type = ' GB';
		}
		else if ($number >= 1048576)
		{
			$decimals = 2;
			$number = round($number / 1048576 * 100) / 100;
			$type = ' MB';
		}
		else if ($number >= 1024)
		{
			$decimals = 1;
			$number = round($number / 1024 * 100) / 100;
			$type = ' KB';
		}
		else
		{
			$decimals = 0;
			$type = ' Bytes';
		}
	}
	$number = str_replace('_', '&nbsp;', number_format($number , $decimals, '.', ','));
	return $number . $type;
}

/**
 * 创建目录函数
 *
 * @param $dir 需要创建的目录
 */
function hg_mkdir($dir)
{
	if (!is_dir($dir))
	{
		if(!@mkdir($dir, CREATE_DIR_MODE, 1))
		{
			return false;//创建目录失败
		}
	}
	return true;
}
/**
 * 创建随机生成字符串
 *
 * @param $length salt长度
 */
function hg_generate_salt( $length = 6 ) {
    // salt字符集，可任意添加你需要的字符
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&';
    $salt = '';
    for ( $i = 0; $i < $length; $i++ ) 
    {
        $salt .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
    return $salt;
}
/**
* 检查IP地址是否正确。
*/
function hg_checkip ($ipaddres) 
{
	$preg="/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
	if(preg_match($preg,$ipaddres))
	{
		return true;
	}
	return false;
}

/**
 * 获取IP
 *
 * @param none
 */
function hg_getip() 
{
	global $_INPUT;
	if ($_INPUT['lpip'])
	{
		if (hg_checkip($_INPUT['lpip']))
		{
			return $_INPUT['lpip'];
		}
	}
	if (isset($_SERVER)) 
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif ($_SERVER['HTTP_X_REAL_IP'])
		{
			$realip = $_SERVER['HTTP_X_REAL_IP'];
		}
		elseif (isset($_SERVER['HTTP_CLIENT_IP'])) 
		{
			$realip = $_SERVER['HTTP_CLIENT_IP'];
		} 
		else 
		{
			$realip = $_SERVER['REMOTE_ADDR'];
		}
	} 
	else 
	{
		
		if (getenv("HTTP_X_FORWARDED_FOR")) 
		{
			$realip = getenv( "HTTP_X_FORWARDED_FOR");
		}
		elseif (getenv('HTTP_X_REAL_IP'))
		{
			$realip = getenv('HTTP_X_REAL_IP');
		} 
		elseif (getenv("HTTP_CLIENT_IP")) 
		{
			$realip = getenv("HTTP_CLIENT_IP");
		}
		else 
		{
			$realip = getenv("REMOTE_ADDR");
		}
	}
	$realip = explode(',', $realip);
	$realip = $realip[0];
	return $realip;
}

function hg_get_cookie($name)
{
	global $gGlobalConfig;
	$cookie_name = $gGlobalConfig['cookie_prefix'] . $name;

	return $_COOKIE[$cookie_name];
}

function hg_authcode($string, $operation, $key = '') 
{
     $key = md5($key ? $key : $GLOBALS['auth_key']);
     $key_length = strlen($key);
     $string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string.$key), 0, 8).$string;
     $string_length = strlen($string);
     $rndkey = $box = array();
     $result = '';
     for($i = 0; $i <= 255; $i++)
	{
		$rndkey[$i] = ord($key[$i % $key_length]);
		$box[$i] = $i;
	}

	for($j = $i = 0; $i < 256; $i++) 
	{
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
    for($a = $j = $i = 0; $i < $string_length; $i++)
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
     }

    if($operation == 'DECODE') 
	{
		if(substr($result, 0, 8) == substr(md5(substr($result, 8).$key), 0, 8)) 
		{
			return substr($result, 8);
		}
		else 
		{
			return '';
		}
     } 
	else 
	{
		return str_replace('=', '', base64_encode($result));
	}
}

/**
 * 返回无样式数据，用于测试
 * @param $obj 
 */
function hg_pre($obj)
{
	echo	$html = "<pre>";
	if(empty($obj))
	{
		var_dump($obj);
	}
	else 
	{
		print_r($obj);
	}
	echo "</pre>";
	exit;
}


function hg_convert_encoding($str, $from = '', $to = '')
{
	static $convert = null;
	if (is_null($convert))
	{
		include_once(ROOT_PATH . 'lib/class/encoding.class.php');
		$convert = new encoding();
	}
	return $convert->convert($str, $from, $to);
}


/**
 * 替换或者过滤特殊字符串
 * @param $text
 * return $text
 */
function hg_filter_chars($text)
{
	$text = str_replace("'","’",$text);//单引号问题
	return $text;
}

function hg_check_application($id)
{
	global $_INPUT, $gDB;
	$id = $id ? $id : $_INPUT['id'];
	$id = intval($id);
	$application = $gDB->query_first("SELECT * FROM " . DB_PREFIX . "applications WHERE id={$id}");
	if (!$application)
	{
		return false;
	}
	return $application;
}

function hg_fetch_query_sql($queryvalues, $table, $condition = '', $db_pre = DB_PREFIX, $insert_type = 'INSERT')
{
	global $gDB;
	$gDB = hg_checkDB();
	$numfields = count($queryvalues);
	if (empty($condition))
	{
		$fieldlist_arr = array();
		$valuelist_arr = array();
		foreach($queryvalues AS $fieldname => $value)
		{
			$fieldlist_arr[] = $fieldname;
			$fieldvalue = (is_numeric($value) AND intval($value) == $value) ? "'$value'" : "'" . addslashes($value) . "'";
			$valuelist_arr[] = $fieldvalue;
		}
		$fieldlist  = implode(", ", $fieldlist_arr);
		$valuelist = implode(", ", $valuelist_arr);
		unset($fieldlist_arr, $valuelist_arr);
		$sql = $insert_type . " INTO " . $db_pre. "$table ($fieldlist) VALUES ($valuelist)";
	}
	else
	{
		$qs_arr = array();
		foreach($queryvalues AS $fieldname => $value)
		{
			$fieldvalue = (is_numeric($value) AND intval($value) == $value) ? "'$value'" : "'" . addslashes($value) . "'";
			$qs_arr[] = $fieldname." = ".$fieldvalue;
		}
		$querystring = implode(', ', $qs_arr);
		unset($qs_arr);
		$sql = "UPDATE " . $db_pre. "$table SET $querystring WHERE $condition";
	}
    $gDB->query($sql);
}

function hg_load_node($id,  $mod_uniqueid = '')
{
	$file = CACHE_DIR . 'program/node/' . $id . '.php';
	if (!class_exists('curl'))
	{
		include_once(ROOT_PATH . 'lib/class/curl.class.php');
	}
	if (DEVELOP_MODE || !is_file($file))
	{
		include_once(ROOT_PATH . 'lib/class/node.class.php');
		$program = new nodeapi();
		$file = $program->compile($id, $mod_uniqueid);
	}
	return $file;
}

function hg_sec2str($time, $format = '')
{
	$h = intval($time / 3600);
	$h = str_pad($h, 2, '0', STR_PAD_LEFT);
	$sec = $time % 3600;
	$m = intval($sec / 60);
	$m = str_pad($m, 2, '0', STR_PAD_LEFT);
	$sec = $sec % 60;
	$sec = str_pad($sec, 2, '0', STR_PAD_LEFT);
	return $h . '小时' . $m . '分' . $sec . '秒';
}

function hg_editors($name,$value = '',$w = 0,$h = 0,$num = 0)
{
	if(empty($name))
	{
		return false;
	}
	include_once(ROOT_PATH . 'lib/editor.php');
	$editor = new Editor($num);
	$script = $editor->InitEditor($name,$w,$h);//_page
	$html = '<textarea  id="' . $name . '" name="' . $name . '" class="text" cols="100" rows="5">' . $value . '</textarea>' . $script ;
	return $html;
}




function hg_editor_test($name,$contentinfo = array(),$w = 0,$h = 0,$num = 0)
{
	if(empty($name))
	{
		return false;
	}
	include_once(ROOT_PATH . 'lib/editor.php');
	$editor = new Editor($num);
	$script = $editor->InitEditor_page($name,$w,$h);//_page
	$html = hg_editor_content($name,$contentinfo,$w,$h,$num,1);
	return $script . $html;
}

function hg_editor_content($name,$contentinfo = array(),$w = 0,$h = 0,$num = 0,$declare=0)
{
	$script_extra = '';
	$pagenum = count($contentinfo);
	if($pagenum > 0)
	{
		$p_nav = "上一页";
		$t_content = "";

		foreach($contentinfo as $key => $val)
		{
			$t_content .= "<textarea id='" . $name .$key. "' name='" . $name .$key. "' style='display:none;'>" . stripslashes($val) ."</textarea>";
		//	$t_content .= "<input type='hidden' name='lititle" .$key. "' value='" . stripslashes($val['pagetitle']) ."'  ><br />";
			if($key == 1)
			{
				$color = "#FF0000";
			}
			else
			{
				$color = "#000000";
			}
			$p_nav .= "<a href='javascript:void(0);' onclick='gotopage(".$key.")'>[<span style='color:".$color."'>".$key."</span>]</a>&nbsp;";
			if($key % 10 == 0)
			{
				$p_nav .= "<br />";
			}
		}
		$p_nav .= "&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='next_page(".($pagenum+1).")' >新建页</a>";
	}
	else
	{
		$p_nav = "";
		$p_nav = "[<span style=color:red;>1</span>] <a href='javascript:void(0)' onclick='next_page(2)'>新建页</a>";
	}
	
	$html = '<div id="playclewinfo"></div>
		<textarea id="' . $name . '" name="' . $name . '" style="width:' . $w . 'px;" rows="24">' . stripslashes($contentinfo[1]) . '</textarea>';
	if($declare)
	{
		$html .= '';
	}
	$html .= '<script language="javascript" type="text/javascript">oEdit1.REPLACE("' . $name . '");</script><div id="page_nav" style="text-align:center;  ">' . $p_nav . '</div>
		<input type="hidden" name="pagenum" id="pagenum" value="' . ($pagenum?$pagenum:1) .'">
		<div id="tempcontent" style="display:none;">' . stripslashes($t_content) . '</div>
		';
	return $html;
}

function hg_editor($name,$contentinfo,$w = 0,$h = 0,$num = 0)
{
	if(empty($name))
	{
		return false;
	}
	include_once(ROOT_PATH . 'lib/editor.php');
	$editor = new Editor($num);
	$script = $editor->InitEditor_page($name,$w,$h);//_page $contentinfo[0] 
	$html = $script . '<textarea  id="' . $name . '" name="' . $name . '" class="text" cols="100" rows="5">' . $contentinfo . '</textarea><script language="javascript" type="text/javascript">oEdit1.REPLACE("' . $name . '");</script>' ;
	return $html;
}

function hg_editor_init($name,$w = 0,$h = 0,$num = 0)
{
	include_once(ROOT_PATH . 'lib/editor.php');
	$editor = new Editor($num);
	$script = $editor->InitEditor_page($name,$w,$h);
	return $script ;
}

function hg_encript_str($str, $en = true)
{
	$salt = CUSTOM_APPKEY;
	if ($en)
	{
		$str = $str . $salt;
		$str = base64_encode($str);
	}
	else
	{
		$str = base64_decode($str);
		$str = str_replace($salt, '', $str);
	}
	return $str;
}

//xml特殊字符的转义函数
function xmlencode($tag)
{
	$tag = str_replace("&", "&amp;", $tag);
	$tag = str_replace("<", "&lt;", $tag);
	$tag = str_replace(">", "&gt;", $tag);
	$tag = str_replace("'", "&apos;", $tag);
	$tag = str_replace('"', '&quot;', $tag);
	return $tag;
}

//数组最大深度
function array_depth($array) {
        $max_depth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = array_depth($value) + 1;
                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }        
        return $max_depth;
 }

function get_serv_file($server, $file)
{
	$socket = new hgSocket();
	$ip = trim($server['ip']);
	$port = intval($server['port']);
	if (!$ip || !$port)
	{
		return array();
	}	
	$cmd = array(
		'action' => 'getfile',
		'para' => $file,
		'user' => $user,
		'pass' => $pass,
	);
	$con = $socket->connect($ip, $port);
	$socket->sendCmd($cmd);
	$content = $socket->readall();
	if ($content == 'Can\'t access this file')
	{
		$content = '';
	}
	return $content;
}
function hg_run_cmd($server, $cmd, $para= '', $dir = '')
{
	$socket = new hgSocket();
	$ip = trim($server['ip']);
	$port = intval($server['port']);
	if (!$ip || !$port)
	{
		return array();
	}	
	$cmd = array(
		'action' => $cmd,
		'para' => $para,
		'dir' => $dir,
		'user' => $user,
		'pass' => $pass,
	);
	$con = $socket->connect($ip, $port);
	$socket->sendCmd($cmd);
	$content = $socket->readall();
	return $content;
}

function write_serv_file($server, $file, $content, $charset = '')
{
	$socket = new hgSocket();
	$ip = trim($server['ip']);
	$port = intval($server['port']);
	if (!$ip || !$port || !$content)
	{
		return array();
	}	
	$content = preg_replace('/\r{1,}/', '', $content);
	$content = preg_replace('/\n{2,}/', "\n", $content);
	$cmd = array(
		'action' => 'write2file',
		'para' => $file,
		'data' => $content,
		'user' => $user,
		'pass' => $pass,
		'charset' => $charset,
	);
	$con = $socket->connect($ip, $port);
	$socket->sendCmd($cmd);
	$content = $socket->readall();
	if ($content == 'success')
	{
		return 1;
	}
	return 0;
}

function hg_get_hosts($content)
{
	if (!$content)
	{
		return array();
	}
	$content = str_replace("\t", ' ', $content);
	$content = str_replace(array("\r\n", "\r"), "\n", $content);
	$hosts = explode("\n", $content);
	$lines = array();
	$domain = array();
	foreach($hosts AS $line)
	{
		$line = trim($line);
		$fc = substr($line, 0, 1);
		if ($fc == '#' || !$fc)
		{
			$lines[] = $line;
		}
		else
		{
			$line = explode(' ', $line);
			$ip = $line[0];
			unset($line[0]);
			foreach($line AS $v)
			{
				$v = trim($v);
				if ($v)
				{
					$domain[$v] = $ip;
				}
			}
		}
	}
	return $domain;
}
class hgSocket
{
	private $scoket;
	private $connetced;
	function __construct()
	{
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	}
	function __destruct()
	{
		$this->close();
	}

	public function close()
	{
		if($this->connetced)
		{
			@socket_close($this->socket);
		}
	}
	public function connect($ip, $port)
	{
		//echo $ip . $port . '<br />';
		$result = @socket_connect($this->socket, $ip, $port);
		if (!$result)
		{
			$this->connetced = false;
		}
		else
		{
			$this->connetced = true;
		}
		return $this->connetced;
	}

	public function sendCmd($cmd)
	{
		if (!$this->connetced)
		{
			return false;
		}
		if (!isset($cmd['charset']))
		{
			$cmd['charset'] = '';
		}
		$str = json_encode($cmd);
		socket_write($this->socket, $str, strlen($str));
	}

	public function read($size = 256)
	{
		if (!$this->connetced)
		{
			return false;
		}
		$out = socket_read($this->socket, $size);
		return $out;
	}

	public function readall()
	{
		if (!$this->connetced)
		{
			return false;
		}
		$data = '';
		$size = 4096;
		while ($out = $this->read($size))
		{
			$data .= $out;
			if (strlen($out) < $size)
			{
				break;
			}
		}
		return $data;
	}
}
function hg_flushMsg($msg, $url = '')
{
	echo $msg . str_repeat(' ', 4096). '<br /><script type="text/javascript">'; 
	if ($url)
	{
		echo 'document.location.href="' . $url . '";';
	}
	echo 'window.scrollTo(0,10000);</script>';
	ob_flush();
}

function build_page_link($page_info = array()) 
{
    if (empty($page_info) || !$page_info['totalNum'] || !$page_info['perPage']) {
        return '';
    }
    //print_r($_SERVER);exit;
    $extralink = '';    
    global $_INPUT;
     $link = '?';
    foreach ((array)$_INPUT AS $k => $v)
    {
        if ($k =='page' || $k =='perPage')  {
            continue;
        }   
        if($k == 'referto' && $v){
            $v = urlencode($v);
        }
        $extralink .= $link . $k . '=' . $v;
        $link = '&amp;';
    }
    $params = array(    
        'baseUrl'  => $extralink,
        'totalNum' => $page_info['totalNum'],
        'perPage'  => $page_info['perPage'],
        'currPage' => $page_info['currPage'],
    );
    include (ROOT_PATH . 'lib/class/page.class.php');
    $page = new page($params);
    $page_link = $page->built_page();
    return $page_link;
}

?>