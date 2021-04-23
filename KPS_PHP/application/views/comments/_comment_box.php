<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>	

<div class="row">
<div class="col-md-8">

<div class="box box-primary message-wrap">
<div class="fixed-wrap slimscrollright-msg" id="msg_wraper">

<div class="box-footer box-comments custom-background-white">
<?php 
$created_by_id = isset($is_private_object) && $is_private_object ? logged_user()->getId() : null;
$a_object_comments = $parent_object->getComments(false, $created_by_id);
$attached_files = array(); $attached_files_counter = 0;
$users_involved = array();
if(isset($a_object_comments) && is_array($a_object_comments) && count($a_object_comments)) :
foreach($a_object_comments as $a_object_comment) : 
	$a_commnet_user = $a_object_comment->getCreatedBy(); 
	if(is_null($created_by_id) && !isset($users_involved[$a_commnet_user->getId()])){
		$users_involved[$a_commnet_user->getId()] = $a_commnet_user;
	}
	?>
    <div class="box-comment" id="comment_<?php echo $a_object_comment->getId(); ?>">
		<img class="img-circle img-sm" src="<?php echo $a_commnet_user->getAvatar(); ?>" alt="<?php echo $a_commnet_user->getName(); ?>">
		<div class="comment-text">
			  <span class="username">
				<?php echo $a_commnet_user->getName(); ?>
				<span class="text-muted pull-right"><?php echo format_date($a_object_comment->getCreatedAt(), 'j M. Y G:i'); ?></span>
			  </span>
		  <?php echo nl2br($a_object_comment->getText()); 
	
			$a_comment_files = $a_object_comment->getFiles(logged_user()->isMember());
			if(isset($a_comment_files) && is_array($a_comment_files) && count($a_comment_files)) { 
				
				echo '<p class="custom-mt-10"><b>'.lang('c_62').'</b><br>';

				foreach($a_comment_files as $a_comment_file) {
					
					if($attached_files_counter < 10) {
					
						$attached_files[$a_comment_file->getId()] = $a_comment_file;
						$attached_files_counter++;
					
					}
											
					echo '<a href="'.get_page_base_url($a_comment_file->getObjectURL()).'" target="_blank">'.$a_comment_file->getFileName().'</a><br>';
				} // foreach

				echo '</p>';

			} // if					
							
			?>

			<?php if($a_object_comment->isObjectOwner(logged_user()) || logged_user()->isOwner() 
			|| (logged_user()->isAdmin() && logged_user()->isProjectUser($project))) : ?>
	
			<div class="pull-right">
				<div class="btn-group">
					<a href="javascript:void();" class="btn btn-xs btn-default" data-url="<?php echo get_trash_action_url($a_object_comment, 'move'); ?>" data-toggle="commonmodal"><i class="fa fa-trash"></i></a>
				</div>
			</div>
			
			<?php endif; ?>
		  
		</div>
	</div>
	
<?php endforeach;	
else : ?>
<p><?php echo lang('c_64'); ?></p>
<?php endif; ?>

</div></div>
</div>

<div class="send-wrap">
<div id="responseForm"></div>

<form method="post" action="<?php echo get_page_base_url("comments/add/".$parent_object->getId()."/".$parent_object->getModelName()); ?>" enctype="multipart/form-data" id="i_comment_form">

<div class="form-group">
	<textarea class="form-control" name="message" id="message" rows="5"></textarea>
</div>

<div class="form-group">

	<div class="well">

		<p><b><?php echo lang('c_62'); ?></b> (<?php echo lang('c_65'); ?> <?php echo (format_filesize(get_max_upload_size())); ?>)</p>
		
		<div class="table-responsive">
			<table class="attach-message-file">
				<tr><td><input name="attachFiles[]" id="message_file" type="file"></td></tr>
			</table>
		</div>
									
		<p class="custom-pt-5"><a class="add-message-file btn-xs btn btn-info"><span class="glyphicon glyphicon-plus"></span> <?php echo lang('c_66'); ?></a></p>
		
		<?php if(is_null($created_by_id)) : ?>
		<hr />
		
		<p><b><?php echo lang('c_67'); ?></b></p>
		<?php $project_people = $project->getUsers(); 
		
		if(isset($notify_users) && is_array($notify_users) && count($notify_users)) {
			 $notify_top_ids = get_objects_ids($notify_users);
		} else { 
			$notify_top_ids = array();
		}
		
		if(isset($project_people) && is_array($project_people) && count($project_people)) {
			foreach($project_people as $project_user) {
				if(!in_array($project_user->getId(), $notify_top_ids)) {
					$notify_users[] = $project_user;
				}
			}
		}
		
		if(isset($notify_users) && is_array($notify_users) && count($notify_users)) : ?>
		<p class="custom-small-grey-color"><a href="javascript:void();" onclick="ToggleAll(true, 'notify_users');"><?php echo lang('c_68'); ?></a> | <a href="javascript:void();" onclick="ToggleAll(false, 'notify_users');"><?php echo lang('c_69'); ?></a></p>
		<?php
			foreach($notify_users as $notify_user) : 
				if($notify_user->getIsActive() && !$notify_user->getIsTrashed() && logged_user()->getId() != $notify_user->getId()): ?>
					<label class="custom-mr-10 custom-font-normal"><input type="checkbox" name="notify_users[]" value="<?php echo $notify_user->getId(); ?>">&nbsp;<?php echo $notify_user->getName(); ?></label>
				<?php endif;
			endforeach;
		else : ?>
			None
		<?php endif; 
		endif;?>
		
	</div>
