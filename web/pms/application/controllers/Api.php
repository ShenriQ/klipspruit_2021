<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once('./application/libraries/REST_Controller.php');

class Api extends REST_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->model('IPackages');
    }
    
    public function packages_get() {
        try {
            $data = [];
            $packages = $this->IPackages->find();
            if(isset($packages) && is_array($packages) && count($packages)) {
                foreach($packages as $package) {
                    $data[] = [
                        'id' => $package->getId(),
                        'name' => $package->getName(),
                        'price' => $package->getPricePerMonth(),
                        'storage' => $package->getMaxStorage(),
                        'users' => $package->getMaxUsers(),
                        'projects' => $package->getMaxProjects()
                    ];
                }
            }
            $response = ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            $response = ['success' => false];
        }
        $this->response($response);
    }
}

