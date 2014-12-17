<?php

include APPPATH.'/libraries/HttpRequest.class.php';

class MY_Model extends CI_Model {

    private static $_instance;
    private $httprequest;

    public function __construct() {
        $this->httprequest = new HttpRequest('http://localhost:8080/php-test/CodeIgniter/api/index.php','api_key', 'api_secret');
	}
    private function __clone(){
        //Clone is not allowed
    }
    public static function getInstance() {
        if( ! (self::$_instance instanceof self) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function request($url='',$params=array(),$type=0) {
        if(empty($url)) return false;
        if(!$type) {
            return $this->httprequest->get($url,$params);
        } else {
            return $this->httprequest->post($url,$params);
        }
    }
}


?>

