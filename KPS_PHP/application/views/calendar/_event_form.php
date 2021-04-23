<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($event->isNew() ? lang('c_43') : lang('c_44').' <a href="'.$event->getObjectURL().'" class="btn btn-xs btn-default" target="_blank"><i class="fa fa-external-link"></i> '.lang('c_445').'</a>')); ?>

<form method="post" action="<?php echo get_page_base_url($event->isNew() ? 'calendar/create' : $event->getEditURL()); ?>" id="i_event_form" class="form-horizontal">

<div class="form-group ">
  <label for="title"><?php echo lang('c_45'); ?> *</label>
  <input type="text" name="title" class="form-control" id="title"  value="<?php echo clean_field($title); ?>" />
</div>

<div class="row">
	<div class="col-md-6">
		<div class="form-group custom-p-2">
		  <label for="start"><?php echo lang('c_46'); ?> *</label>
		  <input class="form-control datetimepicker" name="start" id="start" type="text" value="<?php echo clean_field($start); ?>" readonly />
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group custom-p-2">
		  <label for="end"><?php echo lang('c_47'); ?> *</label>
		  <input class="form-control datetimepicker" name="end" id="end" type="text" value="<?php echo clean_field($end); ?>" readonly />
		</div>
	</div>
</div>

<div class="form-group">
  <label for="description"><?php echo lang('c_48'); ?></label>
  <textarea class="form-control" rows="5" id="textfield" name="description"><?php echo clean_field($description); ?></textarea>
</div>

<div class="form-group no-border">
<?php for($i = 1; $i <= 14; $i++) : ?>
	<span class="color-selector bgC<?php echo $i; ?> <?php if($classname == "bgC".$i){ echo "selected";}?>"><input type="radio" name="classname" value="bgC<?php echo $i; ?>" <?php if($classname == "bgC".$i){ echo "selected";}?>></span>
<?php endfor; ?> 
</div>

<div class="form-group">
<label for="shared_users"><?php echo lang('c_49'); ?></label>
<select class="form-control js-basic-multiple custom-full-width" name="shared_users[]" multiple>
<?php $share_users = $this->Users->getAll();
if(isset($share_users) && is_array($share_users) && count($share_users)) {
foreach($share_users as $share_user) : ?>
<option value="<?php echo $share_user->getId(); ?>"<?php echo (in_array($share_user->getId(), $shared_user_ids) ? ' selected="selected"' : ''); ?>><?php echo $share_user->getName(); ?></option>
<?php endforeach;
} ?>	
</select>
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">

	<?php if(!$event->isNew()) : ?>
	<a class="btn btn-danger pull-left" href="<?php echo base_url($event->getDeleteURL())?>" onclick="return confirm('<?php echo lang('c_51'); ?>');"><?php echo lang('c_50'); ?></a>
	<?php endif; ?>

	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_event_submit"><?php echo ($event->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
	
</div>

</form>

<script type="text/javascript"> 
(function ($) {
	"use strict"; 	
	$(document).ready(function() { 
		$(".js-basic-multiple").select2();
		$(".datetimepicker").datetimepicker({format:'Y-m-d H:i', defaultTime:'10:00'});
		$("#i_event_form input" ).on( "click", function() {
			var classname = $( "input:checked" ).val();
			$(".color-selector").removeClass("selected"); 
			$("." + classname).addClass("selected");
		});
	}); 
})(jQuery);
</script>
