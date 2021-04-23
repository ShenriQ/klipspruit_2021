<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="modal-dialog custom-max-width-model">
<div class="modal-content">
	<div class="modal-header">
	  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
	  <h4 class="modal-title"><?php echo (isset($title_for_layout) ? $title_for_layout : ''); ?></h4>
	</div>
	<div class="modal-body">
		<div id="responseModel"></div>
		<div class="custom-m-15">
			<?php echo (isset($content_for_layout) ? $content_for_layout : ''); ?>
		</div>
	</div>	
</div>
</div>
