<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>客户管理系统</title>
{css:reset}
{css:style}
{css:invalid}
{csshere}
{jshere}
</head>
<body id="login">
<div id="login-wrapper" class="png_bg">
  <div id="login-top">
    <h1>客户管理系统</h1>
    <!-- Logo (221px width) -->
    <a href="#"><img id="logo" src="{$RESOURCE_URL}logo.png" alt="Simpla Admin logo" /></a> 
    </div>
  <!-- End #logn-top -->
  <div id="login-content">
    <form action="login.php" method="post" name="login">
      <div class="notification information png_bg">
        <div>用户名：admin 密码：123</div>
      </div>
      <p>
        <label>用户名</label>
        <input class="text-input" type="text" name="username" />
      </p>
      <div class="clear"></div>
      <p>
        <label>密码</label>
        <input class="text-input" type="password" name="password" />
      </p>
      <div class="clear"></div>
      <p id="remember-password">
        <input type="checkbox" />
        记住我</p>
      <div class="clear"></div>
      <p>
      	<input type="hidden" name="a" value="dologin" />
        <input class="button" type="submit" name="submit" value="登陆" />
      </p>
    </form>
  </div>
  <!-- End #login-content -->
</div>
<!-- End #login-wrapper -->
</body>
</html>
