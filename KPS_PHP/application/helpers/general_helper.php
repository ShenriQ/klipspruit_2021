<?php defined('BASEPATH') OR exit('No direct script access allowed');

function serialize_data($input){
    return base64_encode(serialize($input));
}

function unserialize_data($input){
    return unserialize(base64_decode($input));
}

function clean_field($val) {
	return str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $val);
}

function get_page_base_url($url) {
	return base_url().$url;
}

function get_target_source_row() {

	$CI =& get_instance();
	
	$target_source_id = (int) $CI->session->userdata('target_source_id');
	// $query = $CI->db->query("SELECT * FROM target_sources WHERE id = '".$target_source_id."' ");
	// mssql
	$query = $CI->db->query("SELECT * FROM target_sources WHERE id = ".$target_source_id." ");
	return $query->row();	
	
}

function is_valid_email($text) {
	return preg_match("/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i", trim($text));
}

function get_target_source_id() {

	$row = get_target_source_row();
	return (isset($row) && $row->is_active == 1 ? $row->id : 0);

}

function get_trash_action_url($object, $action, $with_base = true) {
	
	if(($object instanceof Application_object) && $object->isTrashable()) {
		
		$trash_url = 'trash/'.$action.'/'.$object->getTypeName().'/'.$object->getId();
		return $with_base ? get_page_base_url($trash_url) : $trash_url;
		
	}

}

function render_dynamic_form_element(LeadFormElement $form_element, $element_value = null) {
	
	switch($form_element->getFieldCategory()) {
		
		case 1 :
				
			echo '<div class="form-group">
					<label class="col-md-4">'.$form_element->getFieldName().($form_element->getIsRequired() ? ' *' : '').'</label>
					<div class="col-md-8">
						<input class="form-control" name="fd_id_'.$form_element->getid().'" value="'.clean_field($element_value).'" type="text">
						<span class="help-text">'.$form_element->getHelpText().'</span>
					</div>
				</div>';

			break;

		case 2 :
				
			echo '<div class="form-group">
					<label class="col-md-4">'.$form_element->getFieldName().($form_element->getIsRequired() ? ' *' : '').'</label>
					<div class="col-md-8">
						<textarea class="form-control" name="fd_id_'.$form_element->getid().'">'.clean_field($element_value).'</textarea>
						<span class="help-text">'.$form_element->getHelpText().'</span>
					</div>
				</div>';
			
			break;
			
		case 3 :
				
			echo '<div class="form-group">
					<label class="col-md-4">'.$form_element->getFieldName().($form_element->getIsRequired() ? ' *' : '').'</label>
					<div class="col-md-8">';

				$field_values = explode(",", $form_element->getFieldData());
				if(isset($field_values) && is_array($field_values) && count($field_values)) {

					$option_counter_id = 0; 
					foreach($field_values as $field_value) {
						echo '<div class="form-group"><label><input name="fd_id_'.$form_element->getid().'_'.$option_counter_id.'" value="1" type="checkbox"'.($element_value == $field_value ? ' checked="checked"' : '').'> '.clean_field($field_value).'</label></div>';
						$option_counter_id++;
					}
				
				}
				
				echo '<span class="help-text">'.$form_element->getHelpText().'</span>
					</div>
				</div>';

			break;

		case 4 :
				
			echo '<div class="form-group">
					<label class="col-md-4">'.$form_element->getFieldName().($form_element->getIsRequired() ? ' *' : '').'</label>
					<div class="col-md-8">';

				$field_values = explode(",", $form_element->getFieldData());
				if(isset($field_values) && is_array($field_values) && count($field_values)) {

					$option_counter_id = 0; 
					foreach($field_values as $field_value) {
						echo '<div class="form-group"><label><input name="fd_id_'.$form_element->getid().'" value="'.$option_counter_id.'" type="radio"'.($element_value == $field_value ? ' checked="checked"' : '').'> '.clean_field($field_value).'</label></div>';
						$option_counter_id++;
					}
				
				}
				
				echo '<span class="help-text">'.$form_element->getHelpText().'</span>
					</div>
				</div>';

			break;

		case 5 :
				
			echo '<div class="form-group">
					<label class="col-md-4">'.$form_element->getFieldName().($form_element->getIsRequired() ? ' *' : '').'</label>
					<div class="col-md-8"><select name="fd_id_'.$form_element->getid().'" class="form-control">';

				$field_values = explode(",", $form_element->getFieldData());
				if(isset($field_values) && is_array($field_values) && count($field_values)) {

					$option_counter_id = 0; 
					foreach($field_values as $field_value) {
						echo '<option value="'.$option_counter_id.'"'.($element_value == $field_value ? ' selected="selected"' : '').'>'.clean_field($field_value).'</option>';
						$option_counter_id++;						
					}
					
				}
				
				echo '</select><span class="help-text">'.$form_element->getHelpText().'</span>
					</div>
				</div>';

			break;
		
	}
	
}


