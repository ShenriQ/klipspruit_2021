<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", $form->getTitle()); ?>

<form method="post" action="<?php echo (isset($lead) ? get_page_base_url($lead->getEditURL()) : get_page_base_url($form->getObjectURL())); ?>" id="i_lead_form" class="form-horizontal">

<p><?php echo $form->getDescription(); ?></p>
<hr />

<?php $client = $this->Users->findById($client_id);
if(isset($client)) : ?>

<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_29'); ?></label>
	<div class="col-md-8">
		<?php echo $client->getName(); ?>	
	</div>
</div>

<?php endif; ?>

<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_23'); ?></label>
	<div class="col-md-8">

	<?php $projects_options = array(lang('c_135'));

	if(isset($projects) && is_array($projects) && count($projects)) :
		foreach($projects as $project) :
			$projects_options[$project->getId()] = $project->getName();
		endforeach;
	endif;

	echo select_box("project_id", $projects_options, $project_id, ' id="project_id" class="form-control"');

	?>
	</div>
</div>

<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>

<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_297'); ?></label>
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

<?php endif; ?>

<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_465'); ?></label>
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
	<label class="col-md-4"><?php echo lang('c_461'); ?></label>
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

<hr />

<?php if($form->getIsCollectUserinfo()) : ?>
 	
<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_474'); ?> *</label>
	<div class="col-md-8">
		<input class="form-control" name="name" value="<?php echo clean_field($name); ?>" type="text">
	</div>
</div>
<div class="form-group">
	<label class="col-md-4"><?php echo lang('c_475'); ?> *</label>
	<div class="col-md-8">
		<input class="form-control" name="email" value="<?php echo clean_field($email); ?>" type="email">
	</div>
</div>
<div class="form-group">
    <label class="col-md-4"><?php echo lang('c_77'); ?></label>
    <div class="col-md-8">
      <input name="address" class="form-control" value="<?php echo clean_field($address); ?>" type="text">
    </div>
</div>
<div class="form-group">
    <label class="col-md-4"><?php echo lang('c_476'); ?></label>
    <div class="col-md-8">
      <input name="city" class="form-control" value="<?php echo clean_field($city); ?>" type="text">
    </div>
</div>
<div class="form-group">
    <label class="col-md-4"><?php echo lang('c_477'); ?></label>
    <div class="col-md-8">
      <input name="state" class="form-control" value="<?php echo clean_field($state); ?>" type="text">
    </div>
</div>
<div class="form-group">
    <label class="col-md-4"><?php echo lang('c_478'); ?></label>
    <div class="col-md-8">
      <input name="postcode" class="form-control" value="<?php echo clean_field($postcode); ?>" type="text">
    </div>
</div>
<div class="form-group">
    <label class="col-md-4"><?php echo lang('c_479'); ?></label>
    <div class="col-md-8">
      <input name="country" class="form-control" value="<?php echo clean_field($country); ?>" type="text">
    </div>
</div>
<div class="form-group">
    <label class="col-md-4"><?php echo lang('c_78'); ?></label>
    <div class="col-md-8">
      <input name="phone_number" class="form-control" value="<?php echo clean_field($phone_number); ?>" maxlength="30" type="text">
    </div>
</div>
<hr />
<?php endif; ?>

<?php if(isset($form_elements) && is_array($form_elements) && count($form_elements)) : ?>
<?php foreach ($form_elements as $form_element) {

	if(isset($lead)) {
		
		$CI =& get_instance();	
		
		$element_value_object = $CI->LeadFormElementValues->getElementByLeadElement($form_element, $lead);
		$element_value = $element_value_object->getElementValue();
		
	} else {
		$element_value = null;
	}
	
	render_dynamic_form_element($form_element, $element_value); 

} ?> <hr />
<?php endif; ?>

* = <?php echo lang('c_480'); ?>
<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_lead_submit"><?php echo lang('c_471'); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>
