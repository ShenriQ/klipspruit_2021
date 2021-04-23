<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader {
	
	public function model($model, $name = '', $db_conn = FALSE) {

		if (is_string($model) && $name == '') {

			$name = $model;
			$model = $model.'_model';

		}
		
		parent::model($model, $name, $db_conn);

	}

}