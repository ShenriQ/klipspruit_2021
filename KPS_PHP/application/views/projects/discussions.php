<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", $project->getName().' : '.lang('c_263')); 

tpl_assign("header_for_layout", '
');	

if(isset($object_id) && $object_id > 0) : 

$discussion_object = $this->ProjectDiscussions->findById($object_id);
if(is_null($discussion_object) || $discussion_object->getProjectId() != $project->getId()
|| $discussion_object->getIsTrashed() || ($discussion_object->getIsPrivate() && !logged_user()->isMember())) {

	set_flash_error(lang('e_3'));
	redirect($project->getObjectURL('discussions'));

}

?>


<div class="box box-solid">	
	<?php if($discussion_object->isObjectOwner(logged_user()) || logged_user()->isOwner() 
		|| (logged_user()->isAdmin() && logged_user()->isProjectUser($project))) : ?>
		<div class="box-header with-border">
			<div class="btn-group"><a href="<?php echo get_page_base_url($project->getObjectURL('discussions')); ?>" class="btn btn-default btn-sm"><i class="fa fa-long-arrow-left"></i> <?php echo lang('c_523.24'); ?></a></div>
			<div class="pull-right">
				<div class="btn-group">
					<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
					<ul class="dropdown-menu pull-right" role="menu">
					<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($discussion_object->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_121'); ?></a></li>
					<li class="divider"></li>
					<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($discussion_object, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<div class="box-body no-padding"<?php echo ($discussion_object->getIsSticky() ? ' style="background-color:#FFECD9;"' : ''); ?>>
		<div class="mailbox-read-info">
		<h3><?php echo $discussion_object->getTitle(); echo ($discussion_object->getIsPrivate() ? ' <i class="fa fa-lock"></i>' : ''); ?></h3>
		<h5><?php echo lang('c_188'); ?> <em><?php echo $discussion_object->getCreatedByName(true); ?></em>
		<span class="mailbox-read-time pull-right"><?php echo format_date($discussion_object->getCreatedAt(), 'j M. Y, g:i a'); ?>
		</span></h5>
		</div>
		<div class="mailbox-read-message">
			<span class="more"><?php echo $discussion_object->getText(); ?></span>
		</div>
	</div>
</div>

<?php if($discussion_object->isCommentable()) : 
	$discussion_comments_count = $discussion_object->getCommentsCount(); ?>
<h4 class="custom-p-5"><?php echo sprintf(lang('c_266'), lang('c_264')); ?><?php if($discussion_comments_count > 0): ?>  &nbsp; <span class="label label-primary"><?php echo $discussion_comments_count; ?></span><?php endif; ?></h4>
<?php tpl_assign('parent_object', $discussion_object);
tpl_display('comments/_comment_box'); ?>
<?php endif;?>							
			
<?php else : ?>

<p><a href="javascript:void();" data-url="<?php echo get_page_base_url($project->getCreateDiscussionURL()); ?>" class="btn btn-sm btn-success custom-m-3" data-toggle="commonmodal">+ <?php echo lang('c_120'); ?></a></p>
<div class="table-responsive">
<table class="table table-striped table-bordered">
<tbody>

<?php $include_private = logged_user()->isMember(); 
$project_discussions = $project->getDiscussions(false, $include_private);
if(isset($project_discussions) && is_array($project_discussions) && count($project_discussions)) : ?>
	
	<?php foreach($project_discussions as $project_discussion) : 
		$pd_comment_counts = $project_discussion->getCommentsCount();?>
		
		<tr <?php echo ($project_discussion->getIsSticky() ? ' style="background-color:#FFECD9;"' : ''); ?>>
			<td><b><a href="<?php echo get_page_base_url($project_discussion->getObjectURL()); ?>"><?php echo $project_discussion->getTitle(); ?></a><?php echo ($project_discussion->getIsPrivate() ? ' &nbsp; <i class="fa fa-lock"></i>' : ''); ?></b>
			<p class="custom-small-grey-color"><b><?php echo lang('c_188'); ?> </b> <u><?php echo $project_discussion->getCreatedByName(true); ?></u> &mdash; <?php echo format_date($project_discussion->getCreatedAt()); ?></p>
			<p><span class="more"><?php echo $project_discussion->getText(); ?></span></p>
			</td>
			<td width="10%">
				<?php if($pd_comment_counts > 0): ?>&nbsp; <span class="label label-primary"><?php echo $pd_comment_counts; ?></span><?php endif; ?>
				<?php if($project_discussion->isObjectOwner(logged_user()) || logged_user()->isOwner() 
				|| (logged_user()->isAdmin() && logged_user()->isProjectUser($project))) : ?>

				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
						<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($project_discussion->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_121'); ?></a></li>
						<li class="divider"></li>
						<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($project_discussion, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a>
						</ul>
					</div>
				</div>
				<?php endif; ?>													
			</td>
		</tr>

	<?php endforeach; ?>
	
<?php else :?>
	<tr><td colspan="2"><?php echo lang('e_2'); ?></td></tr>
<?php endif; ?>

</tbody></table></div>

<?php endif; ?>
