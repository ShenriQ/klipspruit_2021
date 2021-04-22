<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_346').': <u>'.$ticket->getName().'</u>');?>

<form method="post" action="<?php echo get_page_base_url($ticket->getCloseURL());?>" id="i_close_ticket_form" class="form-horizontal">

<div class="form-group">

<h4><?php echo lang('c_347'); ?></h4>
<p><?php echo lang('c_348'); ?></p>

</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-primary" data-loading-text="<?php echo lang('c_277'); ?>" id="i_close_ticket_submit"><?php echo lang('c_349'); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_350'); ?></a>
</div>

</form>