function config_option($option, $default = null) {

	$CI =& get_instance();
	$option = $CI->Configurations->getByName($option, $default);

	return $option instanceof Application_object ? $option->getValue() : $default;

}

function i_config_option($option, $default = null) {

	$CI =& get_instance();
	$option = $CI->IConfigurations->getByName($option, $default);

	return $option instanceof Application_object ? $option->getValue() : $default;

}

function get_packages($simple_options = true) {
	$CI =& get_instance();
	$ipackages = $CI->IPackages->getAll();
	if(!$simple_options) {
		return $ipackages;
	} else {
		$packages = array();
		foreach($ipackages as $ipackage) {
			$packages[$ipackage->getId()] = $ipackage->getName();
		}
		return $packages;	
	}
} 

function get_site_theme() {

	$CI =& get_instance();
	return $CI->session->userdata('site_theme');
	
}

function get_site_language($is_ucfirst = false) {

	$CI =& get_instance();
	$language = $CI->session->userdata('site_lang');

	return $is_ucfirst ? ucfirst($language) : $language;
	
}

function get_site_language_shortcode() {
	return get_locale_code(get_site_language(true));
}

function get_locale_code($name){
    
	$languageCodes = array(
    "aa" => "Afar",
    "ab" => "Abkhazian",
    "ae" => "Avestan",
    "af" => "Afrikaans",
    "ak" => "Akan",
    "am" => "Amharic",
    "an" => "Aragonese",
    "ar" => "Arabic",
    "as" => "Assamese",
    "av" => "Avaric",
    "ay" => "Aymara",
    "az" => "Azerbaijani",
    "ba" => "Bashkir",
    "be" => "Belarusian",
    "bg" => "Bulgarian",
    "bh" => "Bihari",
    "bi" => "Bislama",
    "bm" => "Bambara",
    "bn" => "Bengali",
    "bo" => "Tibetan",
    "br" => "Breton",
    "bs" => "Bosnian",
    "ca" => "Catalan",
    "ce" => "Chechen",
    "ch" => "Chamorro",
    "co" => "Corsican",
    "cr" => "Cree",
    "cs" => "Czech",
    "cu" => "Church Slavic",
    "cv" => "Chuvash",
    "cy" => "Welsh",
    "da" => "Danish",
    "de" => "German",
    "dv" => "Divehi",
    "dz" => "Dzongkha",
    "ee" => "Ewe",
    "el" => "Greek",
    "en" => "English",
    "eo" => "Esperanto",
    "es" => "Spanish",
    "et" => "Estonian",
    "eu" => "Basque",
    "fa" => "Persian",
    "ff" => "Fulah",
    "fi" => "Finnish",
    "fj" => "Fijian",
    "fo" => "Faroese",
    "fr" => "French",
    "fy" => "Western Frisian",
    "ga" => "Irish",
    "gd" => "Scottish Gaelic",
    "gl" => "Galician",
    "gn" => "Guarani",
    "gu" => "Gujarati",
    "gv" => "Manx",
    "ha" => "Hausa",
    "he" => "Hebrew",
    "hi" => "Hindi",
    "ho" => "Hiri Motu",
    "hr" => "Croatian",
    "ht" => "Haitian",
    "hu" => "Hungarian",
    "hy" => "Armenian",
    "hz" => "Herero",
    "ia" => "Interlingua (International Auxiliary Language Association)",
    "id" => "Indonesian",
    "ie" => "Interlingue",
    "ig" => "Igbo",
    "ii" => "Sichuan Yi",
    "ik" => "Inupiaq",
    "io" => "Ido",
    "is" => "Icelandic",
    "it" => "Italian",
    "iu" => "Inuktitut",
    "ja" => "Japanese",
    "jv" => "Javanese",
    "ka" => "Georgian",
    "kg" => "Kongo",
    "ki" => "Kikuyu",
    "kj" => "Kwanyama",
    "kk" => "Kazakh",
    "kl" => "Kalaallisut",
    "km" => "Khmer",
    "kn" => "Kannada",
    "ko" => "Korean",
    "kr" => "Kanuri",
    "ks" => "Kashmiri",
    "ku" => "Kurdish",
    "kv" => "Komi",
    "kw" => "Cornish",
    "ky" => "Kirghiz",
    "la" => "Latin",
    "lb" => "Luxembourgish",
    "lg" => "Ganda",
    "li" => "Limburgish",
    "ln" => "Lingala",
    "lo" => "Lao",
    "lt" => "Lithuanian",
    "lu" => "Luba-Katanga",
    "lv" => "Latvian",
    "mg" => "Malagasy",
    "mh" => "Marshallese",
    "mi" => "Maori",
    "mk" => "Macedonian",
    "ml" => "Malayalam",
    "mn" => "Mongolian",
    "mr" => "Marathi",
    "ms" => "Malay",
    "mt" => "Maltese",
    "my" => "Burmese",
    "na" => "Nauru",
    "nb" => "Norwegian Bokmal",
    "nd" => "North Ndebele",
    "ne" => "Nepali",
    "ng" => "Ndonga",
    "nl" => "Dutch",
    "nn" => "Norwegian Nynorsk",
    "no" => "Norwegian",
    "nr" => "South Ndebele",
    "nv" => "Navajo",
    "ny" => "Chichewa",
    "oc" => "Occitan",
    "oj" => "Ojibwa",
    "om" => "Oromo",
    "or" => "Oriya",
    "os" => "Ossetian",
    "pa" => "Panjabi",
    "pi" => "Pali",
    "pl" => "Polish",
    "ps" => "Pashto",
    "pt" => "Portuguese",
    "qu" => "Quechua",
    "rm" => "Raeto-Romance",
    "rn" => "Kirundi",
    "ro" => "Romanian",
    "ru" => "Russian",
    "rw" => "Kinyarwanda",
    "sa" => "Sanskrit",
    "sc" => "Sardinian",
    "sd" => "Sindhi",
    "se" => "Northern Sami",
    "sg" => "Sango",
    "si" => "Sinhala",
    "sk" => "Slovak",
    "sl" => "Slovenian",
    "sm" => "Samoan",
    "sn" => "Shona",
    "so" => "Somali",
    "sq" => "Albanian",
    "sr" => "Serbian",
    "ss" => "Swati",
    "st" => "Southern Sotho",
    "su" => "Sundanese",
    "sv" => "Swedish",
    "sw" => "Swahili",
    "ta" => "Tamil",
    "te" => "Telugu",
    "tg" => "Tajik",
    "th" => "Thai",
    "ti" => "Tigrinya",
    "tk" => "Turkmen",
    "tl" => "Tagalog",
    "tn" => "Tswana",
    "to" => "Tonga",
    "tr" => "Turkish",
    "ts" => "Tsonga",
    "tt" => "Tatar",
    "tw" => "Twi",
    "ty" => "Tahitian",
    "ug" => "Uighur",
    "uk" => "Ukrainian",
    "ur" => "Urdu",
    "uz" => "Uzbek",
    "ve" => "Venda",
    "vi" => "Vietnamese",
    "vo" => "Volapuk",
    "wa" => "Walloon",
    "wo" => "Wolof",
    "xh" => "Xhosa",
    "yi" => "Yiddish",
    "yo" => "Yoruba",
    "za" => "Zhuang",
    "zh" => "Chinese",
    "zu" => "Zulu"
    );
    return array_search($name, $languageCodes);
}

