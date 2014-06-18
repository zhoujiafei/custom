<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>客户管理系统</title><link rel="stylesheet" type="text/css" href="<?php echo CSS_FILE_URL; ?>custom/reset.css" />
<link rel="stylesheet" type="text/css" href="<?php echo CSS_FILE_URL; ?>custom/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo CSS_FILE_URL; ?>custom/invalid.css" /><script type="text/javascript" src="<?php echo SCRIPT_URL . $__script_dir; ?>jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo SCRIPT_URL . $__script_dir; ?>simpla.jquery.configuration.js"></script>
<script type="text/javascript" src="<?php echo SCRIPT_URL . $__script_dir; ?>facebox.js"></script>
<script type="text/javascript" src="<?php echo SCRIPT_URL . $__script_dir; ?>jquery.wysiwyg.js"></script></head>
<body>
<div id="body-wrapper">
  <div id="sidebar">
    <div id="sidebar-wrapper">
      <!-- Sidebar with logo and menu -->
      <h1 id="sidebar-title"><a href="#">客户管理系统</a></h1>
      <a href="#"><img id="logo" src="<?php echo $RESOURCE_URL;?>logo.png" alt="客户管理系统" /></a>
      <!-- Sidebar Profile links -->
      <div id="profile-links"> 你好, <a href="#" title="Edit your profile"><?php echo $_user['username'];?></a> | <a href="login.php?a=logout" title="退出">退出</a> </div>
      <ul id="main-nav">
        <?php foreach ($_menus AS $_k => $_v){ ?>
        	<li> 
	        	<a href="<?php echo $_v['link'];?>" class="nav-top-item"><?php echo $_v['zh_name'];?></a>
	        	<?php if($_v['child']){ ?>
	        		<ul>
	        		<?php foreach ($_v['child'] AS $_kk => $_vv){ ?>
	        			<li><a href="<?php echo $_vv['link'];?>"><?php echo $_vv['zh_name'];?></a></li>
	        		<?php } ?>
	        		</ul>
	        	<?php } ?>
        	</li>
        <?php } ?>
      </ul>
    </div>
  </div>
<div id="main-content">
<!-- Page Head -->
<h2>Welcome</h2>
<p id="page-intro">What would you like to do?</p>
<!-- End .shortcut-buttons-set -->
<div class="clear"></div><div class="content-box">
<ul style="width:500px;">
	<li>
		<p>操作系统：<?php echo PHP_OS;  ?></p>
	</li>
	<li>
		<p>服务器信息：<?php echo $_SERVER['SERVER_SOFTWARE']; ?> </p>
		<p>PHP版本： <?php echo PHP_VERSION; ?></p>
		<p>ZEND版本：<?php echo zend_version(); ?></p>
		<?php 
		$max_execution_time = get_cfg_var("max_execution_time");
		$memory_limit = get_cfg_var("memory_limit");
		 ?>
		<?php if($max_execution_time){ ?>
		<p>最大执行时间：<?php echo $max_execution_time;?>秒</p>
		<?php } ?>
		<?php if($memory_limit){ ?>
		<p>占用最大内存：<?php echo $memory_limit;?></p>
		<?php } ?>
	</li>
</ul>
</div>
	<div class="clear"></div>
    <div id="footer"> 
    <small>
      &#169; Copyright 2014 南京厚建软件有限公司 | Powered by <a href="http://www.hoge.cn/">http://www.hoge.cn</a> | <a href="#">Top</a>
    </small> 
    </div>
  </div>
</div>
</body>
</html>