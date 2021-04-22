<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Template{
	
	private $data = array();
	
	public function assign($name, $value) {

		if(!$trimmed = trim($name)) {
			throw new Exception("Variable name '$name' can't be empty");
		}
		
		$this->data[$trimmed] = $value;
		return true;

	}
		
	public function loadTemplate($template, $render_template = true) {

		$CI =& get_instance();
		$tpl = $CI->load->view($template, $this->data, true);
		
		if($render_template) print $tpl;
		else return $tpl;		

	}

    public static function instance() {

		static $instance;
		
		if(!($instance instanceof Template)) {
			$instance = new Template();
		} // if

		return $instance;
      
    }
	
}

if (!function_exists('tpl_assign')) {

	function tpl_assign($varname, $varvalue = null) {
	
		if(is_array($varname)) {
		
			$template_instance = Template::instance();
			foreach($varname as $k => $v) {
				$template_instance->assign($k, $v);
			}
		
		} else {
			Template::instance()->assign($varname, $varvalue);
		}
	
	}

}

if (!function_exists('tpl_fetch')) {

	function tpl_fetch($template) {
		return Template::instance()->loadTemplate($template, false);
	}

}
  
if (!function_exists('tpl_display')) {

	function tpl_display($template) {
		return Template::instance()->loadTemplate($template);
	}

}

