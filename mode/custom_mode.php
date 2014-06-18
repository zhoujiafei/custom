<?php
class custom_mode extends InitFrm 
{
    public function __construct() 
    {
        parent::__construct();
    }

    public function __destruct() 
    {
        parent::__destruct();
    }
    
    
	public function show($condition = '', $fields = '*') 
	{
        $sql = "SELECT " . $fields . " FROM " . DB_PREFIX . "authinfo  WHERE 1 " . $condition;
        $q = $this -> db -> query($sql);
        $info = array();
        while ($r = $this -> db -> fetch_array($q)) 
        {
        	$r['create_time'] = date('Y-m-d H:i:s',$r['create_time']);
        	$r['status_text'] = $this->settings['status'][$r['status']];
           	$info[] = $r;
        }
        return $info;
    }
    
	public function count($condition = '') 
 	{
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "authinfo WHERE 1 " . $condition;
        $total = $this -> db -> query_first($sql);
        return $total;
    }
    
    public function detail($id = '', $condition = '', $fields = '*') 
    {
        if (!$id && !$condition) 
        {
            return false;
        }

        $sql = "SELECT " . $fields . " FROM " . DB_PREFIX . "authinfo  WHERE 1 ";
        if ($id) 
        {
            $sql .= " AND appid = '" . $id . "'";
        }
        if ($condition) 
        {
            $sql .= ' ' . $condition;
        }
        $info = $this -> db -> query_first($sql);
        $info['expire_time'] = date('Y-m-d', $info['expire_time']);
        $info['create_time'] = date('Y-m-d', $info['create_time']);
        $info['update_time'] = date('Y-m-d', $info['update_time']);
        $info['gather_expire'] = date('Y-m-d', $info['gather_expire']);
        $info['codec_expire'] = date('Y-m-d', $info['codec_expire']);
        $info['record_expire'] = date('Y-m-d', $info['record_expire']);
        $info['vod_expire'] = date('Y-m-d', $info['vod_expire']);
        $info['channel_expire'] = date('Y-m-d', $info['channel_expire']);
        return $info;
    }
    
	public function create($data = array())
	{
		if(!$data)
		{
			return false;
		}
		
		$sql = " INSERT INTO " . DB_PREFIX . "authinfo SET ";
		foreach ($data AS $k => $v)
		{
			$sql .= " {$k} = '{$v}',";
		}
		$sql = trim($sql,',');
		$this->db->query($sql);
		$vid = $this->db->insert_id();
		$sql = " UPDATE ".DB_PREFIX."authinfo SET order_id = {$vid}  WHERE appid = {$vid}";
		$this->db->query($sql);
		return $vid;
	}
	
	public function update($id,$data = array())
	{
		if(!$data || !$id)
		{
			return false;
		}
		//查询出原来
		$sql = " SELECT * FROM " .DB_PREFIX. "authinfo WHERE appid = '" .$id. "'";
		$pre_data = $this->db->query_first($sql);
		if(!$pre_data)
		{
			return false;
		}
		
		//更新数据
		$sql = " UPDATE " . DB_PREFIX . "authinfo SET ";
		foreach ($data AS $k => $v)
		{
			$sql .= " {$k} = '{$v}',";
		}
		$sql  = trim($sql,',');
		$sql .= " WHERE appid = '"  .$id. "'";
		$this->db->query($sql);
		return $pre_data;
	}
	
	public function delete($id = '')
	{
		if(!$id)
		{
			return false;
		}
		//查询出原来
		$sql = " SELECT * FROM " .DB_PREFIX. "authinfo WHERE appid IN (" . $id . ")";
		$q = $this->db->query($sql);
		$pre_data = array();
		while ($r = $this->db->fetch_array($q))
		{
			$pre_data[] 	= $r;
		}
		if(!$pre_data)
		{
			return false;
		}
		//删除主表
		$sql = " DELETE FROM " .DB_PREFIX. "authinfo WHERE appid IN (" . $id . ")";
		$this->db->query($sql);
		return $pre_data;
	}
	
	public function audit($id = '')
	{
		if(!$id)
		{
			return false;
		}
		//查询出原来
		$sql = " SELECT * FROM " .DB_PREFIX. "authinfo WHERE appid = '" .$id. "'";
		$pre_data = $this->db->query_first($sql);
		if(!$pre_data)
		{
			return false;
		}
		
		/**********************************以下状态只是示例，根据情况而定************************************/
		switch (intval($pre_data['status']))
		{
			case 1:$status = 2;break;//审核
			case 2:$status = 3;break;//打回
			case 3:$status = 2;break;//审核
		}
		
		$sql = " UPDATE " .DB_PREFIX. "authinfo SET status = '" .$status. "' WHERE appid = '" .$id. "'";
		$this->db->query($sql);
		return array('status' => $status,'appid' => $id,'status_text' => $this->settings['status'][$status]);
	}
}
?>