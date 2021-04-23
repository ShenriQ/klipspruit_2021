<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($project_timelog->isNew() ? lang('c_358') : (isset($is_my_timer) && $is_my_timer ? lang('c_523.8') : lang('c_359').' [Id: '.$project_timelog->getId().']'))); ?>

<form method="post" action="<?php echo get_page_base_url($project_timelog->isNew() ? 'timesheet/create' : $project_timelog->getEditURL()); ?>" id="i_timelog_form" class="form-horizontal">

<div class="form-group">
<?php $projects_options = array(lang('c_344'));

if(isset($projects) && is_array($projects) && count($projects)) :
	foreach($projects as $project) :
		$projects_options[$project->getId()] = $project->getName();
	endforeach;
endif;

echo select_box("project_id", $projects_options, $project_id, (!$project_timelog->isNew() ? ' disabled="disabled"' : '').' id="projectOptions2" class="form-control"');
?>
</div>

<div class="form-group">
<?php  echo select_box("task_id", null, null, ' id="taskOptions" class="form-control"');  ?>	
</div>

<?php if((logged_user()->isOwner() || logged_user()->isAdmin()) && !(isset($is_my_timer) && $is_my_timer)) : ?>

<div class="form-group">
<?php  echo select_box("user_id", null, null, ' id="userOptions" class="form-control"');  ?>	
</div>

<?php endif; ?>

<div class="form-group">
	<input type="text" name="hourly_rate" min="1" class="form-control" id="hourly_rate" value="<?php echo clean_field($hourly_rate); ?>" placeholder="<?php echo lang('c_523.31'); ?>" />
</div>

<div class="form-group">
	<input type="text" name="start_date" class="form-control datetimepicker" value="<?php echo clean_field($start_date); ?>" placeholder="<?php echo lang('c_360'); ?>" <?php echo (isset($is_my_timer) && $is_my_timer ? "disabled" : ""); ?> />
</div>

<div class="form-group">
	<input type="text" name="end_date" class="form-control datetimepicker" value="<?php echo clean_field($end_date); ?>" placeholder="<?php echo lang('c_361'); ?>" <?php echo (isset($is_my_timer) && $is_my_timer ? "disabled" : ""); ?> />
</div>

<div class="form-group">
	<input type="text" name="memo" class="form-control" id="memo" value="<?php echo clean_field($memo); ?>" placeholder="<?php echo lang('c_354'); ?>" />
</div>

<?php if((logged_user()->isOwner() || logged_user()->isAdmin()) && !(isset($is_my_timer) && $is_my_timer)) : ?>

<div class="form-group">
	<select name="is_approved" class="form-control">
	<option value="1"<?php echo $is_approved == 1 ? ' selected="selected"' : ''; ?>><?php echo lang('c_362'); ?></option>
	<option value="0"<?php echo $is_approved == 0 ? ' selected="selected"' : ''; ?>><?php echo lang('c_363'); ?></option>
	</select>
</div>

<?php endif; ?>

<label><input name="is_billable" type="checkbox"<?php echo ($is_billable ? ' checked="checked"' : ''); ?> /> <?php echo lang('c_523.32'); ?> <small class="custom-backgound-lightyellow-underline"><?php echo lang('c_523.29'); ?></small></label>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_timelog_submit"><?php echo ($project_timelog->isNew() ? lang('c_219') : lang('c_226')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_350'); ?></a>
</div>

</form>


<script language="javascript">

$(function() {
  var pu = function() { 
	<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>
  	$.getJSON("<?php echo get_page_base_url("projects/users_json/"); ?>" + $("#projectOptions2").val(), function(b) {
      var c = '<option value="0"><?php echo lang('c_312'); ?></option>';
      $.each(b, function(b, a) {
        c = c + '<option value="' + a.id + '"'+ (a.id == <?php echo $user_id; ?> ? ' selected="selected"' : '')+'>' + a.name + "</option>";
      });
      $("#userOptions").html(c);
    });
	<?php endif; ?>
	$.getJSON("<?php echo get_page_base_url("tasks/project_tasks_json/"); ?>" + $("#projectOptions2").val(), function(b) {
      var c = '<option value="0"><?php echo lang('c_523.25'); ?></option>';
      $.each(b, function(b, a) {
        c = c + '<option value="' + a.id + '"'+ (a.id == <?php echo $task_id; ?> ? ' selected="selected"' : '')+'>' + a.name + "</option>";
      });
      $("#taskOptions").html(c);
    });	
  };
  $("#projectOptions2").change(function() {
  	pu();
  });
  pu();
  $(".datetimepicker").datetimepicker({format:'Y-m-d H:i', step:15, minDate:'-1970/01/02', defaultTime:'10:00'});
});

</script>
