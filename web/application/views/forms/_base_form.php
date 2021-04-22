<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($form->isNew() ? lang('c_483') : lang('c_484').' [#'.$form->getId().']')); 

?>

<form method="post" action="<?php echo get_page_base_url($form->isNew() ? 'forms/create' : $form->getEditURL()); ?>" id="i_base_form" class="form-horizontal">

<div class="form-group">
	<label class="label-heading"><h4><?php echo lang('c_485'); ?></h4></label>
</div>

<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_486'); ?></label>
	<div class="col-md-8">
		<input class="form-control" name="title" value="<?php echo clean_field($title); ?>" type="text">
	</div>
</div>
<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_487'); ?></label>
	<div class="col-md-8">
		<textarea class="form-control" name="welcome_message"><?php echo clean_field($welcome_message); ?></textarea>
	</div>
</div>
<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_488'); ?></label>
	<div class="col-md-8">
	<?php
	
	$users = $this->Users->getAllMembers();
	$user_options = array(lang('c_332'));
	
	if(isset($users) && is_array($users) && count($users)) :
	foreach($users as $user) :
		$user_options[$user->getId()] = $user->getName();
	endforeach;
	endif;
	
	echo select_box("assigned_id", $user_options, $assigned_id, ' class="form-control"');
	
	?>	
	</div>
</div>
<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_491'); ?></label>
	<div class="col-md-8">
	<?php
	
	$statuses = $this->LeadsStatuses->getAll();
	$status_options = array(lang('c_472'));
	
	if(isset($statuses) && is_array($statuses) && count($statuses)) :
	foreach($statuses as $status) :
		$status_options[$status->getId()] = $status->getName();
	endforeach;
	endif;
	
	echo select_box("status_id", $status_options, $status_id, ' class="form-control"');
	
	?>
	</div>
</div>
<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_492'); ?></label>
	<div class="col-md-8">
	<?php
	
	$sources = $this->LeadsSources->getAll();
	$source_options = array(lang('c_473'));
	
	if(isset($sources) && is_array($sources) && count($sources)) :
	foreach($sources as $source) :
		$source_options[$source->getId()] = $source->getName();
	endforeach;
	endif;
	
	echo select_box("source_id", $source_options, $source_id, ' class="form-control"');
	
	?>
	</div>
</div>
<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_489'); ?></label>
	<div class="col-md-8">
		<lable>
		<input type="checkbox" disabled="disabled" checked="checked" />
		<input name="collect_user" type="hidden" value="1" />
		&nbsp; <?php echo lang('c_490'); ?></lable>
	</div>
</div>

<div class="form-group">
	<label class="label-heading"><h4><?php echo lang('c_493'); ?></h4></label>
</div>

<div id="fields">

<?php if($form->isNew()) : 
$field_count = 1; ?>

<div id="field-area-1" class="field-area">
	
	<div class="form-group">
	
		<div class="col-md-4">
		   <input name="field_title_1" class="form-control" placeholder="<?php echo lang('c_494'); ?>" type="text">
		</div>
	
		<div class="col-md-4">
		   <select name="field_type_1" id="field_type_1" class="form-control field_type">
		   <option value="1"><?php echo lang('c_495'); ?></option>
		   <option value="2"><?php echo lang('c_496'); ?></option>
		   <option value="3"><?php echo lang('c_497'); ?></option>
		   <option value="4"><?php echo lang('c_498'); ?></option>
		   <option value="5"><?php echo lang('c_499'); ?></option>
		   </select>
		</div>
	
		<div class="col-md-4">
		   <select name="field_require_1" class="form-control">
		   <option value="0"><?php echo lang('c_500'); ?></option>
		   <option value="1"><?php echo lang('c_501'); ?></option>
		   </select>
		</div>
	
	</div>
	
	<div class="form-group">

		<div class="col-md-4">
		   <input name="field_desc_1" class="form-control" placeholder="<?php echo lang('c_502'); ?>" type="text">
		</div>

		<div class="col-md-4">
		   <input name="field_options_1" id="field_options_1" class="form-control custom-display-none" placeholder="<?php echo lang('c_503'); ?>" type="text">
		</div>

		<div class="col-md-4">
		  <label for="field_delete_1" class="btn btn-danger"><input name="field_delete_1" value="1" id="field_delete_1" type="checkbox"> <?php echo lang('c_208'); ?></label>
		 </div>

	</div>
		
</div>

<?php else :

$field_count = 0;
$form_elements = $form->getElements(); 

if(isset($form_elements) && is_array($form_elements) && count($form_elements)) :
foreach($form_elements as $form_element) : $field_count++; ?>

