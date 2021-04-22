<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", $project->getName().' : Gantt'); 

$gantt_data = '{name: "<u>'.$project->getName().'</u>", desc: "", values: [{ 
label: "", from: "'.format_date($project->getStartDate(), 'Y-m-d').'", to: "'.format_date($project->getEndDate(), 'Y-m-d').'", customClass: "gantt-headerline"}]},';

$project_milestones = $project->getDatedTaskLists(logged_user()->isMember());
if(isset($project_milestones) && is_array($project_milestones) && count($project_milestones)) {

	foreach ($project_milestones as $project_milestone) {
	
		$gantt_data .= '{name: "'.$project_milestone->getName().'", desc: "", values: [
		{label: "", from: "'.format_date($project_milestone->getStartDate(), 'Y-m-d').'", to: "'.format_date($project_milestone->getDueDate(), 'Y-m-d').'", customClass: "gantt-timeline" }]},'; 
		
		$project_tasks = $project_milestone->getDatedTasks();
		if(isset($project_tasks) && is_array($project_tasks) && count($project_tasks)) {
	
			foreach ($project_tasks as $project_task) {
		
				 $gantt_data .= '{name: "<span class=\"sub_gantt_text\">'.$project_task->getName().'</span>", desc: "", values: [
				 {label: "'.$project_task->getName().'", from: "'.format_date($project_task->getStartDate(), 'Y-m-d').'", 
				 to: "'.format_date($project_task->getDueDate(), 'Y-m-d').'", customClass: "'.($project_task->getCompletedById() > 0 ? "ganttGreen" : "").'"}]}, ';
		
		    }
	
		}
	}

}

tpl_assign("footer_for_layout", '<script>
(function ($) {
	"use strict";
	$(document).ready(function() {
		var ganttData = ['.$gantt_data.'];
		ganttChart(ganttData);
	});
})(jQuery);	
</script>');	

?>

<div class="gantt"></div>