<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* IPackages Model */

class IWidgets_model extends Application_model {

    function __construct() {
        parent::__construct('iwidgets', 'IWidget');
	}
	
	public function getAll() {
		return $this->find(array('order' => 'id ASC'));
	}
	
}

/* IPackage Object */

class IWidget extends Application_object {

	function __construct() {
		parent::__construct('iwidgets');
	}
	
	/* Static URLs */
	public function getAddURL() {
		return 'admin/widgets/add';
	}

	public function getEditURL() {
		return 'admin/widgets/edit/'.$this->getId();
	}

	public function getDeleteUrl() {
		return 'admin/widgets/delete/'.$this->getId();
	}

	/* photo methods */
	function getPhotoUrl(){
		$filename = $this->getPhoto();
		return get_page_base_url('public/uploads/'.$filename);
	}

	function savePhotoFile($source, $save = false, $max_width = 512, $max_height = 512) {
    
	  if(!is_readable($source)) return false;
      
      do {
		$temp_file = APPPATH . '/cache/' . sha1(uniqid(rand(), true));
      } while(is_file($temp_file));
      
      try {
        $image = new SimpleGdImage($source);
        $thumb = $image->scale($max_width, $max_height, SimpleGdImage::BOUNDARY_DECREASE_ONLY, false);
        $thumb->saveAs($temp_file, IMAGETYPE_PNG);

		$public_filename = upload_file($temp_file, 'png');
		if($public_filename) {
			$this->setPhoto($public_filename);
			if($save) {
				$this->save();
			}
		}
        
        $result = true;
      
	  } catch(Exception $e) {
	  	  $result = false;
      }
      
      // Cleanup
      if(!$result && $public_filename) {
          delete_file($public_filename);
      }
	  
      @unlink($temp_file);
      
      return $result;
    }
    
    function deletePhoto() {
		$photo_file = $this->getPhoto();
		if($photo_file != ""){
			delete_file($photo_file);
			$this->setPhoto('');
		}
    }
    
}