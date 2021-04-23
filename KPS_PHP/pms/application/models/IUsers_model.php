<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* IUsers Model */

class IUsers_model extends Application_model {

    function __construct() {
        parent::__construct('iusers', 'IUser');
    }


	public function getPaginate($limit = null, $offset = null, $search = null) {
		
		$arguments = null;
		
		if (isset($search) && $search != "") {
			$arguments = array('conditions' => array("name LIKE '%".$search."%' OR email = '%".$search."%'"));
		}

		return $this->paginate($arguments, $limit, $offset);
	
	}	

	public function getByEmail($email) {
	
		return $this->find(array('conditions' => 
		array('email = ?', $email), 'one' => true));
	
	}

	public function getByToken($token) {
		
		return $this->find(array('conditions' => 
		array('token = ?', $token), 'one' => true));
		
	}

    public function tokenExists($token) {
		return  $this->count(array('token = ?', $token)) > 0;
    }
	
}

/* IUser Object */

class IUser extends Application_object {

	function __construct() {
		parent::__construct('iusers');
	}

	/* Auth methods */

    public function resetPassword($save = true) {

		$new_password = substr(sha1(uniqid(rand(), true)), rand(0, 25), 13);

		$this->setPassword($new_password);
		if($save) $this->save();

		return $new_password;

    }
	
    public function setPassword($value) {
     	
	 	if(trim($value) == '') {
			throw new Exception("The password field is required");
		}
		
		do {

			$salt = substr(sha1(uniqid(rand(), true)), rand(0, 25), 13);
			$token = sha1($salt . $value);

		} while($this->CI_instance()->IUsers->tokenExists($token));
		
		$this->setToken($token);
		$this->setSalt($salt);
		
	}
    
    public function getEncodedToken() {
		return $this->CI_instance()->encryption->encrypt($this->getToken());
    }
    
    public function isValidPassword($password) {
		return sha1($this->getSalt().$password) == $this->getToken();
    }
    
    public function isValidToken($encoded_token) {
		return $this->CI_instance()->encryption->decrypt($encoded_token) == $this->getToken();
	}
	
	function getAvatar(){

		$filename = ($this->getAvatarFile() != '') ? $this->getAvatarFile() : get_avatar_default($this->getName());
		return get_page_base_url('public/avatars/'.$filename);
	
	}
	
	/* Static URLs */

	public function getForgotPasswordURL() {
		return 'admin/access/forgot_password/'.$this->getToken();
	}

	public function getEditProfileURL() {
		return 'admin/dashboard/edit_profile';
	}

}