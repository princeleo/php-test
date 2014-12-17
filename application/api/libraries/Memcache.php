<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006 - 2011 EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource	
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter memcache Caching Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link		
 */

class CI_MEMCACHE {

	/**
	 * 关键字前缀 
	 */
	var $_pre = 'data-interface/';
	
	/**
	 * 关键字
	 */
	var $_key = '';
	/**
	 * 默认连接配置
	 */
	protected $_memcache_conf 	= array(
					'default' => array(
						'default_host'		=> '127.0.0.1',
						'default_port'		=> 11211,
						'default_weight'	=> 1
					)
				);
	/**
	 * 实例化对象
	 */			
	var $_memcache = null;
				
	/**
	 * Constructor - Sets Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($config = array())
	{
		$CI =& get_instance();
		$CI->config->load('memcache',true);
		if (count($CI->config->item('memcache','memcache')) > 0)
		{
			$this->initialize($config);
			$this->_pre = $CI->config->item('prekey','memcache');
		}

		$this->_memcache = new memcache();

		foreach ($this->_memcache_conf as $name => $cache_server)
		{
			if ( ! array_key_exists('hostname', $cache_server))
			{
				$cache_server['hostname'] = $this->_default_options['default_host'];
			}
	
			if ( ! array_key_exists('port', $cache_server))
			{
				$cache_server['port'] = $this->_default_options['default_port'];
			}
	
			if ( ! array_key_exists('weight', $cache_server))
			{
				$cache_server['weight'] = $this->_default_options['default_weight'];
			}
			//print_r($cache_server);exit;
			$this->_memcache->addServer(
					$cache_server['hostname'], $cache_server['port'], $cache_server['weight']
			);
			//$this->_memcache->connect($cache_server['hostname'], $cache_server['port'], $cache_server['weight']);
            
		}
		log_message('debug', "memcache Class Initialized");
	}
	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function initialize($config = array())
	{
		$this->_memcache_conf = NULL;
		
		foreach ($config['memcache'] as $name => $conf)
		{
			$this->_memcache_conf[$name] = $conf;
		}
	}
	// ------------------------------------------------------------------------	

	/**
	 * 关键值
	 */
	public function setKey($key)
	{
		$this->_key = $this->_pre.$key;
	}
	/**
	 * 获取关键字值
	 */
	public function getKey()
	{
		return $this->_key;
	}
	/**
	 * 生成关键字
	 */
	public function makeKey($key)
	{
		$this->setKey($key);
		return $this->_key;
	}
	/**
	 * 获取缓存
	 *
	 * @param 	key		缓存关键字
	 * @return 	mixed		data on success/false on failure
	 */	
	public function get($key)
	{	
		$data = $this->_memcache->get($key);

		return (is_array($data)) ? $data[0] : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * 写入缓存
	 *
	 * @param 	key		关键字
	 * @param 	data		需要写入的缓存
	 * @param 	ttl			缓存失效时间(默认为60秒)
	 */
	public function set($key, $data = array(), $ttl = 60)
	{
		return $this->_memcache->set($key, array($data),MEMCACHE_COMPRESSED, $ttl);
	}

	/**更新缓存
	 *
	 * @param 	key		关键字
	 * @param 	data		需要写入的缓存
	 * @param 	ttl			缓存失效时间(默认为60秒)
	 */
	public function replace($key, $data = array(), $ttl = 60)
	{
		return $this->_memcache->replace($key, array($data), $ttl);
	}

	// ------------------------------------------------------------------------
	
	/**
	 * 删除缓存
	 *
	 * @param 	key		关键字
	 */
	public function delete($key)
	{
		return $this->_memcache->delete($key);
	}

	// ------------------------------------------------------------------------
	
	/**
	 * 清除缓存
	 *
	 * @return 	boolean		false on failure/true on success
	 */
	public function clean()
	{
		return $this->_memcache->flush();
	}

	// ------------------------------------------------------------------------
	/**
	 * 关闭到memcached服务端的连接
	 */
	public function close()
	{
		return $this->_memcache->close();
	}
}
// End Class

/* End of file Memcache.php */
/* Location: ./system/libraries/Memcache.php */