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

    <link href="<?php echo base_url();?>public/assets/bootstrap-4.0.0-dist/css/bootstrap.min.css" rel="stylesheet">
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

            <div class="container-fluid carousel-landing">
                <div id="carouselBody" class="carousel slide" data-ride="carousel" data-interval="9000">
                    <div class="carousel-inner row w-100 mx-auto" role="listbox">
                        <?php 
                        $cnt = 0;
                        foreach($widgets as $widget) : ?>
                        <div class="carousel-item col-xs-12 col-md-3 <?php if($cnt == 0) echo "active"; ?>">
                            <div class="panel panel-default">
                                <h4><?php echo $widget->getTitle(); ?></h4>
                                <p><?php echo $widget->getDescription(); ?></p>
                                <div class="panel-thumbnail">
                                    <img class="img-fluid mx-auto d-block" src="<?php echo $widget->getPhotoUrl(); ?>" alt="slide 1">
                                    <a href="#" class="thumb">
                                        Read More
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php $cnt ++; endforeach; ?>
                    </div>
                    <a class="carousel-control-prev" href="#carouselBody" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next text-faded" href="#carouselBody" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
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

    <script src="<?php echo base_url();?>public/assets/bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>

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


    $('#carouselBody').on('slide.bs.carousel', function(e) {
        var $e = $(e.relatedTarget);
        var idx = $e.index();
        var itemsPerSlide = 4;
        var totalItems = $('.carousel-item').length;

        if (idx >= totalItems - (itemsPerSlide - 1)) {
            var it = itemsPerSlide - (totalItems - idx);
            for (var i = 0; i < it; i++) {
                // append slides to end
                if (e.direction == "left") {
                    $('.carousel-item').eq(i).appendTo('.carousel-inner');
                } else {
                    $('.carousel-item').eq(0).appendTo('.carousel-inner');
                }
            }
        }
    });


    $('#carouselBody').carousel({
        interval: 4000
    });


    $(document).ready(function() {
        /* show lightbox when clicking a thumbnail */
        $('a.thumb').click(function(event) {
            event.preventDefault();
            var content = $('.modal-body');
            content.empty();
            var title = $(this).attr("title");
            $('.modal-title').html(title);
            content.html($(this).html());
            $(".modal-profile").modal({
                show: true
            });
        });

    });
    </script>

</body>

</html>