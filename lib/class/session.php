<?php
/***************************************************************************
* LivCMS5.0
* (C)2004-2010 HOGE Software.
*
* $Id: session.php 1618 2012-09-25 06:40:17Z zhuld $
***************************************************************************/
session_start();
class Session
{
	private $user = array();
	//加载用户信息
	public function load_user()
	{
		if ($_SESSION['custom_userinfo'])
		{
			$this->user = $_SESSION['custom_userinfo'];
		}
		return $this->user;
	}

	//卸载用户信息
	public function unload_user()
	{
		$_SESSION['custom_userinfo'] = array();
	}
}
?>