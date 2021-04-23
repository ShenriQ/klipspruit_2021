<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", $project->getName().' : '.lang('c_267')); 

tpl_assign("header_for_layout", '
<link href="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.css").'" rel="stylesheet">
');	

tpl_assign("footer_for_layout", '
<script src="'.get_page_base_url("public/assets/vendor/datatables/js/jquery.dataTables.min.js").'"></script>
<script src="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.min.js").'"></script>
<script>
(function ($) {
	"use strict";
	$(document).ready(function() {
		$(".files").DataTable({
		"iDisplayLength": 10,
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": false,
		language: {
				search: "_INPUT_",
				searchPlaceholder: "'.lang('c_25').'",
				"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/'.get_site_language(true).'.json"

		}  
		});
	});
})(jQuery);	
</script>
');	

?>	

<div id="responseForm"></div>

<div class="table-responsive">
<table class="table table-striped table-bordered files">
<thead>
<th><?php echo lang('c_268'); ?></th>
<th><?php echo lang('c_269'); ?></th>
<th><?php echo lang('c_270'); ?></th>
<th><?php echo lang('c_271'); ?></th>
<th><?php echo lang('c_205'); ?></th>
</thead>
<tbody>

<?php $include_private = logged_user()->isMember(); 
$project_files = $project->getFiles(false, $include_private);
$display_files_count = 0;
if(isset($project_files) && is_array($project_files) && count($project_files)) : ?>
	
	<?php foreach($project_files as $project_file) : ?>
		
		<?php $file_parent = $project_file->getParent();
		if($file_parent && ($file_parent instanceof ProjectComment) && $file_parent->getParentType() == 'Projects') continue; // Private Notes
		$is_file_locked = $file_parent ? $file_parent->getIsPrivate() : $project_file->getIsPrivate();
		
		if(!logged_user()->isMember() && $is_file_locked) continue; 
		$display_files_count++; ?>
		
		<tr>

			<td><a href="<?php echo get_page_base_url($project_file->getObjectURL()); ?>" target="_blank"><?php echo $project_file->getFileName(); ?></a><?php echo ($is_file_locked ? ' <i class="fa fa-lock"></i>' : ''); ?></td>
			<td><?php echo $project_file->getFileTypeString(); ?></td>
			<td><?php echo ($project_file->getParentId() > 0 ? str_replace("Project", "", $project_file->getParentType()) : lang('c_153')); ?></td>
			<td><?php echo $project_file->getCreatedByName(true); ?></td>
			<td><?php echo format_date($project_file->getCreatedAt()); ?>
			
			<?php if($project_file->isObjectOwner(logged_user()) || logged_user()->isOwner() 
			|| (logged_user()->isAdmin() && logged_user()->isProjectUser($project))) : ?>

			<div class="pull-right">
				<div class="btn-group">
					<?php if(!$file_parent) : ?>
					<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
					<ul class="dropdown-menu pull-right" role="menu">
					<?php if($project_file->getIsPrivate()) : ?>
					<li><a href="<?php echo get_page_base_url($project_file->getShowFileURL()); ?>" onclick="return confirm('<?php echo lang('c_272'); ?>');"><?php echo lang('c_73'); ?></a></li>
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
			
			<?php endif; ?>
													
			</td>

		</tr>

	<?php endforeach; ?>
	
<?php endif; ?>

<?php if($display_files_count == 0) :?>
	<tr><td colspan="5"><?php echo lang('e_2'); ?></td></tr>
<?php endif; ?>

</tbody></table>
</div><br>

<div class="box box-primary">

<div class="box-header with-border">
  <i class="fa fa-file-o"></i>
  <h3 class="box-title"><?php echo lang('c_273'); ?></h3>
</div>

<div class="box-body">


<form method="post" action="<?php echo get_page_base_url($project->getUploadFilesURL()); ?>" enctype="multipart/form-data" id="i_upload_form">

<div class="form-group">

	<div class="well">

		<p><b><?php echo lang('c_274'); ?></b> (<?php echo lang('c_65'); ?>: <?php echo (format_filesize(get_max_upload_size())); ?>)</p>
		
		<div class="table-responsive">
		<table class="attach-alone-file">
			<tr><td><input name="attachFiles[]" id="alone_file" type="file"></td></tr>
		</table>
		</div>
									
		<p class="custom-pt-5"><a class="add-alone-file btn-xs btn btn-info"><span class="glyphicon glyphicon-plus"></span> <?php echo lang('c_66'); ?></a></p>
		
		<hr />
		
		<p><b><?php echo lang('c_275'); ?>:</b></p>
		<?php $notify_users = $project->getUsers();
		if(isset($notify_users) && is_array($notify_users) && count($notify_users)) : ?>
		<p class="custom-small-grey-color"><a href="javascript:void();" onclick="ToggleAll(true, 'notify_users');"><?php echo lang('c_68'); ?></a> | <a href="javascript:void();" onclick="ToggleAll(false, 'notify_users');"><?php echo lang('c_69'); ?></a></p>
		<?php
			foreach($notify_users as $notify_user) : 
				if($notify_user->getIsActive() && !$notify_user->getIsTrashed() && logged_user()->getId() != $notify_user->getId()) : ?>
					<label class="custom-mr-10 custom-font-normal"><input type="checkbox" name="notify_users[]" value="<?php echo $notify_user->getId(); ?>">&nbsp;<?php echo $notify_user->getName(); ?></label>
				<?php endif;
			endforeach;
		else : ?>
			None
		<?php endif; ?>

	</div>

</div>

<input type="hidden" name="submited"  id="submited" value="submited" />

<div class="form-group text-right">
	<button class="btn btn-success" type="submit" data-loading-text="<?php echo lang('c_276'); ?>" id="i_upload_submit"><?php echo lang('c_274'); ?></button>
</div>
		
</form>

</div>

</div>