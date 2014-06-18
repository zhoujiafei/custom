<?php
/***************************************************************************
* LivSNS 0.1
* (C)2004-2010 HOGE Software.
*
* $Id: template.class.php 519 2010-12-14 06:12:26Z develop_tong $
***************************************************************************/
/**
 * 系统模板解析类，解析系统模板
 * @author develop_tong
 *
 */
define('COMBO', false);
define('DEVELOP_MODE', true);
class Template
{
    private $mDefaultTemplateDir = '';
    private $mTemplateDir = '';
    private $mTemplateLibDir = '';

    private $softvar = '';
    private $mCssDir = '';
	function __construct($softvar, $group = 'default')
	{
	    $this->softvar = $softvar;
	    $this->group = $group;
		$this->mTemplateLibDir = 'tpl/lib/';
		$this->mTemplateDir = 'tpl/' . $softvar . '/' . $group . '/';
		$this->mDefaultTemplateDir =  'tpl/' . $softvar . '/default/';
		$this->setCssDir();
	}
	
	function __destruct()
	{
	}
	

	public function setCssDir($dir = '')
	{
		$this->mCssDir = $dir;
	}
	/**
	 * 解析模板，生成模板缓存
	 * @param $FileName
	 * @return String
	 */
	public function ParseTemplate($FileName = '')
	{
		$content = $this->mergeTemplate($FileName);
		$tpljscode = $this->parseJsCsscode($content);
		$tplcsscode = $this->parseJsCsscode($content, 'css');
		//$content = str_replace('}{', '} {', $content);

		//将jquery tmpl的模板标签替换下，以免与php的冲突
		$arrkey = array('{{if ', '{{/if}}', '{{else}}');
		$arrvalue = array('{{ if ', '{{ /if}}', '{{ else}}');

		$content = str_replace($arrkey, $arrvalue, $content);

        //echo '<pre>'.$content.'</pre>';
		$pregs = array(
			'/\/\* \$Id: .*? \$ \*\//is',
			"/<\?php[\s]*[\n]*\?>/is",
			"/{(\\$[a-zA-Z0-9_\[\]\-\'\"\$\>\.]+)}/",
			'/\{if[\s]*(.*?)\}/is',
			'/\{else[\s]*if[\s]*(.*?)\}/is',
			'/\{else\}/is',
			'/\{foreach[\s]*(.*?)\}/is',
			'/\{for[\s]*(.*?)\}/is',
			'/\{while[\s]*(.*?)\}/is',
			'/([^{]?)\{\/if\}([^}]?)/is',
			'/\{\/foreach\}/is',
			'/\{\/for\}/is',
			'/\{\/while\}/is',
			'/\{code\}/is',
			'/\{\/code\}/is',
			'/\{css:(.*?)\}/is',
			'/\{js:(.*?)\}/is',
			'/\{csshere\}/is',
			'/\{jshere\}/is',
			'/($\s*$)|(^\s*^)/m',
		);
		$pregs_replace = array(
			'',
			'',
			"<?php echo \${1};?>",
			"<?php if(\${1}){ ?>",
			"<?php } elseif(\${1}) { ?>",
			"<?php } else { ?>",
			"<?php foreach (\${1}){ ?>",
			"<?php for (\${1}){ ?>",
			"<?php while (\${1}){ ?>",
			"\${1}<?php } ?>\${2}",
			"<?php } ?>",
			"<?php } ?>",
			"<?php } ?>",
			"<?php ",
			" ?>",
			'',
			'',
			"\n{$tplcsscode}",
			"\n{$tpljscode}",
			'',
		);
		$content = preg_replace($pregs, $pregs_replace, $content);//匹配样式中的变量
		//$content = preg_replace($pregs, $pregs_replace, $content);//匹配样式中的变量

        $content = str_replace($arrvalue, $arrkey, $content);

		if (!preg_match('/(\<head\>)/is', $content))
		{
			$content = "{$tplcsscode}\n{$tpljscode}\n" . $content;
		}
		
		return $content;
	}

	public function fetchCssFile($filename = 'head', $dir = 'head/')
	{
		if (!$filename)
		{
			$filename = 'head';
		}
		$content = $this->mergeTemplate($filename);
		$preg = '/\{css:(.*?)\}/is';
		preg_match_all($preg, $content, $match);
		$css = array();
		if ($match[1])
		{
			foreach ($match[1] AS $vv)
			{
				$tmp = explode(',', $vv);
				foreach ($tmp AS $v)
				{
					if (!in_array($v, $css))
					{
						$content = $this->fetchCssContent($v);
						$css[$v . '.css'] = $content;
					}
				}
			}
		}
		return $css;
	}

