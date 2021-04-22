<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", 'History of Changes: <u>'.$company->getName().'</u>'); 


echo '<pre>';
print_r($company);
echo '</pre>';

?>
