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

    <link href="<?php echo base_url();?>public/assets/bower_components/bootstrap/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link href="<?php echo base_url();?>public/assets/bower_components/font-awesome/css/font-awesome.min.css"
        rel="stylesheet">
    <link href="<?php echo base_url();?>public/assets/bower_components/Ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>public/assets/css/custom.css" rel="stylesheet">
    <link href="<?php echo base_url();?>public/assets/css/landing.css" rel="stylesheet">
    <link href="<?php echo base_url();?>public/assets/dist/css/AdminLTE.min.css" rel="stylesheet">

    <?php $site_theme = get_site_theme(); ?>
    <link rel="stylesheet" href="<?php echo base_url();?>public/assets/dist/css/skins/skin-blue.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <link href="<?php echo base_url();?>public/assets/vendor/nprogress/nprogress.css" rel="stylesheet">
    <link href="<?php echo base_url();?>public/assets/vendor/jquery-ui/jquery-ui.min.css" rel="stylesheet">


    <link rel='shortcut icon' type="image/png" href="<?php echo base_url()."favicon.ico" ; ?>" />

</head>

<body class="landing">

    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>

    <div class="wrapper">

        <header class="header">
            <span class="logo">
                <p>Klipspruit Mining Execution Systems</p>
            </span>
            <?php if(!(logged_user() instanceof User)) { ?>
            <a class="btn" href="access/login">
                <img src='<?php echo base_url();?>public/assets/images/landing/icons8-user-male-30.png' />
                <span>Log In</span>
            </a>
            <?php } else {?>
            <a class="btn" href="access/logout">
                <img src='<?php echo base_url();?>public/assets/images/landing/icons8-user-male-30.png' />
                <span>Log Out</span>
            </a>
            <?php }?>

            <button class="btn">
                <img src='<?php echo base_url();?>public/assets/images/landing/icons8-home-50.png' />
                <span>Home</span>
            </button>
            <button class="btn">
                <img src='<?php echo base_url();?>public/assets/images/landing/icons8-settings-24.png' />
            </button>
        </header>

        <div class="landing-body">
		<?php if((logged_user() instanceof User)) {?>
            <div class="control-bars">
                <a class="btn">
                    <div class="img">
                        <img src='<?php echo base_url();?>public/assets/images/landing/icons8-increase-100.png' />
                    </div>
                    <div class="text" style="max-width : 80px;">MES Reporting</div>
                </a>
                <button class="btn">
                    <div class="img">
                        <img src='<?php echo base_url();?>public/assets/images/landing/icons8-circuit-64.png' />
                    </div>
                    <div class="text" style="max-width : 120px;">Mining Execution Systems (MES)</div>
                </button>
                <button class="btn">
                    <div class="img">
                        <img src='<?php echo base_url();?>public/assets/images/landing/icons8-engineer-50.png' />
                    </div>
                    <div class="text" style="max-width : 80px;">Control Room</div>
                </button>
                <button class="btn">
                    <div class="img">
                        <img src='<?php echo base_url();?>public/assets/images/landing/icons8-learning-50.png' />
                    </div>
                    <div class="text" style="max-width : 120px;">Analysis & Improvement</div>
                </button>
                <button class="btn">
                    <div class="img">
                        <img src='<?php echo base_url();?>public/assets/images/landing/icons8-digger-80.png' />
                    </div>
                    <div class="text" style="max-width : 120px;"> Mine Operation System (MOS)</div>
                </button>
                <button class="btn">
                    <div class="img">
                        <img src='<?php echo base_url();?>public/assets/images/landing/icons8-edit-file-64.png' />
                    </div>
                    <div class="text">Weighbridge Interface</div>
                </button>
                <button class="btn">
                    <div class="img">
                        <img src='<?php echo base_url();?>public/assets/images/landing/icons8-system-task-80.png' />
                    </div>
                    <div class="text" style="max-width : 120px;">Mining Systems Stock Control</div>
                </button>
                <a class="btn" href="pms">
                    <div class="img">
                        <img src='<?php echo base_url();?>public/assets/images/landing/icons8-helping-hand-26.png' />
                    </div>
                    <div class="text">MES COLLABORATION</div>
                </a>
                <button class="btn">
                    <div class="img"><img
                            src='<?php echo base_url();?>public/assets/images/landing/icons8-system-task-80.png' />
                    </div>
                    <div class="text">Log Service Request</div>
                </button>
            </div>
			<?php } ?>
            <img src='<?php echo base_url();?>public/assets/images/Mine_bg.jpg' class="bg-img" />
        </div>


    </div>

    <?php $gl_success = isset($success) ? $success : $this->session->flashdata('success'); ?>
    <?php if(!empty($gl_success)) :?>
    <div class="notify success"><?php echo $gl_success; ?></div>
    <?php endif; ?>

    <?php $gl_error = isset($error) ? $error : $this->session->flashdata('error'); ?>
    <?php if(!empty($gl_error)) :?>
    <div class="notify error"><?php echo $gl_error; ?></div>
    <?php endif; ?>

    <div class="modal fade" id="common-modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
        data-keyboard="false"></div>

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

    <script src="<?php echo base_url();?>public/assets/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js">
    </script>

    <script src="<?php echo base_url();?>public/assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js">
    </script>

    <script src="<?php echo base_url();?>public/assets/bower_components/chart.js/Chart.js"></script>


    <script type="text/javascript">
    $(document).ready(function() {
        $(".slimscrollright-msg").slimScroll({
            height: "100%",
            position: "right",
            start: "bottom",
            size: "5px"
        });
        $(".slimscrollright-files").slimScroll({
            height: "100%",
            position: "right",
            size: "5px"
        });
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

</body>

</html>