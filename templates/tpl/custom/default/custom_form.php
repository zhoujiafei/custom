{template:head}
{if is_array($formdata) && $a == 'update'}
	{foreach $formdata as $key => $value}
		{code}
			$$key = $value;			
		{/code}
	{/foreach}
{/if}
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
              <input class="text-input small-input" type="text"  name="custom_name" value="{$custom_name}"/>
            </p>
            
            <p>
              <label>客户项目所在域名</label>
              <input class="text-input small-input" type="text" name="domain" value="{$domain}" />
            </p>
            
            <p>
              <label>客户标识</label>
              <input class="text-input small-input" type="text" name="bundle_id" value="{$bundle_id}"/>
              <span class="input-notification attention png_bg">标识不能有重复</span>
            </p>
            
            <p>
              <label>客户描述</label>
              <textarea class="text-input textarea wysiwyg" name="custom_desc" cols="79" rows="15">{$custom_desc}</textarea>
            </p>
            
            <p>
              <label>授权到期时间</label>
              <input class="text-input small-input" type="text" name="expire_time" value="{$expire_time}"/>
            </p>
            
            <p>
              <label>客户简称</label>
              <input class="text-input small-input" type="text" name="display_name" value="{$display_name}" />
            </p>
            
           	<p>
              <label>是否有授权</label>
              <input type="radio" name="is_auth" value="1" {if $is_auth} checked="checked"{/if}/>
              是<br />
              <input type="radio" name="is_auth" value="0" {if !$is_auth} checked="checked"{/if}/>
              否 
            </p>
            
            <p>
              <label>安装类型</label>
              <select name="install_type" class="small-input">
                {foreach $_configs['install_type'] AS $_key => $_value}
                <option value="{$_key}" {if $_key == $install_type}selected{/if}>{$_value}</option>
                {/foreach}
              </select>
            </p>
            
            <p>
              <label>应用限制</label>
              <input class="text-input small-input" type="text" name="app_limit" value="{$app_limit}" />
            </p>
            
            <p>
              <label>源码类型</label>
              <select name="source" class="small-input">
              	{foreach $_configs['source'] AS $_key => $_value}
                <option value="{$_key}"  {if $_key == $source}selected{/if}>{$_value}</option>
                {/foreach}
              </select>
            </p>
            
            <p>
              <label>采集授权时间</label>
              <input class="text-input small-input" type="text" name="gather_expire" value="{$gather_expire}"/>
            </p>
            
            <p>
              <label>授权提示方式</label>
              <input class="text-input small-input" type="text" name="tip_way"  value="{$tip_way}" />
            </p>
            
            <p>
              <label>提示信息显示时长</label>
              <input class="text-input small-input" type="text" name="tip_dur"  value="{$tip_dur}" />
            </p>
            
            <p>
              <label>提示内容</label>
              <input class="text-input small-input" type="text" name="tip_text"   value="{$tip_text}" />
            </p>
            <p>
              <label>转码到期时间</label>
              <input class="text-input small-input" type="text" name="codec_expire"   value="{$codec_expire}" />
            </p>
            <p>
              <label>收录到期时间</label>
              <input class="text-input small-input" type="text" name="record_expire"   value="{$record_expire}" />
            </p>
            <p>
              <label>允许创建的频道数</label>
              <input class="text-input small-input" type="text" name="channel_num"   value="{$channel_num}" />
            </p>
            
            <p>
              <label>频道功能到期时间</label>
              <input class="text-input small-input" type="text" name="channel_expire"  value="{$channel_expire}" />
            </p>
            
            <p>
              <label>是否有转码</label>
              <input type="radio" name="has_codec" value="1" {if $has_codec} checked="checked"{/if} />
              是<br />
              <input type="radio" name="has_codec" value="0" {if !$has_codec} checked="checked"{/if} />
              否 
            </p>
            
            <p>
              <label>是否有收录</label>
              <input type="radio" name="has_record" value="1" {if $has_record} checked="checked"{/if} />
              是<br />
              <input type="radio" name="has_record" value="0" {if !$has_record} checked="checked"{/if} />
              否 
            </p>
            
            <p>
              <label>是否有直播</label>
              <input type="radio" name="has_live" value="1" {if $has_live} checked="checked"{/if} />
              是<br />
              <input type="radio" name="has_live" value="0" {if !$has_live} checked="checked"{/if} />
              否 
            </p>
            
            <p>
              <label>是否有点播</label>
              <input type="radio" name="has_vod" value="1" {if $has_vod} checked="checked"{/if} />
              是<br />
              <input type="radio" name="has_vod" value="0" {if !$has_vod} checked="checked"{/if} />
              否 
            </p>

            <p>
              <label>点播到期时间</label>
              <input class="text-input small-input" type="text" name="vod_expire" value="{$vod_expire}"/>
            </p>
            
            <p>
              <input type="hidden" name="a" value="{$a}" />
              <input type="hidden" name="appid" value="{$appid}" />
              <input class="button" type="submit" value="提交" />
            </p>
            </fieldset>
            <div class="clear"></div>
            <!-- End .clear -->
          </form>
        </div>
     </div>
 </div>
 {template:foot}