function clean_config_option($option){
	return ucwords(str_replace("_", " ", $option));
}

function get_config_SMTP ($from_name = null, $from_email = null) {

	$mail             = new PHPMailer();
	$mail->CharSet = 'UTF-8';

	$smtp_secure_connection = config_option('smtp_secure_connection', 'no');
	if($smtp_secure_connection != 'no'){
		$mail->SMTPSecure = $smtp_secure_connection;
	}
	
	$mail->IsSMTP(); // telling the class to use SMTP
	
	$smtp_authenticate = config_option('smtp_authenticate', 0);
	if($smtp_authenticate == '1') {
	
		$mail->SMTPAuth = true;
		$mail->Username   = config_option('smtp_username'); // SMTP account username
		$mail->Password   = config_option('smtp_password'); // SMTP account password
	
	}

	$mail->Host       = config_option('smtp_server'); // sets the SMTP server
	$mail->Port       = config_option('smtp_port', 25); // set the SMTP port for the GMAIL server

	if(isset($from_name) && isset($from_email)){		

		$mail->SetFrom($from_email, $from_name);
		$mail->AddReplyTo($from_email, $from_name);
	
	}else{

		$smtp_send_name =  config_option('smtp_from_name');
			
		$mail->SetFrom(config_option('smtp_from_email'), $smtp_send_name);
		$mail->AddReplyTo(config_option('smtp_reply_from_email'), $smtp_send_name);
	
	}

	return $mail;

}

