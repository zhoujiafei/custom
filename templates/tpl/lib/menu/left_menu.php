  <div id="sidebar">
    <div id="sidebar-wrapper">
      <!-- Sidebar with logo and menu -->
      <h1 id="sidebar-title"><a href="#">客户管理系统</a></h1>
      <a href="#"><img id="logo" src="{$RESOURCE_URL}logo.png" alt="客户管理系统" /></a>
      <!-- Sidebar Profile links -->
      <div id="profile-links"> 你好, <a href="#" title="Edit your profile">{$_user['username']}</a> | <a href="login.php?a=logout" title="退出">退出</a> </div>
      <ul id="main-nav">
        {foreach $_menus AS $_k => $_v}
        	<li> 
	        	<a href="{$_v['link']}" class="nav-top-item">{$_v['zh_name']}</a>
	        	{if $_v['child']}
	        		<ul>
	        		{foreach $_v['child'] AS $_kk => $_vv}
	        			<li><a href="{$_vv['link']}">{$_vv['zh_name']}</a></li>
	        		{/foreach}
	        		</ul>
	        	{/if}
        	</li>
        {/foreach}
      </ul>
    </div>
  </div>