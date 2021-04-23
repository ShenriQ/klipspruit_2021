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
<link href="<?php echo base_url();?>public/assets/dist/css/AdminLTE.min.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

<?php echo (isset($header_for_layout) ? $header_for_layout : ''); ?>

</head>

<body class="hold-transition login-page">

<div class="login-box">

	<div class="login-logo">
		<a href="<?php echo base_url();?>/"><?php echo i_config_option("logo_text", "PROMS"); ?></a>
	</div>
	
	<div class="login-box-body">
	
		<?php if(isset($heading_for_dialog) || isset($title_for_layout)) : ?>
		<h4 class="login-box-msg"><?php echo (isset($heading_for_dialog) ? $heading_for_dialog : $title_for_layout); ?></h4>
		<?php endif; ?>
		
		<?php $gl_success = isset($success) ? $success : $this->session->flashdata('success'); ?>
		<?php if(!empty($gl_success)) :?>	
		<div class="alert alert-success"><?php echo $gl_success; ?></div>
		<?php endif; ?>
		
		<?php $gl_error = isset($error) ? $error : $this->session->flashdata('error'); ?>
		<?php if(!empty($gl_error)) :?>
		<div class="alert alert-danger"><?php echo $gl_error; ?></div>
		<?php endif; ?>
		
		<?php echo (isset($content_for_layout) ? $content_for_layout : ''); ?>
	
	</div>

</div>

<script src="<?php echo base_url();?>public/assets/vendor/jquery/jquery.min.js"></script>
<script src="<?php echo base_url();?>public/assets/vendor/bootstrap/js/bootstrap.min.js"></script>

<?php echo (isset($footer_for_layout) ? $footer_for_layout : ''); ?>

</body>

</html>