function get_i_config_SMTP ($from_name = null, $from_email = null) {

	$mail             = new PHPMailer();
	$mail->CharSet = 'UTF-8';

	$smtp_secure_connection = i_config_option('smtp_secure_connection', 'no');
	if($smtp_secure_connection != 'no'){
		$mail->SMTPSecure = $smtp_secure_connection;
	}
	
	$mail->IsSMTP(); // telling the class to use SMTP
	
	$smtp_authenticate = i_config_option('smtp_authenticate', 0);
	if($smtp_authenticate == '1') {
	
		$mail->SMTPAuth = true;
		$mail->Username   = i_config_option('smtp_username'); // SMTP account username
		$mail->Password   = i_config_option('smtp_password'); // SMTP account password
	
	}

	$mail->Host       = i_config_option('smtp_server'); // sets the SMTP server
	$mail->Port       = i_config_option('smtp_port', 25); // set the SMTP port for the GMAIL server

	if(isset($from_name) && isset($from_email)){		

		$mail->SetFrom($from_email, $from_name);
		$mail->AddReplyTo($from_email, $from_name);
	
	}else{

		$smtp_send_name =  i_config_option('smtp_from_name');
			
		$mail->SetFrom(i_config_option('smtp_from_email'), $smtp_send_name);
		$mail->AddReplyTo(i_config_option('smtp_reply_from_email'), $smtp_send_name);
	
	}

	return $mail;

}

function send_i_mail_SMTP($to, $name, $subject, $message, $from_name = null, $from_email = null, $cc = null, $bc = null){
	
	$mail = get_i_config_SMTP($from_name, $from_email);
					
	$mail->Subject    = html_entity_decode($subject, ENT_NOQUOTES, 'UTF-8');
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	
	$mail->AddAddress($to, $name);
	
	if(isset($cc) && is_array($cc) && count($cc)){
		foreach($cc as $cc_e) $mail->AddCC($cc_e);
	}

	if(isset($bc) && is_array($bc) && count($bc)){
		foreach($bc as $bc_e) $mail->AddBCC($bc_e);
	}
	
	$mail->MsgHTML($message);
	$mail->Send();

}

