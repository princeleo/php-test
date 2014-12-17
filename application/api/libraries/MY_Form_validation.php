<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 扩展CI_Form_validation,增加对特定数据进行校验的功能
 *
 * @author       Wacosoft Dev Team
 * @email        sales@wacosoft.cn
 * @website      http://www.wacosoft.cn
 * @copyright    Copyright (c) 2004 - 2013, Wacosoft Technologies (Shenzhen) Co., Ltd.
 */
class MY_Form_validation extends CI_Form_validation
{
	var $CI;		
	public function __construct($rules = array())
	{
		parent::__construct($rules);
				
		log_message('debug', "MY_Form_validation successfully run");
	}
  
	
	/**
	 * 根据方法名获取rule规则,有返回true,反之fasle
	 * 
	 * @param  string 	$method 		方法名
	 *
	 * @return bool
	 */
	public function getRuleByMethod($method = '')
	{
		if(isset($this->_config_rules[$method.'/post']))
		{
			return true;
		}
		elseif (isset($this->_config_rules[$method.'/get']))
		{
			return true;
		}
		elseif (isset($this->_config_rules[$method]))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	/**
	 * 返回错误信息
	 *
	 * @return array()
	 */
	public function getErrors()
	{
		return $this->_error_array;
	}
	
	/**
	 * 
	 * 针对数据进行校验
	 * 
	 * @param array $data		需要校验的数据
	 * @param array $rule		校验规则
	 *
	 * @return	bool
	 */
	public function data_valid($rule = array(),$data = array())
	{
		/**
		 * 校验类型,0指定数据集,1$_REQUEST
		 * 当=1时,校验成功后需把数据reset到$_REQUEST中
		 */
		$valid_type = 0;

		//如果data为空,默认校验$_REQUEST
		if(empty($data))
		{
			$data = $_REQUEST;
			$valid_type = 1;
		}
			
		if(empty($rule))
		{
			return FALSE;
		}
		
		if(is_array($rule))
		{
			// 设置校验规则
			$this->set_rules_custom($rule);
		}
		else
		{
			if (count($this->_config_rules) == 0)
			{
				log_message('debug', "Unable to find config_rules");
				return FALSE;
			}
			
			$rule = strtolower($rule);
			if (isset($this->_config_rules[$rule.'/post'])&&$valid_type)
			{
				$data = $_POST;
				log_message('debug', "post rule find");
				
				$this->set_rules_custom($this->_config_rules[$rule.'/post']);
			}
			elseif(isset($this->_config_rules[$rule.'/get'])&&$valid_type)
			{
				$data = $_GET;
				log_message('debug', "get rule find");
				$this->set_rules_custom($this->_config_rules[$rule.'/get']);
			}
			elseif (isset($this->_config_rules[$rule]))
			{
				log_message('debug', "request rule find");
				$this->set_rules_custom($this->_config_rules[$rule]);
			}
			else 
			{
				return FALSE;
			}
		}

		if(!is_array($data)||empty($data))
		{
			log_message('error', "Unable to find validation data");
			return FALSE;
		}
	
		// 检查设置规则是否正确
		if (count($this->_field_data) == 0)
		{
			log_message('error', "Unable to find validation rules");
			return FALSE;
		}
		
		// Load the language file containing error messages
		$this->CI->lang->load('form_validation');

		// Cycle through the rules for each field, match the
		// corresponding $_POST item and test for errors
		foreach ($this->_field_data as $field => $row)
		{
			// Fetch the data from the corresponding $_POST array and cache it in the _field_data array.
			// Depending on whether the field name is an array or a string will determine where we get it from.
	
			if ($row['is_array'] == TRUE)
			{
				$this->_field_data[$field]['postdata'] = $this->_reduce_array($data, $row['keys']);
			}
			else
			{
				if (isset($data[$field]) AND $data[$field] != "")
				{
					$this->_field_data[$field]['postdata'] = $data[$field];
				}
			}
	
			$this->_execute($row, explode('|', $row['rules']), $this->_field_data[$field]['postdata']);
		}
	
		// Did we end up with any errors?
		$total_errors = count($this->_error_array);
				
		// No errors, validation passes!
		if ($total_errors == 0)
		{
			if($valid_type==1)
			{
				$this->_reset_request_array();
				return TRUE;
			}
			else 
			{
				return $this->_reset_data_array($data);
			}
		}
		
		return FALSE;
	}
	
	
	/**
	 * 重设REQUEST数据
	 *
	 * @access	private
	 * @return	null
	 */
	protected function _reset_request_array()
	{
		foreach ($this->_field_data as $field => $row)
		{
			if ( ! is_null($row['postdata']))
			{
				if ($row['is_array'] == FALSE)
				{
					if (isset($_POST[$row['field']]))
					{
						$_POST[$row['field']] = $this->prep_for_form($row['postdata']);
					}
					else 
					{
						$_GET[$row['field']] = $this->prep_for_form($row['postdata']);
					}
				}
				else
				{
					// start with a reference
					$post_ref 	=& $_POST;
					$get_ref 	=& $_GET; 
					$para_ref;
					
					$key = current($row['keys']);
					if (isset($_POST[$key]))
					{
						$para_ref =& $post_ref;
					}
					else 
					{
						$para_ref =& $get_ref;
					}

					// before we assign values, make a reference to the right POST key
					if (count($row['keys']) == 1)
					{
						$para_ref =& $para_ref[$key];
					}
					else
					{
						
						foreach ($row['keys'] as $val)
						{
							$para_ref =& $para_ref[$val];
						}
					}

					if (is_array($row['postdata']))
					{
						$array = array();
						foreach ($row['postdata'] as $k => $v)
						{
							$array[$k] = $this->prep_for_form($v);
						}

						$para_ref = $array;
					}
					else
					{
						$para_ref = $this->prep_for_form($row['postdata']);
					}
				}
			}
		}
	}
	
	/**
	 * 重设数组数据
	 *
	 * @access	private
	 * @return	null
	 */
	protected function _reset_data_array($data)
	{
		foreach ($this->_field_data as $field => $row)
		{
			if ( ! is_null($row['postdata']))
			{
				if ($row['is_array'] == FALSE)
				{
					$data[$row['field']] = $this->prep_for_form($row['postdata']);
				}
				else
				{
					// start with a reference
					$post_ref =& $data;

					// before we assign values, make a reference to the right POST key
					if (count($row['keys']) == 1)
					{
						$post_ref =& $post_ref[current($row['keys'])];
					}
					else
					{
						foreach ($row['keys'] as $val)
						{
							$post_ref =& $post_ref[$val];
						}
					}

					if (is_array($row['postdata']))
					{
						$array = array();
						foreach ($row['postdata'] as $k => $v)
						{
							$array[$k] = $this->prep_for_form($v);
						}

						$post_ref = $array;
					}
					else
					{
						$post_ref = $this->prep_for_form($row['postdata']);
					}
				}
			}
		}
		
		return $data;
	}  
	
	/**
	 * Set Rules
	 *
	 * This function takes an array of field names and validation
	 * rules as input, validates the info, and stores it
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	public function set_rules_custom($field, $label = '', $rules = '')
	{
		// If an array was passed via the first parameter instead of indidual string
		// values we cycle through it and recursively call this function.
		if (is_array($field))
		{
			foreach ($field as $row)
			{
				// Houston, we have a problem...
				if ( ! isset($row['field']) OR ! isset($row['rules']))
				{
					continue;
				}

				// If the field label wasn't passed we use the field name
				$label = ( ! isset($row['label'])) ? $row['field'] : $row['label'];

				// Here we go!
				$this->set_rules_custom($row['field'], $label, $row['rules']);
			}
			return $this;
		}

		// No fields? Nothing to do...
		if ( ! is_string($field) OR  ! is_string($rules) OR $field == '')
		{
			return $this;
		}

		// If the field label wasn't passed we use the field name
		$label = ($label == '') ? $field : $label;

		// Is the field name an array?  We test for the existence of a bracket "[" in
		// the field name to determine this.  If it is an array, we break it apart
		// into its components so that we can fetch the corresponding POST data later
		if (strpos($field, '[') !== FALSE AND preg_match_all('/\[(.*?)\]/', $field, $matches))
		{
			// Note: Due to a bug in current() that affects some versions
			// of PHP we can not pass function call directly into it
			$x = explode('[', $field);
			$indexes[] = current($x);

			for ($i = 0; $i < count($matches['0']); $i++)
			{
				if ($matches['1'][$i] != '')
				{
					$indexes[] = $matches['1'][$i];
				}
			}

			$is_array = TRUE;
		}
		else
		{
			$indexes	= array();
			$is_array	= FALSE;
		}

		// Build our master array
		$this->_field_data[$field] = array(
			'field'				=> $field,
			'label'				=> $label,
			'rules'				=> $rules,
			'is_array'			=> $is_array,
			'keys'				=> $indexes,
			'postdata'			=> NULL,
			'error'				=> ''
		);

		return $this;
	}
	
	/**
	 * 字段类型转换
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function convertype($str, $val)
	{
		switch ($val)
		{
			case 'string':
			case 'char':
			case 's':
			case 'c': //字符类型
				return (settype($str, 'string')) ?$this->CI->security->xss_clean(trim($str)):'';
			case 'float':
			case 'f':
			case 'double':
			case 'd': //浮点类型
				return (settype($str, 'float')) ? $str : 0.0;
			case 'int':
			case 'integer':
			case 'i': //整数类型
				return (settype($str, 'integer')) ? $str : 0;
			case 'bool':
			case 'boolean':
			case 'b': //布尔类型
				return (settype($str, 'boolean')) ? $str : false;
			case 'array':
			case 'a': //数组类型
				return (settype($str, 'array')) ? $str : array();
			case 'object':
			case 'o': //对象类型
				return (settype($str, 'object')) ? $str : null;
			case 'j':return stripslashes($str);
			default:
				return $str;
		}
	}
	
	/**
	 * 验证json字符串的合法性
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function is_json($str,$val='')
	{
		if(!empty($str))
		{
			if (get_magic_quotes_gpc())
			{      
				$str = stripslashes($str);
			}
			$list = json_decode($str,true);
			if(is_array($list))
			{
				//验证数据元素的合法性
				if(!empty($val))
				{
					$ret = $this->data_valid($val,$list);
					if($ret === FALSE)
					{
						return false;
					}
					else 
					{
						return $ret;
					}
				}
				else 
				{
					return $str;
				}
			}
			else 
			{
				return false;
			}
		}
		
		return false;
	}
	
	public function get_error_array()
	{
		return $this->_error_array;
	}
}