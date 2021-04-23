<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_58')); 

$start_date = format_date($event->getStart(), 'j M. Y H:i');
$end_date = format_date($event->getEnd(), 'j M. Y H:i');
$event_date = ($start_date == $end_date ? $start_date : $start_date." &mdash; ".$end_date); ?>

<p class="text-right"><small>&larr; <a href="<?php echo get_page_base_url('calendar'); ?>"><?php echo lang('c_409'); ?></a></small></p>
<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-calendar"></i>
	  <h3 class="box-title"><?php echo lang('c_58'); ?></h3>
	</div>
	
	<div class="box-body">

		<h4><?php echo $event->getTitle(); ?></h4>
		<span class="label <?php echo $event->getClassname(); ?>"><?php echo $event_date; ?></span>
		<p class="mt-5"><?php echo nl2br($event->getDescription()); ?></p>
	</div>

</div>

