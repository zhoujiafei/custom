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
<div class="clear"></div><?php if(is_array($formdata) && $a == 'update'){ ?>
	<?php foreach ($formdata as $key => $value){ ?>
		<?php 
			$$key = $value;			
		 ?>
	<?php } ?>
<?php } ?>
    <div class="content-box">
      <!-- Start Content Box -->
      <div class="content-box-header">
        <h3>客户信息</h3>
        <ul class="content-box-tabs">
          <li><a href="#tab1" class="default-tab">表单</a></li>
        </ul>
        <div class="clear"></div>
      </div>
	<div class="content-box-content">
		<div class="tab-content default-tab" id="tab1">
          <form action="custom.php" method="post">
            <fieldset>
            <!-- Set class to "column-left" or "column-right" on fieldsets to divide the form into columns -->
            <p>
              <label>客户名称</label>
              <input class="text-input small-input" type="text"  name="custom_name" value="<?php echo $custom_name;?>"/>
            </p>
            <p>
              <label>客户项目所在域名</label>
              <input class="text-input small-input" type="text" name="domain" value="<?php echo $domain;?>" />
            </p>
            <p>
              <label>客户标识</label>
              <input class="text-input small-input" type="text" name="bundle_id" value="<?php echo $bundle_id;?>"/>
              <span class="input-notification attention png_bg">标识不能有重复</span>
            </p>
            <p>
              <label>客户描述</label>
              <textarea class="text-input textarea wysiwyg" name="custom_desc" cols="79" rows="15"><?php echo $custom_desc;?></textarea>
            </p>
            <p>
              <label>授权到期时间</label>
              <input class="text-input small-input" type="text" name="expire_time" value="<?php echo $expire_time;?>"/>
            </p>
            <p>
              <label>客户简称</label>
              <input class="text-input small-input" type="text" name="display_name" value="<?php echo $display_name;?>" />
            </p>
           	<p>
              <label>是否有授权</label>
              <input type="radio" name="is_auth" value="1" <?php if($is_auth){ ?> checked="checked"<?php } ?>/>
              是<br />
              <input type="radio" name="is_auth" value="0" <?php if(!$is_auth){ ?> checked="checked"<?php } ?>/>
              否 
            </p>
            <p>
              <label>安装类型</label>
              <select name="install_type" class="small-input">
                <?php foreach ($_configs['install_type'] AS $_key => $_value){ ?>
                <option value="<?php echo $_key;?>" <?php if($_key == $install_type){ ?>selected<?php } ?>><?php echo $_value;?></option>
                <?php } ?>
              </select>
            </p>
            <p>
              <label>应用限制</label>
              <input class="text-input small-input" type="text" name="app_limit" value="<?php echo $app_limit;?>" />
            </p>
            <p>
              <label>源码类型</label>
              <select name="source" class="small-input">
              	<?php foreach ($_configs['source'] AS $_key => $_value){ ?>
                <option value="<?php echo $_key;?>"  <?php if($_key == $source){ ?>selected<?php } ?>><?php echo $_value;?></option>
                <?php } ?>
              </select>
            </p>
            <p>
              <label>采集授权时间</label>
              <input class="text-input small-input" type="text" name="gather_expire" value="<?php echo $gather_expire;?>"/>
            </p>
            <p>
              <label>授权提示方式</label>
              <input class="text-input small-input" type="text" name="tip_way"  value="<?php echo $tip_way;?>" />
            </p>
            <p>
              <label>提示信息显示时长</label>
              <input class="text-input small-input" type="text" name="tip_dur"  value="<?php echo $tip_dur;?>" />
            </p>
            <p>
              <label>提示内容</label>
              <input class="text-input small-input" type="text" name="tip_text"   value="<?php echo $tip_text;?>" />
            </p>
            <p>
              <label>转码到期时间</label>
              <input class="text-input small-input" type="text" name="codec_expire"   value="<?php echo $codec_expire;?>" />
            </p>
            <p>
              <label>收录到期时间</label>
              <input class="text-input small-input" type="text" name="record_expire"   value="<?php echo $record_expire;?>" />
            </p>
            <p>
              <label>允许创建的频道数</label>
              <input class="text-input small-input" type="text" name="channel_num"   value="<?php echo $channel_num;?>" />
            </p>
            <p>
              <label>频道功能到期时间</label>
              <input class="text-input small-input" type="text" name="channel_expire"  value="<?php echo $channel_expire;?>" />
            </p>
            <p>
              <label>是否有转码</label>
              <input type="radio" name="has_codec" value="1" <?php if($has_codec){ ?> checked="checked"<?php } ?> />
              是<br />
              <input type="radio" name="has_codec" value="0" <?php if(!$has_codec){ ?> checked="checked"<?php } ?> />
              否 
            </p>
            <p>
              <label>是否有收录</label>
              <input type="radio" name="has_record" value="1" <?php if($has_record){ ?> checked="checked"<?php } ?> />
              是<br />
              <input type="radio" name="has_record" value="0" <?php if(!$has_record){ ?> checked="checked"<?php } ?> />
              否 
            </p>
            <p>
              <label>是否有直播</label>
              <input type="radio" name="has_live" value="1" <?php if($has_live){ ?> checked="checked"<?php } ?> />
              是<br />
              <input type="radio" name="has_live" value="0" <?php if(!$has_live){ ?> checked="checked"<?php } ?> />
              否 
            </p>
            <p>
              <label>是否有点播</label>
              <input type="radio" name="has_vod" value="1" <?php if($has_vod){ ?> checked="checked"<?php } ?> />
              是<br />
              <input type="radio" name="has_vod" value="0" <?php if(!$has_vod){ ?> checked="checked"<?php } ?> />
              否 
            </p>            <p>
              <label>点播到期时间</label>
              <input class="text-input small-input" type="text" name="vod_expire" value="<?php echo $vod_expire;?>"/>
            </p>
            <p>
              <input type="hidden" name="a" value="<?php echo $a;?>" />
              <input type="hidden" name="appid" value="<?php echo $appid;?>" />
              <input class="button" type="submit" value="提交" />
            </p>
            </fieldset>
            <div class="clear"></div>
            <!-- End .clear -->
          </form>
        </div>
     </div>
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