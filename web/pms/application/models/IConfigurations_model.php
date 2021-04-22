<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* IConfigurations Model */

class IConfigurations_model extends Application_model {

    function __construct() {
        parent::__construct('iconfigurations', 'IConfiguration', true, true);
    }
	
	public function getByCategory($category_name) {
		return $this->find(array('conditions' => array('`category_name` = ?', $category_name), 'order' => '`name`'));
	}
	
	public function getByName($name, $default = null) {
		return $this->find(array('conditions' => array('`name` = ?', $name), 'one' => true));
	}
	
}

/* IConfiguration Object */

class IConfiguration extends Application_object {

	function __construct() {
		parent::__construct('iconfigurations');
	}

	/* Static URLs */

	public function getEditURL() {
		return 'admin/settings/edit/'.$this->getId();
	}

}