	public function fetchTemplateVars($FileName)
	{
		$file = $this->parseTplDir($FileName . '.php');
		if (!$file)
		{
			return;
		}
		$content = $this->mergeTemplate($FileName);
		$preg = '/\/\* \$Id: .*? \$ \*\//is';
		$content = preg_replace($preg, '', $content);
		$varpreg = "/\\$[a-zA-Z0-9_\[\]\-\'\>\.]+/";
		preg_match_all($varpreg, $content, $match);
		$vars = array();
		$vars['all'] = $match[0];
		$vars['if'] = array();
		$vars['loop'] = array();
		$preg = '/\{if[\s]*(.*?)\}/is';
		preg_match_all($preg, $content, $match);
		if ($match[1])
		{
			foreach ($match[1] AS $k => $v)
			{
				$vars['if0'][$k] = $v;
				preg_match_all($varpreg, $v, $varmatch);
				if ($varmatch[0])
				{
					$vars['if'][$k] = $varmatch[0];
				}
			}
		}
		$preg = '/\{foreach[\s]*(.*?)\}/is';
		preg_match_all($preg, $content, $match);
		if ($match[1])
		{
			foreach ($match[1] AS $k => $v)
			{
				$vars['loop']['foreach0'][$k] = $v;
				preg_match_all($varpreg, $v, $varmatch);
				if ($varmatch[0])
				{
					$vars['loop']['foreach'][$k] =$varmatch[0];
				}
			}
		}
		$content = preg_replace($preg, '', $content);
		$preg = '/\{while[\s]*(.*?)\}/is';
		preg_match_all($preg, $content, $match);
		if ($match[1])
		{
			foreach ($match[1] AS $k => $v)
			{
				$vars['loop']['while0'][$k] = $v;
				preg_match_all($varpreg, $v, $varmatch);
				if ($varmatch[0])
				{
					$vars['loop']['while'][$k] = $varmatch[0];
				}
			}
		}
		$preg = '/\{for[\s]*(.*?)\}/is';
		preg_match_all($preg, $content, $match);
		if ($match[1])
		{
			foreach ($match[1] AS $k => $v)
			{
				$vars['loop']['for0'][$k] = $v;
				preg_match_all($varpreg, $v, $varmatch);
				if ($varmatch[0])
				{
					$vars['loop']['for'][$k] = $varmatch[0];
				}
			}
		}
		//echo '<pre>';
		$filtervar = array();
		foreach ($vars['loop'] AS $kkk => $vvv)
		{
			if ($vvv)
			{
				foreach ($vvv AS $kk => $vv)
				{
					if (is_array($vv))
					{
						foreach ($vv AS $k => $v)
						{
							$filtervar[] = $v;
						}
					}
				}
			}
		}
		
		foreach ($vars['all'] AS $k => $v)
		{
			if (in_array($v, $filtervar))
			{
				unset($vars['all'][$k]);
			}
		}
		return $vars;
	}

	private function parseCssCode($css)
	{
		$css = explode(',', $css);
		$csscode =array();
		foreach ($css AS $v)
		{
			if (!in_array($v, $csscode))
			{
				$csscode[$v] = 	'<link rel="stylesheet" type="text/css" href="<?php echo CSS_FILE_URL; ?>' . $this->mCssDir . $v . '"/>';
			}
		}
		return implode("\n", $csscode);
	}

	private function fetchCssContent($css)
	{
		$file = $this->parseTplDir($css . '.css', 'css/');
		if (!$file)
		{
			return '';
		}
		return file_get_contents($file);
	}
	
	private function parseTplDir($FileName = '', $dir = '')
	{
		$FileName = $dir . $FileName;

		$file = $this->mTemplateDir . $FileName;
		if (!is_file($file))
		{
			$file = $this->mDefaultTemplateDir . $FileName;
		}
		if (!is_file($file))
		{
			$file = $this->mTemplateLibDir . $FileName;
		}
		if (!is_file($file))
		{
			return false;
		}
		return $file;
	}


	private function parseResDirType($FileName = '', $dir = ''){
        $FileName = $dir . $FileName;

        $file = $this->mTemplateDir . $FileName;
        if (is_file($file))
        {
            return 'SELF';
        }
        $file = $this->mDefaultTemplateDir . $FileName;
        if (is_file($file))
        {
            return 'DEFAULT';
        }
        $file = $this->mTemplateLibDir . $FileName;
        if (is_file($file))
        {
            return 'LIB';
        }
	}
	/**
	 * 解析嵌套模板，将嵌套引入的模板生成单一文件
	 * @param $Content， 模板内容
	 * @return String
	 */
	private function ParseNestTemplate($Content)
	{
		$eregtag = '/\{template:(.*?(?=[\/\}]))[\/]{0,1}(.*?(?=[\/\}]))\}/ise';
		$Content = preg_replace($eregtag, "\$this->mergeTemplate('\\2', '\\1', 1)", $Content);
		return $Content;
	}