function send_mail_SMTP($to, $name, $subject, $message, $from_name = null, $from_email = null, $cc = null, $bc = null){
	
	$mail = get_config_SMTP($from_name, $from_email);
					
	$mail->Subject    = html_entity_decode($subject, ENT_NOQUOTES, 'UTF-8');
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	
	$mail->AddAddress($to, $name);
	
	if(isset($cc) && is_array($cc) && count($cc)){
		foreach($cc as $cc_e) $mail->AddCC($cc_e);
	}

	if(isset($bc) && is_array($bc) && count($bc)){
		foreach($bc as $bc_e) $mail->AddBCC($bc_e);
	}
	
	$mail->MsgHTML($message);
	$mail->Send();

}

function get_archive_action_url($object, $action, $with_base = true) {
	
	if($object instanceof User || $object instanceof Company) {
		
		$archive_url = 'archive/'.$action.'/'.$object->getTypeName().'/'.$object->getId();
		return $with_base ? get_page_base_url($archive_url) : $archive_url;
		
	}

}

function get_objects_ids($objects) {
	
	$objects_ids = array();

	if(isset($objects) && is_array($objects) && count($objects)) {
		
		foreach($objects as $object) {
		
			if(($object instanceof Application_object) && $object->fieldExists('id')) {
				$objects_ids[] = $object->getId();
			}
		
		}
		
	}
	
	return count($objects_ids) ? $objects_ids : null;

}

function only_ajax_request_allowed() {
	if(!is_ajax_request()) die();
	
}

function is_ajax_request() {

	if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') return false;
	
	return true;
	
}

function set_flash_error($error, $die = false) {

	$CI =& get_instance();
	$CI->session->set_flashdata("error", $error);

	if($die) die();
	
}

function set_flash_success($success, $die = false) {

	$CI =& get_instance();
	$CI->session->set_flashdata("success", $success);

	if($die) die();
	
}

function array_key_value($array, $key, $default = null) {
	return isset($array[$key]) ? $array[$key] : $default;
}

function input_post_request($key, $default = '') {
	return array_key_value($_POST, $key, $default);
}

function input_get_request($key, $default = '') {
	return array_key_value($_GET, $key, $default);
}

function input_file_request($key, $default = '') {
	return array_key_value($_FILES, $key, $default);
}

function output_ajax_request($success, $message = null){

	$output = $success ? array('success' => true) : 
	array('success' => false, 'message' => $message);
	
	return json_encode($output);

}

function select_box($name, $options, $selected = null, $attributes = null) {
	
	$output = "<select name=\"".$name."\"".$attributes.">\n";
	 
	if(is_array($options)) {
		foreach($options as $index => $value) {
			$output .= "<option value=\"".$index."\"".
			($selected == $index ? " selected=\"selected\"" : "").">".$value."</option>\n";
		}
	}
	
	return $output."</select>\n";

}

function get_max_upload_size() {
	return min(php_config_value_to_bytes(ini_get('upload_max_filesize')), php_config_value_to_bytes(ini_get('post_max_size')));
}

function php_config_value_to_bytes($val) {

	$last = strtolower($val[strlen(trim($val))-1]);
	$val = (int) $val;

	switch($last) {
	
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	
	}
	
	return $val;
	
}
 
 
function format_filesize($in_bytes) {

	$units = array(
	'TB' => 1099511627776,
	'GB' => 1073741824,
	'MB' => 1048576,
	'kb' => 1024
	);

	foreach($units as $current_unit => $unit_min_value) {

		if($in_bytes > $unit_min_value) {

			$formated_number = number_format($in_bytes / $unit_min_value, 2);
			
			while(str_ends_with($formated_number, '0')) $formated_number = substr($formated_number, 0, strlen($formated_number) - 1); // remove zeros from the end
			if(str_ends_with($formated_number, '.')) $formated_number = substr($formated_number, 0, strlen($formated_number) - 1); // remove dot from the end
		
			return $formated_number . $current_unit;

		}

	}

	return $in_bytes . 'bytes';

}

function trace($trace, $l = '') {

	if($l != '') $trace .= ':' . $l;
	$trace .= "\r\n";

	file_put_contents(APPPATH.'cache/trace.txt', $trace, FILE_APPEND);

}

