<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<title><?php echo lang('c_403'); ?> / <?php 
echo (isset($title_for_layout) ? $title_for_layout : ''); ?></title>

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

    <a href="<?php echo base_url();?>">
	  <span class="logo sidebar-logo">
	  	<img
            src="<?php echo base_url();?>public/assets/images/logo.png"
            width = "22"
            height = "25"
          />
          <p>Collaboration</p>
	  </span>
    </a>

    <nav class="navbar navbar-static-top" role="navigation">

      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

	  <?php
	
		$overdues = array();
		$o_project_ids = get_objects_ids(logged_user()->getActiveProjects());
		
		if(logged_user()->isOwner() || !logged_user()->isMember()) {
			
			if(logged_user()->isOwner()) {
				$o_client_ids = get_objects_ids($this->Users->getByClients());
			} else {
				$o_client_ids = array(logged_user()->getId());
			}
			
			$o_estimates = $this->Estimates->getOverdues($o_client_ids);
			if(isset($o_estimates) && is_array($o_estimates) && count($o_estimates)) {
				foreach($o_estimates as $o_estimate) {
					$overdues[] = array('text' => lang('c_130').' #'.$o_estimate->getEstimateNo().' <font color="red"><small>'.lang('c_154').'</small></font>', 'icon' => 'fa-file-text-o text-yellow', 'link' => get_page_base_url($o_estimate->getObjectURL()));
				}
			}
			
			$o_invoices = $this->Invoices->getOverdues($o_client_ids);
			if(isset($o_invoices) && is_array($o_invoices) && count($o_invoices)) {
				foreach($o_invoices as $o_invoice) {
					$overdues[] = array('text' => lang('c_173').' #'.$o_invoice->getInvoiceNo().' <font color="red"><small>'.lang('c_154').'</small></font>', 'icon' => 'fa-money text-purple', 'link' => get_page_base_url($o_invoice->getObjectURL()));
				}
			}
		
		}
		
		
		$o_projects = $this->Projects->getOverdues($o_project_ids);
		if(isset($o_projects) && is_array($o_projects) && count($o_projects)) {
			foreach($o_projects as $o_project) {
				$overdues[] = array('text' => lang('c_23').' <u>'.shorter($o_project->getName(), 10).'</u> <font color="red"><small>'.lang('c_404').'</small></font>', 'icon' => 'fa-building text-blue', 'link' => get_page_base_url($o_project->getObjectURL()));
			}
		}
		
		if(logged_user()->isMember()) {
		
			if(logged_user()->isOwner()) {
				$o_members_ids = get_objects_ids($this->Users->getAllMembers());
			} else {
				$o_members_ids = array(logged_user()->getId());
			}
		
			$o_project_tasks = $this->ProjectTasks->getOverdues($o_project_ids, $o_members_ids);
		
			if(isset($o_project_tasks) && is_array($o_project_tasks) && count($o_project_tasks)) {
				foreach($o_project_tasks as $o_project_task) {
					$overdues[] = array('text' => lang('c_405').' <u>'.shorter($o_project_task->getName(), 15).'</u> <font color="red"><small>'.lang('c_154').'</small></font>', 'icon' => 'fa-tasks text-aqua', 'link' => get_page_base_url($o_project_task->getObjectURL()));
				}
			}
		
		}
		
		$overdues_count = (isset($overdues) && is_array($overdues) && count($overdues)) ? count($overdues) : 0;
	  
	  ?>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

		<li class="dropdown notifications-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-search"></i></a>
          <ul class="dropdown-menu">
		    <li class="custom-m-5"><form action="<?php echo get_page_base_url("search"); ?>" method="GET" id="_searchmenu">
				<div class="input-group input-group-sm">
                <input type="text" name="term" id="menu-search-term" class="form-control">
                    <span class="input-group-btn">
                      <button type="submit" class="btn btn-info btn-flat"><?php echo lang('c_523.4'); ?></button>
                    </span>
              </div></form></li>
		  </ul>
        </li>

		 <?php $my_started_timer = logged_user()->getMyStartedTimer();
		 if(isset($my_started_timer)): ?>
		 <li class="dropdown notifications-menu">
			 <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-clock-o"></i>
			 <span class="label label-danger"><i class="fa fa-hourglass-start"></i></span>
			</a>
			<div class="dropdown-menu">
				<div class="dropdown-details panel custom-m-0">
					<div class="list-group">
						<div class="list-group-item"> 
							<div class="clearfix">
								<span class="pull-left" title="<?php echo format_date($my_started_timer->getStartTime(), 'Y-m-d h:i a'); ?>">
									Started at<br><strong><?php echo format_date($my_started_timer->getStartTime(), 'h:i a'); ?></strong>
								</span>
								<span class="pull-right">
									<a href="javascript:void();" data-url="<?php echo get_page_base_url($my_started_timer->getEditURL()); ?>" data-toggle="commonmodal" class="btn btn-danger btn-sm" title="<?php echo lang('c_523.8'); ?>"><i class="fa fa fa-clock-o"></i> <?php echo lang('c_523.8'); ?></a>
									<a href="<?php echo get_page_base_url("dashboard/delete_timer/?ref=" . base64_encode(current_url())); ?>" class="btn btn-default btn-sm" title="<?php echo lang('c_523.9'); ?>"><i class="fa fa fa-trash"></i></a>
								</span>
							</div>
							<div class="custom-pt-5"><i class="fa fa-th-large"></i>&nbsp <a href="<?php echo get_page_base_url($my_started_timer->getProject()->getObjectURL()); ?>"><?php echo $my_started_timer->getProject()->getName(); ?></a></div>
						</div>
					</div>
				</div>
			</div>
		 </li>
		 <?php endif; ?>
		 
		 <?php $site_lang = get_site_language(); ?>

		<!-- <li class="dropdown notifications-menu">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-language"></i></a>
		<ul class="dropdown-menu">
			<li><ul class="menu">
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/arabic"); ?>"<?php echo ($site_lang == "arabic" ? ' class="custom-font-weight-bold"' : ''); ?>>Arabic</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/czech"); ?>"<?php echo ($site_lang == "czech" ? ' class="custom-font-weight-bold"' : ''); ?>>Czech</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/dutch"); ?>"<?php echo ($site_lang == "dutch" ? ' class="custom-font-weight-bold"' : ''); ?>>Dutch</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/english"); ?>"<?php echo ($site_lang == "" || $site_lang == "english" ? ' class="custom-font-weight-bold"' : ''); ?>>English</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/french"); ?>"<?php echo ($site_lang == "french" ? ' class="custom-font-weight-bold"' : ''); ?>>French</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/german"); ?>"<?php echo ($site_lang == "german" ? ' class="custom-font-weight-bold"' : ''); ?>>German</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/greek"); ?>"<?php echo ($site_lang == "greek" ? ' class="custom-font-weight-bold"' : ''); ?>>Greek</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/italian"); ?>"<?php echo ($site_lang == "italian" ? ' class="custom-font-weight-bold"' : ''); ?>>Italian</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/norwegian"); ?>"<?php echo ($site_lang == "norwegian" ? ' class="custom-font-weight-bold"' : ''); ?>>Norwegian</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/polish"); ?>"<?php echo ($site_lang == "polish" ? ' class="custom-font-weight-bold"' : ''); ?>>Polish</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/portuguese"); ?>"<?php echo ($site_lang == "portuguese" ? ' class="custom-font-weight-bold"' : ''); ?>>Portuguese</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/russian"); ?>"<?php echo ($site_lang == "russian" ? ' class="custom-font-weight-bold"' : ''); ?>>Russian</a></li>
			<li><a href="<?php echo get_page_base_url("dashboard/switch_language/spanish"); ?>"<?php echo ($site_lang == "spanish" ? ' class="custom-font-weight-bold"' : ''); ?>>Spanish</a></li>
			</ul></li>
		</ul>
		</li>		 -->

		 <!-- <li class="dropdown notifications-menu">

			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
				<i class="fa fa-life-ring"></i>
				<?php if($overdues_count > 0) : ?>
				<span class="label label-danger"><?php echo $overdues_count; ?></span>
				<?php endif; ?>
			</a>
		    
            <ul class="dropdown-menu">
				
				<?php if($overdues_count > 0) : ?>
				<li class="header"><strong><?php echo sprintf(lang('c_444'), $overdues_count); ?></strong></li>
				<li><ul class="menu">
				<?php foreach($overdues as $overdue_i) : ?>
					<li class="custom-normal-white-space"><a href="<?php echo $overdue_i['link']; ?>">
					<i class="fa <?php echo $overdue_i['icon']; ?>"></i>
					<?php echo $overdue_i['text']; ?></a></li>
				<?php endforeach; ?>
				</ul></li>
				<?php endif; ?>

            </ul>
			
          </li> -->
		  
		  <li class="dropdown notifications-menu">
	
			<?php $i_notifications = logged_user()->getMyNotifications();
			$notifications_count = (isset($i_notifications) && is_array($i_notifications) && count($i_notifications)) ? count($i_notifications) : 0;?>
		  
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
			  <?php if($notifications_count > 0) : ?>
              <span class="label label-warning"><?php echo $notifications_count; ?></span>
			  <?php endif; ?>
            </a>

            <ul class="dropdown-menu">
	
				<?php if($notifications_count > 0) : ?>
				<li class="header"><strong><?php echo sprintf(lang('c_443'), $notifications_count); ?></strong></li>
				<li><ul class="menu">
				<?php foreach($i_notifications as $i_notification) : 
				list($date_tr_class, $log_date_value) = get_date_format_array($i_notification->getCreatedAt()); ?>
				<li><a href="<?php echo get_page_base_url($i_notification->getObjectURL()); ?>">
				<i class="fa fa-asterisk fa-fw"></i> <?php echo $i_notification->getSubject(); ?>
				<small class="text-muted"><i class="fa fa-clock-o"></i> <?php echo $log_date_value; ?></small></a></li>
				<?php endforeach; ?>
				</ul></li>
				<li class="footer">
					<a class="text-center" href="<?php echo get_page_base_url('notifications'); ?>">
						<?php echo lang('c_406'); ?>
					</a>
				</li>
				<?php endif; ?>

            </ul>
          </li>
          
 
          <li class="dropdown user user-menu">

            <a href="#" class="dropdown-toggle" data-toggle="dropdown">

              <img src="<?php echo logged_user()->getAvatar(); ?>" class="user-image" alt="<?php echo logged_user()->getName(); ?>">
              <span class="hidden-xs"><?php echo logged_user()->getName(); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo logged_user()->getAvatar(); ?>" class="img-circle" alt="<?php echo logged_user()->getName(); ?>">
                <p>
                  <?php echo logged_user()->getName(); ?> - <?php if(logged_user()->isOwner()) : ?><?php echo lang('c_235'); ?><?php 
					   elseif(logged_user()->isAdmin()) : ?><?php echo lang('c_221'); ?><?php 
					   elseif(logged_user()->isMember()) : ?><?php echo lang('c_28'); ?><?php 
					   else : ?><?php echo lang('c_29'); ?><?php endif; ?>
                  <small><?php echo lang('c_523.112'); ?> <?php echo format_date(logged_user()->getCreatedAt(), 'M. Y'); ?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="javascript:void();" data-url="<?php echo get_page_base_url(logged_user()->getEditProfileURL()); ?>" data-toggle="commonmodal" class="btn btn-default btn-flat"><?php echo lang('c_407'); ?></span></a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo base_url();?>access/logout<?php echo '?ref='.base64_encode(current_url()); ?>" class="btn btn-default btn-flat"><?php echo lang('c_408'); ?></span></a>
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
			<a href="<?php echo get_page_base_url('dashboard'); ?>"><i class="fa fa-dashboard fa-fw"></i> <span><?php echo lang('c_88'); ?></span></a>
		</li>

		<li<?php echo ($this->router->fetch_class() == 'projects' ? ' class="active"' : ''); ?>>
			<a href="<?php echo get_page_base_url('projects'); ?>"><i class="fa fa-building fa-fw"></i> <span><?php echo lang('c_278'); ?></span></a>
		</li>

		<li<?php echo ($this->router->fetch_class() == 'activity' ? ' class="active"' : ''); ?>>
			<a href="<?php echo get_page_base_url('activity'); ?>"><i class="fa fa-clock-o fa-fw"></i> <span><?php echo lang('c_22'); ?></span></a>
		</li>

		<?php if(logged_user()->isMember()) : ?>
	
		<li<?php echo ($this->router->fetch_class() == 'mywork' ? ' class="active"' : ''); ?>>
			<a href="<?php echo get_page_base_url('mywork'); ?>"><i class="fa fa-check-circle fa-fw"></i> <span><?php echo lang('c_182'); ?></span></a>
		</li>					

		<li<?php echo ($this->router->fetch_class() == 'timesheet' ? ' class="active"' : ''); ?>>
			<a href="<?php echo get_page_base_url('timesheet'); ?>"><i class="fa fa-calendar-check-o fa-fw"></i> <span><?php echo lang('c_302'); ?></span></a>
		</li>
		
		<?php endif; ?>

		<li<?php echo ($this->router->fetch_class() == 'calendar' ? ' class="active"' : ''); ?>>
			<a href="<?php echo get_page_base_url('calendar'); ?>"><i class="fa fa-calendar fa-fw"></i> <span><?php echo lang('c_409'); ?></span></a>
		</li>					

		<li<?php echo ($this->router->fetch_class() == 'notifications' ? ' class="active"' : ''); ?>>
			<a href="<?php echo get_page_base_url('notifications'); ?>"><i class="fa fa-bell-o fa-fw"></i> <span><?php echo lang('c_210'); ?></span></a>
		</li>

		<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>

		<!-- <li class="treeview<?php echo ($this->router->fetch_class() == 'tickets' || $this->router->fetch_class() == 'departments'? ' active ' : ''); ?>">
		   <a href="#"><i class="fa fa-ticket fa-fw"></i> <span><?php echo lang('c_295'); ?></span>
			<span class="pull-right-container">
				<i class="fa fa-angle-left pull-right"></i>
			  </span>
		   </a>
		   <ul class="treeview-menu">
				<li<?php echo ($this->router->fetch_class() == 'tickets' ? ' class="active"' : ''); ?>>
					<a href="<?php echo get_page_base_url('tickets'); ?>"> <span><?php echo lang('c_295'); ?></span></a>
				</li>					
	
				<li<?php echo ($this->router->fetch_class() == 'departments' ? ' class="active"' : ''); ?>>
					<a href="<?php echo get_page_base_url('departments'); ?>"> <span><?php echo lang('c_113'); ?></span></a>
				</li>					
		    </ul>
			
		</li> -->

		<li class="treeview<?php echo ($this->router->fetch_class() == 'leads' || $this->router->fetch_class() == 'leadssources' || $this->router->fetch_class() == 'leadsstatuses' || $this->router->fetch_class() == 'forms' ? ' active ' : ''); ?>">
		   <a href="#"><i class="fa fa-briefcase fa-fw"></i> <span><?php echo lang('c_455'); ?></span>
			<span class="pull-right-container">
				<i class="fa fa-angle-left pull-right"></i>
			  </span>
		   </a>
		   <ul class="treeview-menu">
				<li<?php echo ($this->router->fetch_class() == 'leads' ? ' class="active"' : ''); ?>>
					<a href="<?php echo get_page_base_url('leads'); ?>"> <span><?php echo lang('c_455'); ?></span></a>
				</li>					

				<li<?php echo ($this->router->fetch_class() == 'forms' ? ' class="active"' : ''); ?>>
					<a href="<?php echo get_page_base_url('forms'); ?>"> <span><?php echo lang('c_458'); ?></span></a>
				</li>					
	
				<li<?php echo ($this->router->fetch_class() == 'leadssources' ? ' class="active"' : ''); ?>>
					<a href="<?php echo get_page_base_url('leadssources'); ?>"> <span><?php echo lang('c_456'); ?></span></a>
				</li>					

				<li<?php echo ($this->router->fetch_class() == 'leadsstatuses' ? ' class="active"' : ''); ?>>
					<a href="<?php echo get_page_base_url('leadsstatuses'); ?>"> <span><?php echo lang('c_457'); ?></span></a>
				</li>					

		    </ul>
			
		</li>

		<?php elseif(!logged_user()->isMember()) : ?>

			<li<?php echo ($this->router->fetch_class() == 'tickets' ? ' class="active"' : ''); ?>>
				<a href="<?php echo get_page_base_url('tickets'); ?>"><i class="fa fa-ticket fa-fw"></i> <span><?php echo lang('c_295'); ?></span></a>
			</li>					

		<?php endif; ?>

		<?php if(logged_user()->isOwner()) : ?>

			<li class="treeview<?php echo ($this->router->fetch_class() == 'people' || $this->router->fetch_class() == 'archive' ? ' active ' : ''); ?>">
				<a href="#"><i class="fa fa-users fa-fw"></i> <span><?php echo lang('c_27'); ?></span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
			
					<li<?php echo ($this->router->fetch_method() == 'members' ? ' class="active"' : ''); ?>>
						<a href="<?php echo get_page_base_url('people/members'); ?>"> <span><?php echo lang('c_200'); ?></span></a>
					</li>					
					<li<?php echo ($this->router->fetch_method() == 'clients' ? ' class="active"' : ''); ?>>
						<a href="<?php echo get_page_base_url('people/clients'); ?>"<?php echo ($this->router->fetch_class() == 'people' && $this->router->fetch_method() == 'clients' ? ' class="waves-effect active"' : ''); ?>> <span><?php echo lang('c_199'); ?></span></a>
					</li>					

					<li<?php echo ($this->router->fetch_class() == 'archive' ? ' class="active"' : ''); ?>>
						<a href="<?php echo get_page_base_url('archive'); ?>"> <span><?php echo lang('c_26'); ?></span></a>
					</li>
				</ul>				
			</li>

			<!-- <li<?php echo ($this->router->fetch_class() == 'invoices' ? ' class="active"' : ''); ?>>
				<a href="<?php echo get_page_base_url('invoices'); ?>"><i class="fa fa-money fa-fw"></i> <span><?php echo lang('c_172'); ?></span></a>
			</li>					

			<li<?php echo ($this->router->fetch_class() == 'estimates' ? ' class="active"' : ''); ?>>
				<a href="<?php echo get_page_base_url('estimates'); ?>"><i class="fa fa-files-o fa-fw"></i> <span><?php echo lang('c_150'); ?></span></a>
			</li>					 -->

			<!-- <li<?php echo ($this->router->fetch_class() == 'transactions' ? ' class="active"' : ''); ?>>
				<a href="<?php echo get_page_base_url('transactions'); ?>"><i class="fa fa-book fa-fw"></i> <span><?php echo lang('c_375'); ?></span></a>
			</li>						 -->
								
		<?php elseif(!logged_user()->isMember()) : ?>

			<li<?php echo ($this->router->fetch_class() == 'invoices' ? ' class="active"' : ''); ?>>
				<a href="<?php echo get_page_base_url('invoices'); ?>"><i class="fa fa-money fa-fw"></i> <span><?php echo lang('c_172'); ?></span></a>
			</li>					
		
			<li<?php echo ($this->router->fetch_class() == 'estimates' ? ' class="active"' : ''); ?>>
				<a href="<?php echo get_page_base_url('estimates'); ?>"><i class="fa fa-files-o fa-fw"></i> <span><?php echo lang('c_150'); ?></span></a>
			</li>					
		
		<?php endif; ?>
		
		<?php if(logged_user()->isOwner()) : ?>

			<li class="treeview<?php echo ($this->router->fetch_class() == 'reports' ? ' active ' : ''); ?>">
			<a href="#"><i class="fa fa-bar-chart fa-fw"></i> <span><?php echo lang('c_411'); ?></span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu">
					
				<li<?php echo ($this->router->fetch_class() == 'reports' && $this->router->fetch_method() == 'index' ? ' class="active"' : ''); ?>>
					<a href="<?php echo get_page_base_url('reports/index'); ?>"> <span><?php echo lang('c_303'); ?></span></a>
				</li>					
			
				<li<?php echo ($this->router->fetch_method() == 'time_tracking' ? ' class="active"' : ''); ?>>
					<a href="<?php echo get_page_base_url('reports/time_tracking'); ?>"> <span><?php echo lang('c_311'); ?></span></a>
				</li>					
			
				</ul>
			
			</li>
			
			<li<?php echo ($this->router->fetch_class() == 'noticeboard' ? ' class="active"' : ''); ?>>
				<a href="<?php echo get_page_base_url('noticeboard'); ?>"><i class="fa fa-bullhorn fa-fw"></i> <span><?php echo lang('c_202'); ?></span></a>
			</li>

			<!-- <li<?php echo ($this->router->fetch_class() == 'settings' ? ' class="active"' : ''); ?>>
				<a href="<?php echo get_page_base_url('settings'); ?>"<?php echo ($this->router->fetch_class() == 'settings' ? ' class="active"' : ''); ?>><i class="fa fa-gear fa-fw"></i> <span><?php echo lang('c_317'); ?></span></a>
			</li>					

			<li<?php echo ($this->router->fetch_class() == 'tools' ? ' class="active"' : ''); ?>>
				<a href="<?php echo get_page_base_url('tools'); ?>"<?php echo ($this->router->fetch_class() == 'tools' ? ' class="active"' : ''); ?>><i class="fa fa-wrench fa-fw"></i> <span><?php echo lang('c_516'); ?></span></a>
			</li>					

			<li<?php echo ($this->router->fetch_class() == 'trash' ? ' class="active"' : ''); ?>>
				<a href="<?php echo get_page_base_url('trash'); ?>"><i class="fa fa-trash-o fa-fw"></i> <span><?php echo lang('c_378'); ?></span></a>
			</li>					 -->

		<?php endif; ?>

      </ul>

	  <?php if(logged_user()->isOwner()) : 
		$CI =& get_instance();
		$target_sources = $CI->TargetSources->findById(get_target_source_id());
	 	$packages = get_packages();
	  ?>
 
	  <?php endif; ?>

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
				
		<?php if(isset($is_project_dashboard) && $is_project_dashboard) : ?>

		<div class="box box-solid">
	
		<div class="box-header with-border">
		  <i class="fa fa-building"></i>
		  <h3 class="box-title"><?php echo $project->getName(); ?>
		  <small class="text-muted"><?php echo $project->getCreatedForCompany()->getName(); ?></small>
		  </h3>

		  <div class="box-tools pull-right">
			<div class="btn-group">
				<?php if(logged_user()->isOwner() || (logged_user()->isAdmin() && logged_user()->isProjectUser($project)) ) : ?>
					<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><?php echo lang('c_234'); ?> <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" role="menu">
						<?php if(!$project->isCompleted()) : ?><li><a href="javascript:void();" data-url="<?php echo get_page_base_url($project->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_248'); ?></a></li>
						<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($project->getManagePeopleURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_254'); ?></a></li>
						<li class="divider"></li>
						<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($project->getCompleteURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_281'); ?></a></li><?php else : ?>
						<li><a href="<?php echo get_page_base_url($project->getReopenURL()); ?>"><?php echo lang('c_282'); ?></a></li><?php endif; ?>
					</ul>
				<?php endif; ?>
				<?php if(isset($my_started_timer)): ?>
					<?php if ($my_started_timer->getProject()->getId() == $project->getId()): ?>
						<a href="javascript:void();" data-url="<?php echo get_page_base_url($my_started_timer->getEditURL()); ?>" data-toggle="commonmodal" class="btn btn-danger btn-sm" title="<?php echo lang('c_523.8'); ?>"><i class="fa fa fa-clock-o"></i> <?php echo lang('c_523.8'); ?></a>
					<?php else: ?>
						<button class="btn btn-primary btn-sm" title="<?php echo lang('c_523.7'); ?>" disabled><i class="fa fa fa-clock-o"></i> <?php echo lang('c_523.7'); ?></button>
					<?php endif; ?>
				<?php elseif(logged_user()->isMember()): ?>
					<a href="<?php echo get_page_base_url("dashboard/start_timer/" . $project->getId() . "/?ref=" . base64_encode(current_url())); ?>" class="btn btn-primary btn-sm" title="<?php echo lang('c_523.7'); ?>"><i class="fa fa fa-clock-o"></i> <?php echo lang('c_523.7'); ?></a>
				<?php endif;?>
			</div>
		  </div><br>
		  
		</div>
		
		<div class="box-body">
		
		
		<div class="row">
			
			<?php if(isset($active_tab) && $active_tab == "overview") : ?>
			
			<div class="col-md-3">
	
			  <div class="box box-danger">
				<div class="box-body box-profile">
					  
				  <p><strong><i class="fa fa-file-text-o margin-r-5"></i> <?php echo lang('c_48'); ?></strong><br>
				  <span class="more"><?php echo $project->getDescription(); ?></span></p>		
		
				  <ul class="list-group list-group-unbordered">
					<?php $project_label = $project->getLabel();
					if(isset($project_label) && $project_label->getIsActive()) : ?>
					<li class="list-group-item">
						<b><?php echo lang('c_440'); ?></b> <span class="label pull-right" style="background-color:#<?php echo $project_label->getBgColorHex(); ?>;"><?php echo $project_label->getName(); ?></span>
					</li><?php endif; ?>

					<li class="list-group-item">
					  <b><?php echo lang('c_188'); ?></b> <span class="pull-right"><?php echo $project->getCreatedByName(true); ?></span>
					</li>
					
					<li class="list-group-item">
					  <b><?php echo lang('c_441'); ?></b> <span class="pull-right"><?php echo format_date($project->getCreatedAt()); ?></span>
					</li>

					<?php
					$total_project_tasks = $project->getTasksCount(true);
					$completed_project_tasks = $total_project_tasks - $project->getTasksCount();
					$completed_percentage = $total_project_tasks > 0 ? round(($completed_project_tasks/$total_project_tasks)*100) : 0;
					?>
	
					<li class="list-group-item">
					  <b><?php echo lang('c_523.23'); ?></b> 
						<span class="pull-right"><?php echo $completed_project_tasks; ?>/<?php echo $total_project_tasks; ?></span>
						<div class="progress custom-m-0 progress-sm">
							<div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="<?php echo $completed_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $completed_percentage; ?>%"></div>
						</div>
					</li>
		
					<li class="list-group-item">
					  <b><?php echo lang('c_192'); ?></b> <span class="pull-right"><?php echo format_date($project->getStartDate(), 'j M. Y'); ?></span>
					</li>

					<li class="list-group-item">
					  <b><?php echo lang('c_238'); ?></b> <span class="pull-right"><?php echo format_date($project->getDueDate(), 'j M. Y'); ?>
					  <?php if($project->getDueDate() < time()) : ?>&nbsp; <b class="custom-color-red"><?php echo lang('c_154'); ?></b><?php endif; ?></span>
					</li>
							  
				  </ul>
		
				  <p><strong><i class="fa fa-users margin-r-5"></i> <?php echo lang('c_27'); ?></strong><br><?php 
				  $involved_users = $project->getUsers(false);
				  if(isset($involved_users) && is_array($involved_users) && count($involved_users)) : ?>
				  <div class="item">
				  <?php foreach($involved_users as $involved_user) : ?>
				   <img src="<?php echo $involved_user->getAvatar(); ?>" class="img-circle-sm" title="<?php echo $involved_user->getName(); ?>">
				  <?php endforeach; ?>
				  </div>
				  <?php else : ?><?php echo lang('c_153'); ?>
				  <?php endif; ?></p>
				
				</div>
	
			  </div>
	
			</div>
			
			<?php endif; ?>
			
			<div class="<?php echo (isset($active_tab) && $active_tab == "overview" ? 'col-md-9' : 'col-md-12'); ?>">
	
			  <div class="nav-tabs-custom">
	
				<ul class="nav nav-tabs" role="tablist">
				  <li<?php echo (isset($active_tab) && $active_tab == "overview" ? ' class="active"': ''); ?>><a href="<?php echo get_page_base_url($project->getObjectURL()); ?>" role="tab" class="custom-cursor-pointer"><?php echo lang('c_413'); ?></a></li>
				  <li<?php echo (isset($active_tab) && $active_tab == "discussions" ? ' class="active"': ''); ?>><a href="<?php echo get_page_base_url($project->getObjectURL('discussions')); ?>" role="tab" class="custom-cursor-pointer"><?php echo lang('c_263'); ?></a></li>
				  <li<?php echo (isset($active_tab) && ($active_tab == "task_lists" ||  $active_tab == "tasks")  ? ' class="active"': ''); ?>><a href="<?php echo get_page_base_url($project->getObjectURL('task_lists')); ?>" role="tab" class="custom-cursor-pointer"><?php echo lang('c_286'); ?></a></li>
				  <li<?php echo (isset($active_tab) && $active_tab == "files" ? ' class="active"': ''); ?>><a href="<?php echo get_page_base_url($project->getObjectURL('files')); ?>" role="tab" class="custom-cursor-pointer"><?php echo lang('c_267'); ?></a></li>
				  <li<?php echo (isset($active_tab) && $active_tab == "tickets" ? ' class="active"': ''); ?>><a href="<?php echo get_page_base_url($project->getObjectURL('tickets')); ?>" role="tab" class="custom-cursor-pointer"><?php echo lang('c_295'); ?></a></li>
				  <li<?php echo (isset($active_tab) && $active_tab == "gantt" ? ' class="active"': ''); ?>><a href="<?php echo get_page_base_url($project->getObjectURL('gantt')); ?>" role="tab" class="custom-cursor-pointer"><?php echo lang('c_414'); ?></a></li>
				  <?php if(logged_user()->isMember() || (!logged_user()->isMember() && $project->getIsTimelogVisible())) : ?><li<?php echo (isset($active_tab) && $active_tab == "timesheet" ? ' class="active"': ''); ?>><a href="<?php echo get_page_base_url($project->getObjectURL('timesheet')); ?>" role="tab" class="custom-cursor-pointer"><?php echo lang('c_302'); ?><?php echo (!$project->getIsTimelogVisible() ? ' <i class="fa fa-lock"></i>' : ''); ?></a></li><?php endif; ?>
				  <?php if(logged_user()->isOwner() || !logged_user()->isMember()) : ?><li<?php echo (isset($active_tab) && $active_tab == "invoices" ? ' class="active"': ''); ?>><a href="<?php echo get_page_base_url($project->getObjectURL('invoices')); ?>" role="tab" class="custom-cursor-pointer"><?php echo lang('c_172'); ?></a></li><?php endif; ?>
				  <li<?php echo (isset($active_tab) && $active_tab == "notes" ? ' class="active"': ''); ?>><a href="<?php echo get_page_base_url($project->getObjectURL('notes')); ?>" role="tab" class="custom-cursor-pointer"><?php echo lang('c_284'); ?></a></li>
				  <li<?php echo (isset($active_tab) && $active_tab == "activity" ? ' class="active"': ''); ?>><a href="<?php echo get_page_base_url($project->getObjectURL('activity')); ?>" role="tab" class="custom-cursor-pointer"><?php echo lang('c_22'); ?></a></li>
				</ul>
				
				<div class="tab-content custom-p-15">
			
				  <div class="tab-pane fade in active">
	
					<?php echo (isset($content_for_layout) ? $content_for_layout : ''); ?>
				 
				  </div>
			   
				</div>
			
			  </div>
			
			</div>
		
		</div>
		
		</div>
		
		</div>
		
		<?php else : ?>				
		
		<?php echo (isset($content_for_layout) ? $content_for_layout : ''); ?>
		
		<?php endif; ?>
					
    </section>

  </div>

  <footer class="main-footer">
	  Copyright <?php echo date("Y"); ?> &copy; <strong><?php echo config_option("site_name", "Project Management System"); ?>.</strong> All rights reserved.
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
$(document).ready(function(){
	$(".slimscrollright-msg").slimScroll({height:"100%",position:"right",start:"bottom",size:"5px"});
	$(".slimscrollright-files").slimScroll({height:"100%",position:"right",size:"5px"});
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
</script>

<?php echo (isset($footer_for_layout) ? $footer_for_layout : ''); ?>

</body>
</html>