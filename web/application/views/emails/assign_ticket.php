<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php echo lang('c_421'); ?>,<br>
<br><?php echo sprintf(lang('c_423'), '<b>'.$ticket_subject.'(#'.$ticket_no.')</b>'); ?><br>
<br>
<?php echo lang('c_422'); ?>:<br>
<a href="<?php echo $ticket_link; ?>" target="_blank"><?php echo $ticket_link; ?></a><br>
<br>--<br>
<a href="<?php echo base_url();?>" target="_blank"><?php echo config_option('site_name'); ?></a>