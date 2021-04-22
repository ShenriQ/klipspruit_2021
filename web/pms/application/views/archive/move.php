<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_33'));?>

<?php if(isset($item_object)) : ?>

<form method="post" action="<?php echo get_archive_action_url($item_object, 'move');?>" id="i_move_to_archive_form" class="form-horizontal">

<div class="form-group">

<h4><?php echo sprintf(lang('c_34'), '<u>'.$item_object->getName().'</u>'); ?>:</h4>

<?php 

switch($item_object->getTypeName()) {

	case 'user' : 

		echo "<p>".lang('c_35')."</p>"; 
		break;
	
	case 'company' : 
		
		$item_users = $item_object->getUsers(false, true);
		if(isset($item_users) && is_array($item_users) && count($item_users)) {
			
			echo "<p>".lang('c_36')."</p><ul>";

			foreach($item_users as $item_user) {
				echo '<li>'.$item_user->getName().'</li>';		
			}

			echo "</ul>";
						
		}
		
		break;

}
	  
?>
	
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-danger" data-loading-text="<?php echo lang('c_277'); ?>" id="i_move_to_archive_submit"><?php echo lang('c_33'); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>

<?php endif; ?>
