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
<h2>欢迎使用客户管理系统</h2>
<p id="page-intro">http://www.hoge.cn</p>
<!-- End .shortcut-buttons-set -->
<div class="clear"></div><script type="text/javascript">
    function change_status(obj)
    {
		var _appid = $(obj).attr('_appid');
		$.get('custom.php',{
			'appid':_appid,
			'a':'audit'
		},function(data){
			var data = eval('('+data+')');
			$('#_status_'+_appid).text(data[0].status_text);
		});
    }
</script>
    <div class="content-box">
      <!-- Start Content Box -->
      <div class="content-box-header">
        <h3>客户信息</h3>
        <ul class="content-box-tabs">
          <li><a href="#tab1" class="default-tab">列表</a></li>
        </ul>
        <div class="clear"></div>
      </div>
      <!-- End .content-box-header -->
      <div class="content-box-content">
        <div class="tab-content default-tab" id="tab1">
          <table>
            <thead>
              <tr>
                <th>
                  <input class="check-all" type="checkbox" />
                </th>
                <th>客户名称</th>
                <th>客户标识</th>
                <th>状态</th>
                <th>客户密钥</th>
                <th>创建时间</th>
                <th>操作</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <td colspan="7">
                <?php echo $pagelink;?>
                 <a href="custom.php?a=detail" class="button" style="float:right;margin-left:10px;">新增</a>
                <!--
                  <div class="pagination"> <a href="#" title="First Page">&laquo; First</a><a href="#" title="Previous Page">&laquo; Previous</a> <a href="#" class="number" title="1">1</a> <a href="#" class="number" title="2">2</a> <a href="#" class="number current" title="3">3</a> <a href="#" class="number" title="4">4</a> <a href="#" title="Next Page">Next &raquo;</a><a href="#" title="Last Page">Last &raquo;</a> </div>
                 -->
                  <!-- End .pagination -->
                  <div class="clear"></div>
                </td>
              </tr>
            </tfoot>
            <tbody>
               <?php if(!$list){ ?>
               	<p>内容为空</p>
               <?php } else { ?>
               		<?php foreach ($list AS $k => $v){ ?>
		              <tr>
		                <td>
		                  <input type="checkbox" name="line_<?php echo $v['appid'];?>" value="<?php echo $v['appid'];?>" />
		                </td>
		                <td><?php echo $v['custom_name'];?></td>
		                <td><?php echo $v['bundle_id'];?></td>
		                <td id="_status_<?php echo $v['appid'];?>" onclick="change_status(this);"  _appid="<?php echo $v['appid'];?>" style="cursor:pointer;color:blue;"><?php echo $v['status_text'];?></td>
		                <td><?php echo $v['appkey'];?></td>
		                <td><?php echo $v['create_time'];?></td>
		                <td>
		                  <!-- Icons -->
		                  <a href="custom.php?a=detail&appid=<?php echo $v['appid'];?>" title="Edit"><img src="<?php echo $RESOURCE_URL;?>icons/pencil.png" alt="Edit" /></a> 
		                  <a href="custom.php?a=delete&appid=<?php echo $v['appid'];?>" title="Delete"><img src="<?php echo $RESOURCE_URL;?>icons/cross.png" alt="Delete" />
		              </tr>
              		<?php } ?>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- End .content-box-content -->
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