function str_starts_with($string, $niddle) {  
	return substr($string, 0, strlen($niddle)) == $niddle;  	
}

function str_ends_with($string, $niddle) {
	return substr($string, strlen($string) - strlen($niddle), strlen($niddle)) == $niddle;
}

function with_slash($path) {
	return str_ends_with($path, '/') ? $path : $path . '/';
}

function get_form_files($name) {

	if(isset($_FILES) && is_array($_FILES) && count($_FILES)) {

		$files = re_array_files($_FILES);
		return array_key_value($files, $name);

	}					  					

	return null;
	
}

function re_array_files(array $_files, $top = true) {
    
	$files = array();
	
    foreach($_files as $name=>$file) {
    
	    if($top) $sub_name = $file['name'];
        else $sub_name = $name;
       
        if(is_array($sub_name)) {
            
			foreach(array_keys($sub_name) as $key) {
            
			    $files[$name][$key] = array(
                    'name'     => $file['name'][$key],
                    'type'     => $file['type'][$key],
                    'tmp_name' => $file['tmp_name'][$key],
                    'error'    => $file['error'][$key],
                    'size'     => $file['size'][$key],
                );
            
			    $files[$name] = re_array_files($files[$name], false);
				
            }
			
        } else {
            $files[$name] = $file;
        }
    
	}
	
    return $files;

}

function format_date($timestamp, $format = 'm-d-Y H:i') {
	return date($format, $timestamp);
}

function format_mysql_date($date, $format = 'Y-m-d') {
	$timestamp = strtotime($date);
	return date($format, $timestamp);
}

function validate_date($date, $format = 'm/d/Y') {

	$timestamp = strtotime($date);
	return format_date($timestamp, $format) == $date;

}

function is_valid_day($timestamp, $day = 'today') {
	
	$date_format = 'Y-m-d';
	return date($date_format, strtotime($day)) == date($date_format, $timestamp);

}

function get_date_format_array($date) {

	$result = array();
	
	if(is_valid_day($date, 'today')) {
	
		array_push($result, "todayLog");
		array_push($result, "Today ".format_date($date, 'H:i'));
	
	} elseif (is_valid_day($date, 'yesterday')) {
	
		array_push($result, "yesterdayLog");
		array_push($result, "Yesterday ".format_date($date, 'H:i'));
	
	} else {
	
		array_push($result, "otherdayLog");
		array_push($result, format_date($date, 'j M. Y H:i'));
	
	} 

	return $result;
	
}

function get_activity_icon ($name) {
	
	$icon_classes_name = "fa ";
	
	switch($name) {

		case 'Projects' :
			$icon_classes_name .= "fa-building bg-blue";
			break;

		case 'ProjectTaskLists' :
			$icon_classes_name .= "fa-list bg-aqua";
			break;

		case 'ProjectTasks' :
			$icon_classes_name .= "fa-tasks bg-green";
			break;

		case 'ProjectDiscussions' :
			$icon_classes_name .= "fa-commenting-o bg-orange";
			break;

		case 'Tickets' :
			$icon_classes_name .= "fa-ticket bg-purple";
			break;

		case 'ProjectFiles' :
			$icon_classes_name .= "fa-file bg-maroon";
			break;

		case 'ProjectTimelogs' :
			$icon_classes_name .= "fa-calendar-check-o bg-orange";
			break;

		case 'ProjectComments' :
			$icon_classes_name .= "fa-comments bg-yellow";
			break;

		case 'Invoices' :
			$icon_classes_name .= "fa-money bg-navy";
			break;

		case 'Estimates' :
			$icon_classes_name .= "fa-files-o bg-olive";
			break;
		
	}

	return $icon_classes_name;
}