	private function mergeTemplate($FileName, $dir = '', $type = 0)
	{
		//echo $FileName . '<br />';
		$getfile = explode(',', $FileName);
		$FileName = $getfile[0];
		unset($getfile[0]);
		if (!$FileName)
		{
			$FileName = $dir;
		}
		if ($type)
		{
			if (!$dir)
			{
				$dir = $FileName . '/';
			}
			else
			{
				$dir .= '/';
			}
		}
		$file = $this->parseTplDir($FileName . '.php', $dir);
		if (!$file)
		{
			return;
		}
		$content = file_get_contents($file);
		$content = $this->ParseNestTemplate($content);
		$content = $this->parseEvalCode($content);
		//$content = str_replace(array("\n", "\r"), "\n", $content);
		if ($getfile)//替换模板变量
		{
			if ($getfile[1])
			{
				$preg = "/(\\$[a-zA-Z0-9_\.\$]+)/";
				$name = trim($getfile[1]);
				$name = preg_replace($preg, "<?php echo \${1}; ?>", $name);
				$content = str_replace('{$hg_name}', $name, $content);
			}
			if ($getfile[2])
			{
				$content = str_replace('$hg_value', trim($getfile[2]), $content);
			}
			if ($getfile[3])
			{
				$content = str_replace('$hg_data', trim($getfile[3]), $content);
			}
			if ($getfile[4])
			{
				$content = str_replace('$hg_attr', trim($getfile[4]), $content);
			}
		}
		
		if (!$content)
		{
			return;
		}
		return $content;
	}

	private function execEvalCode($code)
	{
		@eval('$code=\'' . $code . '\';');
		//echo $code;
		return $code;
	}

	private function parseEvalCode($content)
	{
		$preg = '/\{evalcode}(.*?)\{\/evalcode\}/ise';
		$content = preg_replace($preg, "\n\$this->execEvalCode('\\1')",  $content);
		return $content;
	}

	private function parseJsCsscode($content, $type = 'js'){
		if(!COMBO){
			return $this->parseJsCsscode_normal($content, $type);
		}else{
			return $this->parseJsCsscode_combo($content, $type);
		}
	}

	private function parseJsCsscode_normal($content, $type = 'js')
	{
		$preg = '/\{' . $type . ':(.*?)\}/is';
		preg_match_all($preg, $content, $match);
		
		$jsscode = array();
		foreach ($match[1] AS $v)
		{
		    $prex = '';
		    if(!DEVELOP_MODE){
                $prex = $this->parseResDirType($v . '.'. $type, $type . '/');
                if($prex == 'SELF'){
                    $prex = $this->softvar . '/' . $this->group . '/' . $type . '/';
                }else if($prex == 'DEFAULT'){
                    $prex = $this->softvar . '/default/' . $type . '/';
                }else if($prex == 'LIB'){
                    $prex = 'lib/' . $type . '/';
                }
		    }

			$jsfile = $this->parseTplDir($v . '.'. $type, $type . '/');
			if (!in_array($v, $jsscode))
			{
				if (is_file($jsfile))
				{
					if ($type == 'js')
					{
						$jsscode[$v] = 	'<script type="text/javascript" src="<?php echo SCRIPT_URL . $__script_dir; ?>' . $prex . $v . '.js"></script>' . "\n";
					}
					else if ($type == 'css')
					{
						$jsscode[$v] = 	'<link rel="stylesheet" type="text/css" href="<?php echo CSS_FILE_URL; ?>' . $this->mCssDir . $prex . $v . '.css" />' . "\n";
					}
				}
				else
				{
					$jsscode[$v] =  '<div style="color:red">' . $v . ".{$type} 文件未获取到或不存在</div>";
				}
			}
		}
		$jscodes = implode('', $jsscode);
		return $jscodes;
	}

	private function parseJsCsscode_combo($content, $type = 'js')
	{
		$preg = '/\{' . $type . ':(.*?)\}/is';
		preg_match_all($preg, $content, $match);
		
		$jsscode = array();

		$js_filter = array(
			'jquery.min.js',
			//'jquery.form.js',
			//'alertbox.min.js'
		);
		$js_filter_script = array();
		
		foreach ($match[1] AS $v)
		{
			$jsfile = $this->parseTplDir($v . '.'. $type, $type . '/');
			if (!in_array($v, $jsscode))
			{
				if ($type == 'js')
				{
					if(in_array($v.'.js', $js_filter)){
						$js_filter_script[$v] = '<script type="text/javascript" src="<?php echo SCRIPT_URL . $__script_dir; ?>' . $v . '.js"></script>' . "\n";
					}else{
						$jsscode[$v] = 	$v.'.js';
					}
				}
				else if ($type == 'css')
				{
					$jsscode[$v] = 	'<link rel="stylesheet" type="text/css" href="<?php echo CSS_FILE_URL; ?>'  . $this->mCssDir . $v . '.css" />' . "\n";
				}
			}
		}
		$jscodes = '';
		if($type == 'js'){
			if($js_filter_script){
				$jscodes .=  implode('', $js_filter_script);
			}
			if($jsscode){
				$jscodes .=  '<script type="text/javascript" src="<?php echo COMBO_URL; ?>combo.php?s='.implode(',', $jsscode).'"></script>';
			}
		}else{
			$jscodes = implode('', $jsscode);
		}
		return $jscodes;
	}
}
?>