<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", 'Edit Widget'); ?>

<form method="post" action="<?php echo get_page_base_url($widget->getEditURL()); ?>" id="i_widget_form" enctype="multipart/form-data" class="form-horizontal">
    <div class="form-group">
        <input type="text" class="form-control" name="title" placeholder="Title"
            value="<?php echo clean_field($title); ?>" />
    </div>

    <div class="form-group">
        <textarea name="description" class="form-control" placeholder="Description" rows="5">
			<?php echo clean_field($description); ?>
		</textarea>
    </div>

    <div class="form-group">
        <label><?php echo lang('c_446'); ?></label>
        <p><input name="photo" type="file" id="photo">
            <label class="custom-background-light-yellow"><input name="remove_photo" type="checkbox">
                <?php echo lang('c_448'); ?></label>
        </p>
    </div>

    <input type="hidden" name="submited" value="submited" />

    <div class="form-group text-right">
        <button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>"
            id="i_widget_submit">Update</button>
        <a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
    </div>

</form>
