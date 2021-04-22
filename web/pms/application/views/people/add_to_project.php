<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_223').': <u>'.$user->getName().'</u>'); ?>

<form method="post" action="<?php echo get_page_base_url($user->getAddToProjectURL()); ?>" id="i_add_to_project_form" class="form-horizontal">

<?php $unassigned_projects = $user->getUnassignedProjects();
if(isset($unassigned_projects) && is_array($unassigned_projects) && count($unassigned_projects)) : 
$submit_action_enable = true; ?>

<div class="form-group"><?php echo sprintf(lang('c_224'), '<u>'.$user->getName().'</u>'); ?>:</div>
<div class="form-group well">
<?php foreach($unassigned_projects as $unassigned_project) : ?>
<label class="custom-m-10"><input type="checkbox" name="projects[]" value="<?php echo $unassigned_project->getId(); ?>">&nbsp;<?php echo $unassigned_project->getName(); ?></label>
<?php endforeach; ?>
</div>

<?php else : $submit_action_enable = false; ?>
<div class="form-group"><?php echo lang('c_225'); ?></div>
<?php endif; ?>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<?php if ($submit_action_enable) : ?><button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_add_to_project_submit"><?php echo lang('c_226'); ?></button><?php endif; ?>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>
