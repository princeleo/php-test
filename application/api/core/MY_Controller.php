<?php defined('BASEPATH') OR exit('No direct script access allowed');
 
require APPPATH."libraries/REST_Controller.php";

class MY_Controller extends REST_Controller {

	/**
	 * 输出数据
	 *
	 * @var array
	 */
	var $_data = array();
	
	/**
	 * 是否使用缓存
	 *
	 * @var bool
	 */
	var $_cache = false;
	
	/**
	 * 默认缓存时间,单位秒
	 *
	 * @var int
	 */
	var $_cache_time = 60;
	
	/**
	 * 缓存键值标识前缀
	 *
	 * @var string
	 */
	var $_cache_key_prefix = '';
	
	public function __construct()
	{
		parent::__construct();
		

	}
}