<?php
/***************************************************************************
* LivCMS5.0
* (C)2009-2010 HOGE Software.
*
* $Id: exec.php 19 2011-05-19 08:34:49Z develop_tong $
***************************************************************************/
$module = SCRIPT_NAME;
$$module = new $module();  

$func = $_INPUT['a'];
if (!method_exists($$module, $func))
{
	$func = 'show';	
}
$$module->$func();
?>