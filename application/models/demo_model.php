<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Demo_model extends MY_Model {
    public $_catid;
    protected $_columnid = null;
    protected $_typeid = null;

    function __construct() {
        parent::__construct();
    }
   public function get_list(){
        return $this->request('?a=2',array('type'=>'test'),'');
   }
}
?>