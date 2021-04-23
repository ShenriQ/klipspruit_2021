<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<strong><?php echo lang('c_80'); ?>: </strong><?php echo $company_name; ?><br>
<br>
<strong><?php echo lang('c_23'); ?>: </strong><?php echo $project_name; ?><br>
<br>
<strong><?php echo lang('c_267'); ?>: </strong><br>
<?php echo $files; ?><br>
<?php echo lang('c_422'); ?>:<br>
<a href="<?php echo $files_link; ?>" target="_blank"><?php echo $files_link; ?></a><br>
<br>--<br>
<a href="<?php echo base_url();?>" target="_blank"><?php echo config_option('site_name'); ?></a>