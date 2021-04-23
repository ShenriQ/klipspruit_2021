<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_516')); ?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-wrench"></i>
	  <h3 class="box-title"><?php echo lang('c_516'); ?></h3>
	</div>
	
	<div class="box-body">
	
		<div id="administrationTools">
			<div class="administrationTool">
				<h2><a href="javascript:void();" data-url="<?php echo get_page_base_url('tools/test_email'); ?>" data-toggle="commonmodal"><?php echo lang('c_515'); ?></a></h2>
				<p class="administrationToolDesc"><?php echo lang('c_517'); ?></p>
			</div>
		</div>

	</div>

</div>

