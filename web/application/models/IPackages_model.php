<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* IPackages Model */

class IPackages_model extends Application_model {

    function __construct() {
        parent::__construct('ipackages', 'IPackage');
	}
	
	public function getAll() {
		return $this->find(array('order' => '`id` ASC'));
	}
	
}

/* IPackage Object */

class IPackage extends Application_object {

	function __construct() {
		parent::__construct('ipackages');
	}
	
	/* Static URLs */

	public function getEditURL() {
		return 'admin/packages/edit/'.$this->getId();
	}

}