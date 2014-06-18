<?php
require('../lib/templates/template.class.php');
class output
{
	private $mTpl;
	function __construct()
	{
		$softvar = $_REQUEST['softvar'];
		$group = $_REQUEST['group'];
		$this->mTpl = new Template($softvar, $group);
		if ($_REQUEST['single'])
		{
			$this->mTpl->setCssDir($softvar . '/');
		}
	}
	
	function __destruct()
	{
	}
	
	public function show()
	{
		$template_name = $_REQUEST['template'];
		echo $this->mTpl->ParseTemplate($template_name);
	}

	public function getcss()
	{
		$template_name = $_REQUEST['template'];
		$css = $this->mTpl->fetchCssFile($template_name);
		echo json_encode($css);
	}

	public function getjs()
	{
		$template_name = $_REQUEST['template'];
		echo $this->mTpl->fetchJsFile($template_name);
	}

	public function getvars()
	{
		$template_name = $_REQUEST['template'];
		$vars = $this->mTpl->fetchTemplateVars($template_name);
		echo json_encode($vars);
	}
}
$out = new output();
$action = $_REQUEST['a'];
if (!method_exists($out,$action))
{
	$action = 'show';
}
$out->$action();
?>
