<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?php echo lang('c_403'); ?><?php echo (isset($title_for_layout) ? ' - '.$title_for_layout : ''); ?></title>

<link href="<?php echo base_url();?>public/assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/bower_components/Ionicons/css/ionicons.min.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/css/custom.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/dist/css/AdminLTE.min.css" rel="stylesheet">

<link rel="stylesheet" href="<?php echo base_url();?>public/assets/dist/css/skins/skin-blue.min.css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<link href="<?php echo base_url();?>public/assets/vendor/nprogress/nprogress.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/vendor/jquery-ui/jquery-ui.min.css" rel="stylesheet">

<?php echo (isset($header_for_layout) ? $header_for_layout : ''); ?>

</head>

<body>

<div class="container">

<div class="row">

<div class="col-md-12">
<div class="docbox">

<?php if(isset($heading_for_dialog) || isset($title_for_layout)) : ?>
<div class="panel-heading">
	<h3 class="panel-title"><?php echo (isset($heading_for_dialog) ? $heading_for_dialog : $title_for_layout); ?></h3>
</div>
<?php endif; ?>

<div class="panel-body">

<?php echo (isset($content_for_layout) ? $content_for_layout : ''); ?>

</div>

</div>
</div></div>

</div>

<?php $gl_success = isset($success) ? $success : $this->session->flashdata('success'); ?>
<?php if(!empty($gl_success)) :?>	
<div class="notify success"><?php echo $gl_success; ?></div>
<?php endif; ?>

<?php $gl_error = isset($error) ? $error : $this->session->flashdata('error'); ?>
<?php if(!empty($gl_error)) :?>
<div class="notify error"><?php echo $gl_error; ?></div>
<?php endif; ?>

<div class="modal fade" id="common-modal" tabindex="-1" role="dialog" aria-hidden="true" 
data-backdrop="static" data-keyboard="false"></div>

<script src="<?php echo base_url();?>public/assets/vendor/nprogress/nprogress.js"></script>

<script src="<?php echo base_url();?>public/assets/vendor/jquery/jquery.min.js"></script>

<script src="<?php echo base_url();?>public/assets/vendor/jquery-ui/jquery-ui.min.js"></script>

<script src="<?php echo base_url();?>public/assets/vendor/bootstrap/js/bootstrap.min.js"></script>

<script src="<?php echo base_url();?>public/assets/vendor/metisMenu/metisMenu.min.js"></script>

<script src="<?php echo base_url();?>public/assets/dist/js/sb-admin-2.js"></script>

<script src="<?php echo base_url();?>public/assets/js/application.js"></script>

<?php echo (isset($footer_for_layout) ? $footer_for_layout : ''); ?>

</body>

</html>
