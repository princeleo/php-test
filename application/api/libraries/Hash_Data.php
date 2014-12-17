<?php
/**
 * hash data
 *
 */
class Hash_Data
{
	var $ci;
	
	private $_section_component = null;
	
	private $_favo_section = null;
	
	private $_homepage_search_section = null;
	
	function Hash_Data()
	{
		$this->ci =& get_instance();
		$this->ci->config->load('tabledata',true);
		$this->_section_component = $this->ci->config->item('section_component', 'tabledata');
		$this->ci->config->load('pagedata',true);
		$this->_favo_section = $this->ci->config->item('favo_section', 'pagedata');
		$this->_homepage_search_section = $this->ci->config->item('homepage_search_section', 'pagedata');
	}
   
	/**
	 * 根据栏目组件ID,获取内容表名
	 * @param $id
	 * @return $tablename
	 */
	function get_tablename($id){
		$table_name = '';
		if(empty($id)) return $table_name;

		if(array_key_exists($id,$this->_section_component))
		{
			$table_name = $this->_section_component[$id]['table_name'];
		}

		return $table_name;
	}
	
	/**
	 * 根据栏目组件ID,获取组件名称
	 * @param $id
	 * @return $compname
	 */
	function get_compname($id){
		$comp_name = '';
		if(empty($id)) return $comp_name;

		if(array_key_exists($id,$this->_section_component))
		{
			$comp_name = $this->_section_component[$id]['comp_name'];
		}
		return $comp_name;
	}
	
	/**
	 * 根据栏目组件名称,获取组件ID
	 * @param $id
	 * @return $compname
	 */
	function get_compname_id($comp_name)
	{
		if(empty($comp_name)) 
		{
			return false;
		}
		
		$id = 0;
		
		foreach ($this->_section_component as $k=>$item)
		{
			if($item['comp_name']==$comp_name)
			{
				$id = $k;
				break;
			}
		}
		
		return $id;
	}
	    
    /**
     * 返回收藏夹栏目
     * @return array $favo_sec_data
     */
    function get_favo_section()
    {
    	$favo_sec_data = array();
    	if(is_array($this->_section_component))
    	{
    		foreach ($this->_section_component as $secpid=>$secps)
    		{
    			if(in_array($secps['comp_name'],$this->_favo_section))
    			{
    				$favo_sec_data[$secpid] = $secps;
    			} 
    		}
    	}
    	return $favo_sec_data;
    }
	
    /**
     * 返回可收藏栏目组件列表
     * @return array
     */
    function get_favo_available_component()
    {
    	return $this->_favo_section;
    }
	
    /**
     * 返回首页搜索栏目 
     * @return array 
     */
    function get_home_search_section()
    {
    	return $this->_homepage_search_section;
    }
    
    /**
     * 读取本次请求数据
     */
    function get_page_data()
    {
    	
    }
}