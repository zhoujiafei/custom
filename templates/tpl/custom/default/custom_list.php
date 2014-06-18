{template:head}
<script type="text/javascript">
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
                {$pagelink}
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
               {if !$list}
               	<p>内容为空</p>
               {else}
               		{foreach $list AS $k => $v}
		              <tr>
		                <td>
		                  <input type="checkbox" name="line_{$v['appid']}" value="{$v['appid']}" />
		                </td>
		                <td>{$v['custom_name']}</td>
		                <td>{$v['bundle_id']}</td>
		                <td id="_status_{$v['appid']}" onclick="change_status(this);"  _appid="{$v['appid']}" style="cursor:pointer;color:blue;">{$v['status_text']}</td>
		                <td>{$v['appkey']}</td>
		                <td>{$v['create_time']}</td>
		                <td>
		                  <!-- Icons -->
		                  <a href="custom.php?a=detail&appid={$v['appid']}" title="Edit"><img src="{$RESOURCE_URL}icons/pencil.png" alt="Edit" /></a> 
		                  <a href="custom.php?a=delete&appid={$v['appid']}" title="Delete"><img src="{$RESOURCE_URL}icons/cross.png" alt="Delete" />
		              </tr>
              		{/foreach}
              {/if}
            </tbody>
          </table>
        </div>
      </div>
      <!-- End .content-box-content -->
    </div>
{template:foot}