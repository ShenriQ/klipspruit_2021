<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($discussion->isNew() ? lang('c_120'): lang('c_121'))); ?>

<form method="post" action="<?php echo get_page_base_url($discussion->isNew() ? $project->getCreateDiscussionURL() : $discussion->getEditURL()); ?>" id="i_topic_form" class="form-horizontal">

<?php if($discussion->isNew()) : ?><div class="form-group">
<?php echo lang('c_119'); ?>
</div><?php endif; ?>
	
<div class="form-group">
	<input type="text" class="form-control" name="title" value="<?php echo clean_field($title); ?>" maxlength="100" placeholder="<?php echo lang('c_115'); ?>" />
</div>

<div class="form-group">
	<textarea class="form-control" name="text" placeholder="<?php echo lang('c_116'); ?>"><?php echo clean_field($text); ?></textarea>
</div>

<div class="form-group">

<?php if(logged_user()->isMember()) : ?>
<label><input name="is_private" type="checkbox"<?php echo ($is_private ? ' checked="checked"' : ''); ?> /> <?php echo lang('c_122'); ?> <small class="custom-backgound-lightyellow-underline"><?php echo lang('c_123'); ?></small></label><br>
<?php endif; ?>

<label><input name="is_sticky" type="checkbox"<?php echo ($is_sticky ? ' checked="checked"' : ''); ?> /> <?php echo lang('c_124'); ?> <small class="custom-backgound-lightyellow-underline"><?php echo lang('c_125'); ?></small></label>

</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_topic_submit"><?php echo ($discussion->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>
