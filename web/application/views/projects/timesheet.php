<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
if(!(logged_user()->isMember() || (!logged_user()->isMember() && $project->getIsTimelogVisible()))) {
	set_flash_error(lang('e_3'));
	redirect($project->getObjectURL());
}

tpl_assign("title_for_layout", $project->getName().' : '.lang('c_302')); 

$timelogs = $project->getTimesheet();
$is_project_panel = true;

include(APPPATH.'views/timesheet/index.php');

?>