function get_simple_paginate($total_pages, $current_page, $base_url, $param){
	
	$simple_paginate_box = '';
	
	if($total_pages > 1){
		
		$paginate_url = $base_url.$param;
		
		// start here...
		$simple_paginate_box .= '<ul class="pagination pagination-md no-margin pull-right">';
		
		if($current_page == 1){
			$simple_paginate_box .= '<li><a href="javascript:;" class="text-bold">1</a></li> ';
		}else{
	
			$simple_paginate_box .= ($current_page == 2 ? '<li><a href="'.$base_url.'">&laquo;</a></li> ' : '<li><a href="'.str_replace("#PAGE#", ($current_page-1), $paginate_url).'">&laquo;</a></li> ');
			$simple_paginate_box .= '<li><a href="'.$base_url.'">1</a></li> ';
			
		} // if
		
		if($current_page > 7){
			$str_nev_val = $current_page-5;
			$str_extd = '<li><a href="javascript:;"> ... </a></li> ';
		}else{
			 $str_nev_val = 2;
			 $str_extd = '';
		} // if
		
		$pe = $str_nev_val+8;
		
		if($pe > $total_pages){
			$end_nev_val = $total_pages;
			$end_extd = '';
		}else{
			$end_nev_val = $pe+1;
			$end_extd = '<li><a href="javascript:;"> ... </a></li> ';
		} // if
		
		$simple_paginate_box .= $str_extd;						
		
		for($i=$str_nev_val; $i<$end_nev_val; $i++){
			
			if($i == $current_page){
				$simple_paginate_box .= '<li><a href="javascript:;" class="text-bold">'.$i.'</a></li> ';					
			}else{
				$simple_paginate_box .= '<li><a href="'.str_replace("#PAGE#", $i, $paginate_url).'">'.$i.'</a></li> ';
			} // if
			
		} // for
	
		$simple_paginate_box .= $end_extd;						
	
		if($current_page == $total_pages){
			$simple_paginate_box .= '<li><a href="javascript:;" class="text-bold">'.$total_pages.'</a></li> ';
		}else{
			$simple_paginate_box .= '<li><a href="'.str_replace("#PAGE#", $total_pages, $paginate_url).'">'.$total_pages.'</a></li>
			<li><a href="'.str_replace("#PAGE#", ($current_page+1), $paginate_url).'">&raquo;</a></li> ';
		} // if
	
		
		$simple_paginate_box .= '</ul>';
		// end here...
	
	} // if
	
	return $simple_paginate_box;
	
}

function remove_whitespaces_linebreaks($string) {
	return preg_replace('/\v(?:[\v\h]+)/', '', $string);
}

function shorter($text, $chars_limit, $readmore_url = null) {

    if (mb_strlen($text) > $chars_limit) {

        $new_text = mb_substr($text, 0, $chars_limit, 'utf-8');
        $new_text = remove_whitespaces_linebreaks($new_text);
        return $new_text . '...'.(!is_null($readmore_url) ? ' <a href="'.$readmore_url.'">Read more</a>' : '');

    } else {
	    return $text;
    }

}

function get_object_by_model_and_id($model, $object_id) {
	
	$CI =& get_instance();
	$object = $CI->$model->findById($object_id);

	return $object instanceof Application_object ? $object : null;

}

function get_avatar_default($str){

	$acronym = '';
	$alphas = range('a', 'z');

	$words = preg_split("/(\s|\-|\.)/", $str);
	$counter = 0;
	foreach($words as $w) {
		if($counter >= 2) break;
		$letter = strtolower(substr($w,0,1));
		if(in_array($letter, $alphas)) {
			$acronym .= $letter;
		}
		$counter++;
	}

	$avatarfile = ($acronym != "") ? $acronym . ".png" : "default.png";
	return "default/" . $avatarfile;

}
if ( ! function_exists('download_estimate_pdf')) {

	function download_pdf_file($html, $filename) {

		$CI =& get_instance();
		$CI->load->library('pdf');
	
		$CI->pdf->setPrintHeader(false);
		$CI->pdf->setPrintFooter(false);
		$CI->pdf->SetCellPadding(1.6);
		$CI->pdf->setImageScale(1.40);
		$CI->pdf->setCellHeightRatio(1.6);
		$CI->pdf->AddPage();
		
		$CI->pdf->writeHTML($html);
		$CI->pdf->Output($filename, "I");
	
	}

}

