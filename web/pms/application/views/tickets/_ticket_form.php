<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($ticket->isNew() ? lang('c_300') : lang('c_189').' # '.$ticket->getTicketNo())); ?>

<form method="post" action="<?php echo get_page_base_url($ticket->isNew() ? 'tickets/create' : $ticket->getEditURL()); ?>" id="i_ticket_form" class="form-horizontal">

<div class="form-group">
	<input type="text" name="subject" value="<?php echo clean_field($subject); ?>" class="form-control" placeholder="<?php echo lang('c_335'); ?>" />
</div>

<div class="form-group">
	<textarea class="form-control" name="description" placeholder="<?php echo lang('c_343'); ?>"><?php echo clean_field($description); ?></textarea>
</div>

<div class="form-group">
<?php $projects_options = array(lang('c_344'));

if(isset($projects) && is_array($projects) && count($projects)) :
	foreach($projects as $project) :
		$projects_options[$project->getId()] = $project->getName();
	endforeach;
endif;

echo select_box("project_id", $projects_options, $project_id, (!$ticket->isNew() ? ' disabled="disabled"' : '').' id="projectOptions" class="form-control"');

?>	
</div>

<?php if(logged_user()->isMember()) : ?>

<div class="form-group">
<?php  echo select_box("assignee_id", null, null, ' id="userOptions" class="form-control"');  ?>	
</div>

<?php endif; ?>

<div class="form-group">
<?php $ticket_types_options = array(lang('c_345'));

if(isset($ticket_types) && is_array($ticket_types) && count($ticket_types)) :
	foreach($ticket_types as $ticket_type) :
		$ticket_types_options[$ticket_type->getId()] = $ticket_type->getName();
	endforeach;
endif;

echo select_box("type_id", $ticket_types_options, $type_id, ' class="form-control"');

?>	
</div>

<div class="form-group">
<?php

$labels = $this->GlobalLabels->getByType('TICKET');

$labels_options = array(lang('c_251'));
foreach($labels as $label) :
	$labels_options[$label->getId()] = $label->getName();
endforeach;

echo select_box("label_id", $labels_options, $label_id, ' class="form-control"');

?>
</div>

<div class="form-group">
	<select name="status" class="form-control">
	<option value="1"<?php echo $status == 1 ? ' selected="selected"' : ''; ?>><?php echo lang('c_298'); ?></option>
	<option value="0"<?php echo $status == 0 ? ' selected="selected"' : ''; ?>><?php echo lang('c_299'); ?></option>
	</select>
</div>


<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_ticket_submit"><?php echo ($ticket->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>


<script language="javascript">

$(function() {
  var pu = function() { 
  	$.getJSON("<?php echo get_page_base_url("projects/users_json/"); ?>" + $("#projectOptions").val(), function(b) {
      var c = '<option value="0"><?php echo lang('c_332'); ?></option>';
      $.each(b, function(b, a) {
        c = c + '<option value="' + a.id + '"'+ (a.id == <?php echo $assignee_id; ?> ? ' selected="selected"' : '')+'>' + a.name + "</option>";
      });
      $("#userOptions").html(c);
    });
  };
  $("#projectOptions").change(function() {
  	pu();
  });
  pu();
});

</script>