</div>

<input type="hidden" name="submited"  id="submited" value="submited" />

<div class="form-group text-right">
	<button class="btn btn-success" type="submit" data-loading-text="<?php echo lang('c_276'); ?>" id="i_comment_submit"><?php echo lang('c_70'); ?></button>                    
</div>
		
</form>
</div>

</div>

<div class="col-md-4">

	<div class="box box-warning">
	
		<div class="box-header with-border">
		  <i class="fa fa-tasks"></i>
		  <h3 class="box-title"><?php echo lang('c_71'); ?></h3>
		</div>
		
		<div class="box-body no-padding fixed-wrap slimscrollright-files">
	
			<?php $display_files_count=0;
			  if(isset($attached_files) && is_array($attached_files) && count($attached_files)) : 
			  arsort($attached_files); ?>
				
				<?php foreach($attached_files as $project_file) : ?>
					
					<?php $file_parent = $project_file->getParent();
					$is_file_locked = $file_parent ? $file_parent->getIsPrivate() : $project_file->getIsPrivate();
					
					if(!logged_user()->isMember() && $is_file_locked) continue; 
					$display_files_count++; ?>
				
					<?php if($display_files_count == 1) : ?>
	
					<div class="table-responsive">
					<table class="table table-hover table-bordered" width="100%">
					<tbody><?php endif; ?>
							
					<tr>
			
						<td><div class="col-md-10"><a href="<?php echo get_page_base_url($project_file->getObjectURL()); ?>" target="_blank"><?php echo $project_file->getFileName(); ?></a><?php echo ($is_file_locked ? ' <i class="fa fa-lock"></i>' : ''); ?>
						<p class="custom-small-grey-color"><b><?php echo lang('c_72'); ?> </b> <u><?php echo $project_file->getCreatedByName(true); ?></u> &mdash; <?php echo format_date($project_file->getCreatedAt()); ?></p></div>
	
						<div class="col-md-2">
						<?php if($project_file->isObjectOwner(logged_user()) || logged_user()->isOwner() 
						|| (logged_user()->isAdmin() && logged_user()->isProjectUser($project))) : ?>
					
						<div class="text-right">
							<div class="btn-group">
								<?php if(!$file_parent && is_null($created_by_id)) : ?>
								<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
								<ul class="dropdown-menu pull-right" role="menu">
								<?php if($project_file->getIsPrivate()) : ?>
								<li><a href="<?php echo get_page_base_url($project_file->getShowFileURL()); ?>" onclick="return confirm('Are you sure that you want to show this file to client?');"><?php echo lang('c_73'); ?></a></li>
								<?php else : ?>
								<li><a href="<?php echo get_page_base_url($project_file->getHideFileURL()); ?>"><?php echo lang('c_74'); ?></a></li>
								<?php endif; ?>
								<li class="divider"></li>
								<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($project_file, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
								</ul>
								<?php else : ?>
								<a href="javascript:void();" class="btn btn-xs btn-default" data-url="<?php echo get_trash_action_url($project_file, 'move'); ?>" data-toggle="commonmodal"><i class="fa fa-trash"></i></a>
								<?php endif; ?>
							</div>
						</div>
						
						<?php endif; ?></div>
						
						</td>
	
					</tr>
				<?php endforeach; ?>
		
			<?php endif; ?>
		
		
			<?php if($display_files_count == 0) :?>
				<p class="custom-p-15"><?php echo lang('e_2'); ?></p>
			<?php else: ?>
				</tbody></table></div>
			<?php endif; ?>
				
		</div>
		
	</div>

	<?php if(isset($users_involved) && is_array($users_involved) && count($users_involved)) :?>

	<div class="box box-info">
	
		<div class="box-header with-border">
		  <i class="fa fa-users"></i>
		  <h3 class="box-title"><?php echo lang('c_75'); ?></h3>
		</div>
		
		<div class="box-body">

			<ul class="users-list clearfix">
			<?php foreach($users_involved as $user_involved) :?>
			<li>
			  <img src="<?php echo $user_involved->getAvatar(); ?>" class="img-circle-md" title="<?php echo $user_involved->getName(); ?>">
			  <span class="users-list-date"><?php if($user_involved->isOwner()) : ?>Owner<?php 
			   elseif($user_involved->isAdmin()) : ?>Admin<?php 
			   elseif($user_involved->isMember()) : ?>Member<?php 
			   else : ?>Client<?php endif; ?></span>
			</li>
			
			<?php endforeach; ?>
			
			</ul>
		
		</div>
	
	</div>
	
	<?php endif; ?>
		
</div>

</div>