<div id="field-area-<?php echo $field_count;?>" class="field-area">
	
	<div class="form-group">
	
		<div class="col-md-4">
		   <input name="field_title_<?php echo $field_count;?>" class="form-control" placeholder="<?php echo lang('c_494'); ?>" type="text" value="<?php echo $form_element->getFieldName(); ?>">
		</div>
	
		<div class="col-md-4">
		   <select name="field_type_<?php echo $field_count;?>" id="field_type_<?php echo $field_count;?>" class="form-control field_type">
		   <option value="1"<?php echo ($form_element->getFieldCategory() == 1 ? ' selected="selected"' : ''); ?>><?php echo lang('c_495'); ?></option>
		   <option value="2"<?php echo ($form_element->getFieldCategory() == 2 ? ' selected="selected"' : ''); ?>><?php echo lang('c_496'); ?></option>
		   <option value="3"<?php echo ($form_element->getFieldCategory() == 3 ? ' selected="selected"' : ''); ?>><?php echo lang('c_497'); ?></option>
		   <option value="4"<?php echo ($form_element->getFieldCategory() == 4 ? ' selected="selected"' : ''); ?>><?php echo lang('c_498'); ?></option>
		   <option value="5"<?php echo ($form_element->getFieldCategory() == 5 ? ' selected="selected"' : ''); ?>><?php echo lang('c_499'); ?></option>
		   </select>
		</div>
	
		<div class="col-md-4">
		   <select name="field_require_<?php echo $field_count;?>" class="form-control">
		   <option value="0"><?php echo lang('c_500'); ?></option>
		   <option value="1"<?php echo ($form_element->getIsRequired() ? ' selected="selected"' : ''); ?>><?php echo lang('c_501'); ?></option>
		   </select>
		</div>
	
	</div>
	
	<div class="form-group">

		<div class="col-md-4">
		   <input name="field_desc_<?php echo $field_count;?>" class="form-control" placeholder="<?php echo lang('c_502'); ?>" type="text" value="<?php echo $form_element->getHelpText(); ?>">
		</div>

		<div class="col-md-4">
		   <input name="field_options_<?php echo $field_count;?>" id="field_options_<?php echo $field_count;?>" class="form-control" value="<?php echo $form_element->getFieldData(); ?>" placeholder="<?php echo lang('c_503'); ?>" type="text" style="display:<?php echo ($form_element->getFieldCategory() > 2 ? 'block' : 'none'); ?>;">
		</div>

		<div class="col-md-4">
		  <label for="field_delete_<?php echo $field_count;?>" class="btn btn-danger"><input name="field_delete_<?php echo $field_count;?>" value="<?php echo $field_count;?>" id="field_delete_<?php echo $field_count;?>" type="checkbox"> <?php echo lang('c_208'); ?></label>
		</div>

	</div>
	
</div>

<input type="hidden" name="element_<?php echo $field_count;?>" value="<?php echo $form_element->getId(); ?>" />

<?php endforeach;
endif; 
endif; ?>

</div>

<input name="field_count" value="<?php echo $field_count;?>" id="field_count" type="hidden">
<p class="custom-mt-10"><input class="btn btn-primary" value="+ <?php echo lang('c_504'); ?>" onclick="add_form_field()" type="button"></p>

<hr>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_base_submit"><?php echo lang('c_471'); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>

<script type="text/javascript">
(function($) {
    "use strict";
    $(document).ready(function() {
        $("#fields").on("change", ".field_type", function() {
            var a = $(this).attr("name");
            a = parseInt(a.replace("field_type_", ""));
            3 <= $(this).val() ? $("#field_options_" + a).css("display", "block") : $("#field_options_" + a).css("display", "none")
        })
    });
})(jQuery);
function remove_field(a){$("#field-area-"+a).remove();a=$("#field_count").val();a--;$("#field_count").val(a)}
function add_form_field(){var a=$("#field_count").val();a++;var b=a;b='<div id="field-area-'+b+'" class="field-area"><div class="form-group"><div class="col-md-4"><input type="text" name="field_title_'+b+'" class="form-control" placeholder="<?php echo lang('c_494'); ?>"></div><div class="col-md-4"><select name="field_type_'+b+'" id="field_type_'+b+'" class="form-control field_type"><option value="1"><?php echo lang('c_495'); ?></option><option value="2"><?php echo lang('c_496'); ?></option><option value="3"><?php echo lang('c_497'); ?></option><option value="4"><?php echo lang('c_498'); ?></option><option value="5"><?php echo lang('c_499'); ?></option></select></div><div class="col-md-4"><select name="field_require_'+
b+'" class="form-control"><option value="0"><?php echo lang('c_500'); ?></option><option value="1"><?php echo lang('c_501'); ?></option></select></div></div><div class="form-group"><div class="col-md-4"><input type="text" name="field_desc_'+b+'" class="form-control" placeholder="<?php echo lang('c_502'); ?>"></div><div class="col-md-4"><input type="text" name="field_options_'+b+'" id="field_options_'+b+'" class="form-control custom-display-none" placeholder="<?php echo lang('c_503'); ?>"></div><div class="col-md-4"><label for="field_delete_'+b+'" class="btn btn-danger"><input name="field_delete_'+b+'" value="'+b+'" id="field_delete_'+b+'" type="checkbox"> <?php echo lang('c_208'); ?></label></button></div></div></div>';$("#fields").append(b);$("#field_count").val(a)};
</script>
