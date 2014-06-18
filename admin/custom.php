<?php
define('SCRIPT_NAME', 'custom');
require_once('global.php');
require_once(ROOT_PATH . 'mode/custom_mode.php');
class custom extends uiBaseFrm
{
    private $mode;
    public function __construct()
    {
        parent::__construct();
        $this->mode = new custom_mode();
    }
    
    public function __destruct()
    {
        parent::__destruct();
    }

	public function show()
	{
		$page = $this->input['page'] ? $this->input['page'] : 1;			
		$count = $this->input['perPage'] ? intval($this->input['perPage']) : 10;
        $offset = ($page - 1) * $count;
		$condition = $this->get_condition();
		$orderby = '  ORDER BY order_id DESC,appid DESC ';
		$limit = ' LIMIT ' . $offset . ' , ' . $count;
		$ret = $this->mode->show($condition . $orderby . $limit);
        $total = $this->mode->count($condition);
        $pageinfo = array(
            'totalNum' => $total['total'],
            'perPage' => $count,
            'currPage' => floor($offset/$count) + 1,
        );
        $pagelink = build_page_link($pageinfo);
        $this->tpl->addVar('list', $ret);
        $this->tpl->addVar('pagelink', $pagelink);
        $this->tpl->outTemplate('custom_list');
	}

	public function count()
	{
		$condition = $this->get_condition();
		$info = $this->mode->count($condition);
		echo json_encode($info);
	}
	
	public function get_condition()
	{
		$condition = '';
		return $condition;
	}
	
	public function detail()
	{
		$ret = array();
		if($this->input['appid'])
		{
			$ret = $this->mode->detail($this->input['appid']);
			$this->tpl->addVar('a', 'update');
		}
		else 
		{
			$this->tpl->addVar('a', 'create');
		}
		
		$this->tpl->addVar('formdata', $ret);
		$this->tpl->outTemplate('custom_form');
	}
	
	public function create()
	{
		if(!$this->input['custom_name'])
		{
			$this->errorOutput(NO_CUSTOM_NAME);
		}
		
		//随机生成appkey
		$appkey = $this->create_appkey();
		$data = array(
			'appkey' 			=> $appkey,
			'custom_name' 		=> $this->input['custom_name'],
			'domain' 			=> $this->input['domain'],
			'bundle_id' 		=> $this->input['bundle_id'],
			'custom_desc' 		=> $this->input['custom_desc'],
			'expire_time' 		=> strtotime($this->input['expire_time']),
			'display_name' 		=> $this->input['display_name'],
			'is_auth' 			=> intval($this->input['is_auth']),
			'install_type' 		=> $this->input['install_type'],
			'app_limit' 		=> $this->input['app_limit'],
			'source' 			=> $this->input['source'],
			'gather_expire' 	=> strtotime($this->input['gather_expire']),
			'tip_way' 			=> $this->input['tip_way'],
			'tip_dur' 			=> $this->input['tip_dur'],
			'tip_text' 			=> $this->input['tip_text'],
			'codec_expire' 		=> strtotime($this->input['codec_expire']),
			'record_expire' 	=> strtotime($this->input['record_expire']),
			'channel_num' 		=> $this->input['channel_num'],
			'channel_expire' 	=> strtotime($this->input['channel_expire']),
			'has_codec' 		=> intval($this->input['has_codec']),
			'has_record' 		=> intval($this->input['has_record']),
			'has_live' 			=> intval($this->input['has_live']),
			'has_vod' 			=> intval($this->input['has_vod']),
			'vod_expire' 		=> strtotime($this->input['vod_expire']),
			'create_time' 		=> TIMENOW,
			'update_time' 		=> TIMENOW,
		);
		
		$vid = $this->mode->create($data);
		if($vid)
		{
			header('Location:custom.php');
		}
	}
	
	public function update()
	{
		if(!$this->input['appid'])
		{
			$this->errorOutput(NOID);
		}
		
		$update_data = array(
			'custom_name' 		=> $this->input['custom_name'],
			'domain' 			=> $this->input['domain'],
			'bundle_id' 		=> $this->input['bundle_id'],
			'custom_desc' 		=> $this->input['custom_desc'],
			'expire_time' 		=> strtotime($this->input['expire_time']),
			'display_name' 		=> $this->input['display_name'],
			'is_auth' 			=> intval($this->input['is_auth']),
			'install_type' 		=> $this->input['install_type'],
			'app_limit' 		=> $this->input['app_limit'],
			'source' 			=> $this->input['source'],
			'gather_expire' 	=> strtotime($this->input['gather_expire']),
			'tip_way' 			=> $this->input['tip_way'],
			'tip_dur' 			=> $this->input['tip_dur'],
			'tip_text' 			=> $this->input['tip_text'],
			'codec_expire' 		=> strtotime($this->input['codec_expire']),
			'record_expire' 	=> strtotime($this->input['record_expire']),
			'channel_num' 		=> $this->input['channel_num'],
			'channel_expire' 	=> strtotime($this->input['channel_expire']),
			'has_codec' 		=> intval($this->input['has_codec']),
			'has_record' 		=> intval($this->input['has_record']),
			'has_live' 			=> intval($this->input['has_live']),
			'has_vod' 			=> intval($this->input['has_vod']),
			'vod_expire' 		=> strtotime($this->input['vod_expire']),
			'update_time' 		=> TIMENOW,
		);
		$ret = $this->mode->update($this->input['appid'],$update_data);
		if($ret)
		{
			header('Location:custom.php');
		}
	}
	
	public function delete()
	{
		if(!$this->input['appid'])
		{
			$this->errorOutput(NOID);
		}
		
		$ret = $this->mode->delete($this->input['appid']);
		if($ret)
		{
			header('Location:custom.php');
		}
	}
	
	public function audit()
	{
		if(!$this->input['appid'])
		{
			$this->errorOutput(NOID);
		}
		$ret = $this->mode->audit($this->input['appid']);
		if($ret)
		{
			$this->addItem($ret);
			$this->output();
		}
	}
	
	//创建密钥
	private function create_appkey()
	{
		//生成appkey
		$keys = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$appkey = '';
		for($i = 0;$i<32;$i++)
		{
			$n = rand(0,61);
			$appkey .= $keys[$n];
		}
		return $appkey;
	}
}

include (ROOT_PATH . 'lib/exec.php');

?>