function clone_invoice($invoice) {

	
	$clone_invoice = new Invoice();
				
	$clone_invoice->setClientId($invoice->getClientId());
	$clone_invoice->setProjectId($invoice->getProjectId());
	$clone_invoice->setSubject($invoice->getSubject());

	$clone_invoice->setStatus($invoice->getStatus());
	$clone_invoice->setReference($invoice->getReference());

	$clone_invoice->setTax($invoice->getTax());
	$clone_invoice->setTaxRate($invoice->getTaxRate());

	$clone_invoice->setTax2($invoice->getTax2());
	$clone_invoice->setTaxRate2($invoice->getTaxRate2());

	$clone_invoice->setDiscountAmount($invoice->getDiscountAmount());
	$clone_invoice->setDiscountAmountType($invoice->getDiscountAmountType());

	$clone_invoice->setNote($invoice->getNote());
	$clone_invoice->setPrivateNote($invoice->getPrivateNote());

	$clone_invoice->setDueAfterDays($invoice->getDueAfterDays());
	$clone_invoice->setIsOnlinePaymentDisabled($invoice->getIsOnlinePaymentDisabled());

	$clone_invoice->setCompanyId($invoice->getCompanyId());
	$clone_invoice->setCompanyName($invoice->getCompanyName());
	$clone_invoice->setCompanyAddress($invoice->getCompanyAddress());

	$clone_invoice->setCreatedById($invoice->getCreatedById());
	$clone_invoice->save();

	$sub_total=0;
	$o_key = 0;
	
	$invoice_items = $invoice->getItems();
	if(isset($invoice_items) && is_array($invoice_items) && count($invoice_items)) {

		foreach($invoice_items as $invoice_item) {
		
			$clone_invoice_item = new InvoiceItem();

			$o_key++;
			$clone_invoice_item->setOKey($o_key);
			
			$item_hash_id = sha1(uniqid(rand(), true).$clone_invoice->getId().
			$invoice_item->getQuantity().$invoice_item->getAmount().
			$invoice_item->getDescription().$o_key.time());
			
			$clone_invoice_item->setUKey($item_hash_id);

			$clone_invoice_item->setInvoiceId($clone_invoice->getId());
			$clone_invoice_item->setQuantity($invoice_item->getQuantity());
			$clone_invoice_item->setAmount($invoice_item->getAmount());
			$clone_invoice_item->setDescription($invoice_item->getDescription());
							
			$clone_invoice_item->save();
							
			$sub_total += $invoice_item->getAmount()*$invoice_item->getQuantity();
		
		}
	
	}

	$total_amount = $sub_total;

	if ($invoice->getTaxRate() > 0) {
	
		$tax_amount = abs($sub_total/100*$invoice->getTaxRate());
		$total_amount += $tax_amount;

	}

	if ($invoice->getTaxRate2() > 0) {
	
		$tax2_amount = abs($sub_total/100*$invoice->getTaxRate2());
		$total_amount += $tax2_amount;
	
	}

	$calculated_discount_amount = $invoice->getDiscountAmount();
	if($calculated_discount_amount > 0) {
		if($invoice->getDiscountAmountType() == 'percentage') {
			$calculated_discount_amount = abs($calculated_discount_amount/100*$total_amount);
		}
	}

	$total_amount = ($total_amount - $calculated_discount_amount);
	$clone_invoice->setTotalAmount($total_amount);
	
	$clone_invoice->setInvoiceNo("INV-".str_pad($clone_invoice->getId(), 6, '0', STR_PAD_LEFT));
	$clone_invoice->setAccessKey(sha1(uniqid(rand(), true).$clone_invoice->getId().time()));
	
	$clone_invoice->save();

	return $clone_invoice;

}

function add_period_to_timestamp($timestamp, $value, $type, $format = 'Y-m-d') {
	return format_date(strtotime("+" . $value. " " . $type, $timestamp), $format);
}

function subtract_period_to_timestamp($timestamp, $value, $type, $format = 'Y-m-d') {
	return format_date(strtotime("-" . $value. " " . $type, $timestamp), $format);
}
