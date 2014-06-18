{template:head}
<div class="content-box">
<ul style="width:500px;">
	<li>
		<p>操作系统：{code}echo PHP_OS; {/code}</p>
	</li>
	<li>
		<p>服务器信息：{code}echo $_SERVER['SERVER_SOFTWARE'];{/code} </p>
		<p>PHP版本： {code}echo PHP_VERSION;{/code}</p>
		<p>ZEND版本：{code}echo zend_version();{/code}</p>
		{code}
		$max_execution_time = get_cfg_var("max_execution_time");
		$memory_limit = get_cfg_var("memory_limit");
		{/code}
		{if $max_execution_time}
		<p>最大执行时间：{$max_execution_time}秒</p>
		{/if}
		{if $memory_limit}
		<p>占用最大内存：{$memory_limit}</p>
		{/if}
	</li>
</ul>
</div>
{template:foot}