<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* Configurations Model */

class Configurations_model extends Application_model {

    function __construct() {
        parent::__construct('configurations', 'Configuration');
    }
	
	public function getByCategory($category_name) {
		return $this->find(array('conditions' => array('category_name = ?', $category_name), 'order' => 'name'));
	}
	
	public function getByName($name, $default = null) {
		return $this->find(array('conditions' => array('name = ?', $name), 'one' => true));
	}
	
}

/* Configuration Object */

class Configuration extends Application_object {

	function __construct() {
		parent::__construct('configurations');
	}

	/* Static URLs */

	public function getEditURL() {
		return 'settings/edit/'.$this->getId();
	}

}