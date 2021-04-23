<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", "Dashboard"); 

$year = date('Y', time());
$start = $year."-01-01";
$end = $year."-12-31";

$stats_start = date('d M, Y', human_to_unix($start.' 00:00'));
$stats_end = date('d M, Y', human_to_unix($end.' 00:00'));

$stats_payments = $this->IOrders->getPaymentsStatsFor($start, $end);
	
$line1 = '';
$labels = '';
$totalIncomeForYear = 0;

$untilMonth = ($end) ? date_format(date_create_from_format('Y-m-d', $end), 'm') : 12;	
for ($i = 01; $i <= $untilMonth; $i++) {

	$monthname = date_format(date_create_from_format('Y-m-d', '2016-'.$i.'-01'), 'M');
	$num = "0";
	$num2 = "0";

	foreach ($stats_payments as $stats_payment) {
		$act_month = explode("-", $stats_payment->stats_date); 
		if($act_month[1] == $i){  
			$num = ($stats_payment->summary - $stats_payment->fee_amount);
			break; 
		} // if
	} // foreach
	
	$i = sprintf("%02.2d", $i);
	$labels .= '"'.$monthname.'"';
	$line1 .= $num;
				
	$totalIncomeForYear = $totalIncomeForYear+$num;

	if($i != "12"){ 
		$line1 .= ","; $labels .= ",";
	} // if

} // for

$all_stats_payments = $this->IOrders->getPaymentsAllStats();
if(isset($all_stats_payments) && is_array($all_stats_payments) && count($all_stats_payments) == 1) {
	$total_earned = ($all_stats_payments[0]->summary - $all_stats_payments[0]->fee_amount);
} else {
	$total_earned = 0;
}

$active_subscription = $this->TargetSources->countActiveOnly();
$expired_subscription = $this->TargetSources->countInactiveOnly();;
$subscriptions = $active_subscription + $expired_subscription;

tpl_assign("footer_for_layout", "<script>

  var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
  var salesChart       = new Chart(salesChartCanvas);

  var salesChartData = {
    labels  : [".$labels."],
    datasets: [
      {
        label               : 'Earned',
        fillColor           : 'rgba(60,141,188,0.9)',
        strokeColor         : 'rgba(60,141,188,0.8)',
        pointColor          : '#3b8bba',
        pointStrokeColor    : 'rgba(60,141,188,1)',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(60,141,188,1)',
        data                : [".$line1."]
      }
    ]
  };

  var salesChartOptions = {
    // Boolean - If we should show the scale at all
    showScale               : true,
    // Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines      : false,
    // String - Colour of the grid lines
    scaleGridLineColor      : 'rgba(0,0,0,.05)',
    // Number - Width of the grid lines
    scaleGridLineWidth      : 1,
    // Boolean - Whether to show horizontal lines (except X axis)
    scaleShowHorizontalLines: true,
    // Boolean - Whether to show vertical lines (except Y axis)
    scaleShowVerticalLines  : true,
    // Boolean - Whether the line is curved between points
    bezierCurve             : true,
    // Number - Tension of the bezier curve between points
    bezierCurveTension      : 0.3,
    // Boolean - Whether to show a dot for each point
    pointDot                : false,
    // Number - Radius of each point dot in pixels
    pointDotRadius          : 4,
    // Number - Pixel width of point dot stroke
    pointDotStrokeWidth     : 1,
    // Number - amount extra to add to the radius to cater for hit detection outside the drawn point
    pointHitDetectionRadius : 20,
    // Boolean - Whether to show a stroke for datasets
    datasetStroke           : true,
    // Number - Pixel width of dataset stroke
    datasetStrokeWidth      : 2,
    // Boolean - Whether to fill the dataset with a color
    datasetFill             : true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio     : true,
    // Boolean - whether to make the chart responsive to window resizing
    responsive              : true
  };

  // Create the line chart
  salesChart.Line(salesChartData, salesChartOptions);

</script>");

?>

<div class="row">
	
	<div class="col-md-3 col-sm-6 col-xs-12">
	  <div class="info-box">
		<span class="info-box-icon bg-aqua"><i class="fa fa-flag"></i></span>
		<div class="info-box-content">
		  <span class="info-box-text">Total Subs</span>
		  <span class="info-box-number"><?php echo $subscriptions; ?></span>
		</div>
	  </div>
	</div>

	<div class="col-md-3 col-sm-6 col-xs-12">
	  <div class="info-box">
		<span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
		<div class="info-box-content">
		  <span class="info-box-text">Active Subs</span>
		  <span class="info-box-number"><?php echo $active_subscription; ?></span>
		</div>
	  </div>
	</div>

	<div class="clearfix visible-sm-block"></div>
	
	<div class="col-md-3 col-sm-6 col-xs-12">
	  <div class="info-box">
		<span class="info-box-icon bg-red"><i class="fa fa-warning"></i></span>
		<div class="info-box-content">
		  <span class="info-box-text">Expired Subs</span>
		  <span class="info-box-number"><?php echo $expired_subscription; ?></span>
		</div>
	  </div>	
	</div>
	
	<div class="col-md-3 col-sm-6 col-xs-12">
	  <div class="info-box">
		<span class="info-box-icon bg-blue"><i class="fa fa-money"></i></span>	
		<div class="info-box-content">
		  <span class="info-box-text">All Income</span>
		  <span class="info-box-number"><?php echo ($total_earned > 0 ? "$".number_format($total_earned, 2) : "0"); ?></span>
		</div>
	  </div>
	</div>

</div>

<div class="row">
	<div class="col-md-12">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">Monthly Earning Report (<?php echo $year; ?>)</h3>

				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<p class="text-center">
							<strong><?=$stats_start;?> &mdash; <?=$stats_end;?></strong>
						</p>
						<div class="chart">
							<canvas id="salesChart" style="height: 180px;"></canvas>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<div class="row">
					<div class="col-sm-12 col-xs-12">
					<div class="description-block border-right">
						<h5 class="description-header">$<?php echo number_format($totalIncomeForYear, 2); ?></h5>
						<span class="description-text">Income</span>
					</div>
					</div>		
				</div>
			</div>
		</div>
	</div>
</div>