<?php

include APPPATH.'/libraries/HttpClient.class.php';

class HttpRequest {

	public $api_server;
	public $api_key;
	public $api_secret;

	public $format = 'json';

	private static $_instance;
	
	
	//construct is ok	
	function __construct($api_server='', $api_key='', $api_secret='') {	
		if(!empty($api_server)) $this->api_server = $api_server;
		if(!empty($api_key)) $this->api_key = $api_key;
		if(!empty($api_secret)) $this->api_secret = $api_secret;	
	}
	
	//singleton instance is ok
	public static function instance($api_server='', $api_key='', $api_secret='') {
		if(!(self::$_instance instanceof self)) {
                self::$_instance = new self($api_server, $api_key, $api_secret);
        }
        return self::$_instance;
	}
	
	public function http($url, $params=array()) {
		//wait for a moment
	}
	
	public function get($url='', $params=array()) {
		$data = $this->request($url, 'GET', $params);

		if($this->format == 'json') return json_decode($data, true); 

		return $data;
	}
	
	public function post($url='', $params=array()) {
		$data = $this->request($url, 'POST', $params);

		if($this->format == 'json') return json_decode($data, true); 

		return $data;
	}
	
	private function request($url, $method, $params=array(), $multi=false) {
		$response = '';

		if(empty($url) || empty($params))
			return '{"code":0, "msg":"url or params is null"}';

        $params['api_key'] = $this->api_key;
		$params['timestamp'] = $this->timestamp();
        $params['token'] = $this->token($params);
		$params['format'] = $this->format;
		switch($method){
			case 'GET':
                if(!isset($params['json_encode_option'])) {
                    $url = $this->api_server . "/$url?" . http_build_query($params);
                } else {
                    $option = $params['json_encode_option'];
                    unset($params['json_encode_option']);
                    $url = $this->api_server . "/$url?" . http_build_query($params) . '&option=' . $option;
                }
				$response = HttpClient::quickGet($url);	
				break;
			default:
				$url = $this->api_server . "/$url";
				//echo "url: $url \r\n";
				$response = HttpClient::quickPost($url, $params);
		}
		
		return $response->getContent();	
	}

	/**
	 *首先, 对(非空)参数(除token外)按名称升序进行拼接(如a=1&b=2&c=3)
	 *然后, 在拼接的字符串最后拼接上api_secret(如a=1&b=2&c=3api_secret) 
     */		
	private function token($params) {
		//ksort
        unset($params['token']);
        unset($params['api_key']);
        unset($params['format']);
		ksort($params);
		$token = http_build_query($params) . $this->api_secret;

		return md5($token);
	}
	
	public function timestamp() {
//        list($usec, $sec) = explode(" ", microtime());
        
		//return $sec.((int)($this->microtime_float() * 1000));
		return time();
    }
}


?>
