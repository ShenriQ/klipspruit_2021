<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<title>Administration Panel<?php 
echo (isset($title_for_layout) ? ' - '.$title_for_layout : ''); ?></title>

<link href="<?php echo base_url();?>public/assets/vendor/jquery.ganttView.css" rel="stylesheet">

<link href="<?php echo base_url();?>public/assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/bower_components/Ionicons/css/ionicons.min.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/css/custom.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/dist/css/AdminLTE.min.css" rel="stylesheet">

<?php $site_theme = get_site_theme(); ?>
<link rel="stylesheet" href="<?php echo base_url();?>public/assets/dist/css/skins/skin-blue.min.css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  
<link href="<?php echo base_url();?>public/assets/vendor/nprogress/nprogress.css" rel="stylesheet">
<link href="<?php echo base_url();?>public/assets/vendor/jquery-ui/jquery-ui.min.css" rel="stylesheet">

<?php echo (isset($header_for_layout) ? $header_for_layout : ''); ?>
<link rel='shortcut icon' type="image/png" href="<?php echo base_url()."favicon.ico" ; ?>"/>
</head>

<body class="hold-transition skin-blue sidebar-mini">

<div class="preloader">
	<div class="cssload-speeding-wheel"></div>
</div>

<div class="wrapper">

<header class="main-header">

    <a href="<?php echo base_url();?>admin/dashboard">
      <span class="logo"><?php echo i_config_option("logo_text", "PROMS"); ?></span>
    </a>

    <nav class="navbar navbar-static-top" role="navigation">

      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
	  </a>
	  
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <li class="dropdown user user-menu">

            <a href="#" class="dropdown-toggle" data-toggle="dropdown">

              <img src="<?php echo logged_admin_user()->getAvatar(); ?>" class="user-image" alt="<?php echo logged_admin_user()->getName(); ?>">
              <span class="hidden-xs"><?php echo logged_admin_user()->getName(); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo logged_admin_user()->getAvatar(); ?>" class="img-circle" alt="<?php echo logged_admin_user()->getName(); ?>">
                <p>
                  <?php echo logged_admin_user()->getName(); ?>
                  <small>Member since <?php echo format_date(logged_admin_user()->getCreatedAt(), 'M. Y'); ?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="javascript:void();" data-url="<?php echo get_page_base_url(logged_admin_user()->getEditProfileURL()); ?>" data-toggle="commonmodal" class="btn btn-default btn-flat">Edit Profile</span></a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo base_url();?>admin/access/logout<?php echo '?ref='.base64_encode(current_url()); ?>" class="btn btn-default btn-flat">Logout</span></a>
                </div>
              </li>
            </ul>
          </li>
		  		  
        </ul>
      </div>
    </nav>
  </header>
  
  <aside class="main-sidebar">

    <section class="sidebar">
      <hr/>
      <ul class="sidebar-menu" data-widget="tree">

        <li<?php echo ($this->router->fetch_class() == 'dashboard' || $this->router->fetch_class() == 'Dashboard' ? ' class="active"' : ''); ?>>
          <a href="<?php echo get_page_base_url('admin/dashboard'); ?>"><i class="fa fa-dashboard fa-fw"></i> <span>Dashboard</span></a>
        </li>

        <li<?php echo ($this->router->fetch_class() == 'subscriptions' || $this->router->fetch_class() == 'Subscriptions' ? ' class="active"' : ''); ?>>
          <a href="<?php echo get_page_base_url('admin/subscriptions'); ?>"><i class="fa fa-flag fa-fw"></i> <span>Subscriptions</span></a>
        </li>
        
        <li<?php echo ($this->router->fetch_class() == 'orders' || $this->router->fetch_class() == 'orders' ? ' class="active"' : ''); ?>>
          <a href="<?php echo get_page_base_url('admin/orders'); ?>"><i class="fa fa-money fa-fw"></i> <span>Orders</span></a>
        </li>

        <li<?php echo ($this->router->fetch_class() == 'packages' ? ' class="active"' : ''); ?>>
    			<a href="<?php echo get_page_base_url('admin/packages'); ?>"<?php echo ($this->router->fetch_class() == 'settings' ? ' class="active"' : ''); ?>><i class="fa fa-cubes fa-fw"></i> <span>Packages</span></a>
		    </li>	

        <li<?php echo ($this->router->fetch_class() == 'settings' ? ' class="active"' : ''); ?>>
    			<a href="<?php echo get_page_base_url('admin/settings'); ?>"<?php echo ($this->router->fetch_class() == 'settings' ? ' class="active"' : ''); ?>><i class="fa fa-gear fa-fw"></i> <span>Settings</span></a>
		    </li>	

      </ul>

    </section>

  </aside>

  <div class="content-wrapper">

    <?php if(isset($page_heading)) : ?>
      <section class="content-header">
      <h1><?php echo $page_heading; ?></h1>
        <ol class="breadcrumb">
          <li><a href="<?php echo base_url();?>"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active"><?php echo $module_name; ?></li>
        </ol>
      </section>
    <?php endif; ?>

    <section class="content container-fluid">
					
		<?php echo (isset($content_for_layout) ? $content_for_layout : ''); ?>
					
    </section>

  </div>

  <footer class="main-footer">
    Copyright <?php echo date("Y"); ?> &copy; <strong><?php echo i_config_option("site_name", "Project Management System"); ?>.</strong> All rights reserved.
  </footer>

  <div class="control-sidebar-bg"></div>

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

<script src="<?php echo base_url();?>public/assets/js/jquery.slimscroll.js"></script>

<script src="<?php echo base_url();?>public/assets/js/waves.js"></script>

<script src="<?php echo base_url();?>public/assets/js/custom.min.js"></script>

<script src="<?php echo base_url();?>public/assets/vendor/jquery-base64/jquery.base64.min.js"></script>

<script src="<?php echo base_url();?>public/assets/js/application.js"></script>

<script src="<?php echo base_url();?>public/assets/vendor/jquery.ganttView.js"></script>   

<script src="<?php echo base_url();?>public/assets/dist/js/adminlte.min.js"></script>

<script src="<?php echo base_url();?>public/assets/bower_components/fastclick/lib/fastclick.js"></script>

<script src="<?php echo base_url();?>public/assets/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>

<script src="<?php echo base_url();?>public/assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?php echo base_url();?>public/assets/bower_components/chart.js/Chart.js"></script>


<script type="text/javascript">
(function($){
	"use strict";
	$(document).ready(function(){
		$(".slimscrollright-msg").slimScroll({height:"100%",position:"right",start:"bottom",size:"5px",color:"#dcdcdc"});
		$(".slimscrollright-files").slimScroll({height:"100%",position:"right",size:"5px",color:"#dcdcdc"});
		$('#site_lang_list').on('change', function() {

			var location = $(this).val();
			if (location != "") {
				window.location.href = location;
			}
			return false;
		
		});

		$('#site_theme_list').on('change', function() {

			var location = $(this).val();
			if (location != "") {
				window.location.href = location;
			}
			return false;
		
		});
		
	});
})(jQuery);
</script>

<?php echo (isset($footer_for_layout) ? $footer_for_layout : ''); ?>

</body>
</html>