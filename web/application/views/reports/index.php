<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", lang('c_303'));

tpl_assign("header_for_layout", '
<link href="'.base_url('public/assets/vendor/datetimepicker/jquery.datetimepicker.css').'" rel="stylesheet">
');	

tpl_assign("footer_for_layout", '
<script type="text/javascript" src="'.base_url('public/assets/vendor/datetimepicker/jquery.datetimepicker.js').'"></script>
<script type="text/javascript" src="'.base_url('public/assets/vendor/chart.min.js').'"></script>
<script type="text/javascript">

(function ($) {
	"use strict";
  $(document).ready(function(){
    var ctx = document.getElementById("tileChart");
    var myChart = new Chart(ctx, {
      type: "bar",
      data: {
        labels: ['.$labels.'],
        datasets: ['.(($line2 != '0') ? '{
          label: "'.lang('c_304').'",
          backgroundColor: "rgba(237,85,101,0.6)",
          borderColor: "rgba(237,85,101,1)",
          pointBorderColor: "rgba(0,0,0,0)",
          pointBackgroundColor: "#ffffff",
          pointHoverBackgroundColor: "rgba(237, 85, 101, 0.5)",
          pointHitRadius: 25,
          pointRadius: 1,
          data: ['.$line2.'],
        },' : '').'{
          label: "'.lang('c_305').'",
          backgroundColor: "rgba(46,204,113,0.6)",
          borderColor: "rgba(46,204,113,1)",
          pointBorderColor: "rgba(0,0,0,0)",
          pointBackgroundColor: "#ffffff",
          pointHoverBackgroundColor: "rgba(79, 193, 233, 1)",
          pointHitRadius: 25,
          pointRadius: 1,
          data: ['.$line1.'],
      }]
      },
      options: {
        tooltips:{
          xPadding: 10,
          yPadding: 10,
          cornerRadius:2,
          mode: "label",
          multiKeyBackground: "rgba(0,0,0,0.2)"
        },
        legend:{
          display: false
        },
        scales: {
          
          yAxes: [{
            display: true,
            gridLines:[{
                        drawOnChartArea: false,
            }],
            ticks: {
                        fontColor: "#A4A5A9",
                        fontFamily: "Open Sans",
                        fontSize: 11,
                        beginAtZero:true,
                        maxTicksLimit:6,
                    }
          }],
          xAxes: [{
            display: true,
            ticks: {
                        fontColor: "#A4A5A9",
                        fontFamily: "Open Sans",
                        fontSize: 11,
                  }
          }]
        }
      }
    });
    $(".datepicker").datetimepicker({format:\'Y-m-d\', timepicker:false});
  });
})(jQuery);
</script>
');	

?>
<form action="" method="POST" id="_reports">
<div class="row">     
  <div class="col-md-3">
	<div class="form-group">
	  <input class="form-control datepicker" name="start" id="start" type="text" value="<?=$stats_start_short;?>" placeholder="<?php echo lang('c_192'); ?>" readonly /> 
	</div>
  </div>
  <div class="col-md-3">
	<div class="form-group">
	  <input class="form-control datepicker" name="end" id="end" type="text" value="<?=$stats_end_short;?>" placeholder="<?php echo lang('c_198'); ?>" readonly />
	</div>
  </div>
  <div class="col-md-2">
	<div class="form-group">
	  <input class="btn btn-primary" name="send" type="submit" value="<?php echo lang('c_306'); ?>" required />
	</div>
  </div>
</div>
</form>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-bar-chart"></i>
	  <h3 class="box-title"><?php echo lang('c_303'); ?></h3>
	</div>
	
	<div class="box-body">
	  
	  <div class="row">
	
		 <div class="col-lg-4 col-md-12">
			  <h5><?php echo lang('c_307'); ?></h5>
			  <h4 class="custom-pb-15"><?=$stats_start;?> &mdash; <?=$stats_end;?></h4>
		 </div>
	 
		 <div class="col-lg-8 col-md-12">
			<div class="col-sm-4 text-right">
				<h4><?php echo lang('c_308'); ?></h4>
				<h2><?php echo number_format($totalIncomeForYear, 2); ?></h2>
			</div>
			<div class="col-sm-4 text-right">
				<h4><?php echo lang('c_309'); ?></h4>
				<h2 class="custom-color-red"><?php echo number_format($totalExpenses, 2); ?></h2>
			</div>
			<div class="col-sm-4 text-right tile-positive">
				<h4><?php echo lang('c_310'); ?></h4>
				<h2 class="<?php echo ($totalProfit >= 0 ? 'custom-color-green' : 'custom-color-red'); ?>"><?php echo number_format($totalProfit, 2);?></h2>
			</div>
		 </div>
	 	
	</div>
	
	<p>&nbsp;</p>
			
	<div class="row">
	
		<div class="col-lg-12">
			  <canvas id="tileChart" width="auto" height="80"></canvas>
		</div>
	
	</div>
	
</div>
</div>