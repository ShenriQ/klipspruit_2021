<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_88')); 

$year = date('Y', time()); 
$previous_data = array(
"month" => date("M", strtotime("-1 month")),
"income" => 0, "expenses" => 0, "profit" => 0);

$current_data = array(
"month" => date("M", time()),
"income" => 0, "expenses" => 0, "profit" => 0);

$start = $year."-01-01";
$end = $year."-12-31";

$stats_start = date('d M, Y', human_to_unix($start.' 00:00'));
$stats_end = date('d M, Y', human_to_unix($end.' 00:00'));

$stats_payments = $this->TransactionLogs->getTransactionStatsFor("payment", $start, $end);
$stats_expenses = $this->TransactionLogs->getTransactionStatsFor("expense", $start, $end);

$totalExpenses = 0;
$totalIncomeForYear = 0;
	
$line1 = '';
$line2 = '';
$labels = '';

$untilMonth = ($end) ? date_format(date_create_from_format('Y-m-d', $end), 'm') : 12;	
for ($i = 01; $i <= $untilMonth; $i++) {

	$monthname = date_format(date_create_from_format('Y-m-d', '2016-'.$i.'-01'), 'M');
	$num = "0";
	$num2 = "0";

	foreach ($stats_payments as $value) {
		$act_month = explode("-", $value->stats_date); 
		if($act_month[1] == $i){  
			$num = sprintf("%02.2d", $value->summary);
			break; 
		} // if
	} // foreach
	 
	foreach ($stats_expenses as $value) {
		$act_month = explode("-", $value->stats_date); 
		if($act_month[1] == $i){  
			$num2 = sprintf("%02.2d", $value->summary);
			break;
		} // if
	} // foreach
	
	$i = sprintf("%02.2d", $i);
	$labels .= '"'.$monthname.'"';
	$line1 .= $num;
				
	$totalIncomeForYear = $totalIncomeForYear+$num;
	$line2 .= $num2;
	$totalExpenses = $totalExpenses+$num2;
	
	if($previous_data["month"] == $monthname) {

		$previous_data["income"] = $num;
		$previous_data["expenses"] = $num2;
		$previous_data["profit"] = $num-$num2;

	} elseif ($current_data["month"] == $monthname) {

		$current_data["income"] = $num;
		$current_data["expenses"] = $num2;
		$current_data["profit"] = $num-$num2;

	}
	
	if($i != "12"){ 
		$line1 .= ","; $line2 .= ","; $labels .= ",";
	} // if

} // for

// Income Defference
$income_percentage = 0;
if($current_data["income"] > $previous_data["income"]) {
	
	$income_diff = $current_data["income"] - $previous_data["income"];
	$income_percentage = $previous_data["income"] == 0 ? 100 : round(($income_diff/abs($previous_data["income"]))*100);
	
} elseif($current_data["income"] < $previous_data["income"]) { 

	$income_diff = $previous_data["income"] - $current_data["income"];
	$income_percentage = $previous_data["income"] == 0 ? -100 : round(($income_diff/abs($previous_data["income"]))*(-100));

} 

// Expenses Defference
$expenses_percentage = 0;
if($current_data["expenses"] > $previous_data["expenses"]) {
	
	$expenses_diff = $current_data["expenses"] - $previous_data["expenses"];
	$expenses_percentage = $previous_data["expenses"] == 0 ? 100 : round(($expenses_diff/abs($previous_data["expenses"]))*100);
	
} elseif($current_data["expenses"] < $previous_data["expenses"]) { 

	$expenses_diff = $previous_data["expenses"] - $current_data["expenses"];
	$expenses_percentage = $previous_data["expenses"] == 0 ? -100 : round(($expenses_diff/abs($previous_data["expenses"]))*(-100));

} 

// Profit Defference
$profit_percentage = 0;

if($current_data["profit"] > $previous_data["profit"]) {
	
	$profit_diff = $current_data["profit"] - $previous_data["profit"];
	$profit_percentage = $previous_data["profit"] == 0 ? 100 : round(($profit_diff/abs($previous_data["profit"]))*100);
	
} elseif($current_data["profit"] < $previous_data["profit"]) { 

	$profit_diff = $previous_data["profit"] - $current_data["profit"];
	$profit_percentage = $previous_data["profit"] == 0 ? -100 : round(($profit_diff/abs($previous_data["profit"]))*(-100));

} 

tpl_assign("footer_for_layout", "<script>

  var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
  var salesChart       = new Chart(salesChartCanvas);

  var salesChartData = {
    labels  : [".$labels."],
    datasets: [
      {
        label               : '".lang('c_304')."',
        fillColor           : 'rgb(210, 214, 222)',
        strokeColor         : 'rgb(210, 214, 222)',
        pointColor          : 'rgb(210, 214, 222)',
        pointStrokeColor    : '#c1c7d1',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgb(220,220,220)',
        data                : [".$line2."]
      },
      {
        label               : '".lang('c_305')."',
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


$announcements = $this->Announcements->getAllActive();
if(isset($announcements) && is_array($announcements) && count($announcements)) :
foreach ($announcements as $announcement_msg) : 
$share_with = $announcement_msg->getShareWith();
if($share_with == 'all' || ($share_with == 'members' && logged_user()->isMember()) 
|| ($share_with == 'clients' && !logged_user()->isMember()) ): ?>
<div id="notice_<?php echo $announcement_msg->getId();?>" class="alert alert-info alert-dismissable">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<i class="fa fa-bullhorn"></i> <strong><?php echo $announcement_msg->getTitle(); ?></strong> &mdash; <?php echo $announcement_msg->getDescription(); ?>
</div>	
<?php endif; endforeach; 
endif; ?>
  
<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>
<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('projects/add'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal">+ <?php echo lang('c_247'); ?></a></p>
<?php endif; ?>

<div class="row">

	<div class="col-md-3 col-sm-6 col-xs-12">
	  <div class="info-box">
		<span class="info-box-icon bg-aqua"><i class="fa fa-building"></i></span>
		<div class="info-box-content">
		  <span class="info-box-text"><?php echo lang('c_89'); ?></span>
		  <span class="info-box-number"><?php echo (isset($projects) && is_array($projects) ? count($projects) : 0); ?></span>
		</div>
	  </div>
	
	</div>
	
	<div class="col-md-3 col-sm-6 col-xs-12">
	  <div class="info-box">
		<span class="info-box-icon bg-red"><i class="fa fa-calendar"></i></span>
	
		<div class="info-box-content">
		  <span class="info-box-text"><?php echo lang('c_90'); ?></span>
		  <span class="info-box-number"><?php $current_datetime = date("Y-m-d H:i:s"); 
			$conditions = array('(start >= ? OR end >= ?)', $current_datetime, $current_datetime); 
			$events = logged_user()->getEvents($conditions);
			echo (isset($events) && is_array($events) ? count($events) : 0); ?></span>
		</div>
	
	  </div>
	
	</div>
	
	<div class="clearfix visible-sm-block"></div>
	
	<div class="col-md-3 col-sm-6 col-xs-12">
	  <div class="info-box">
		<span class="info-box-icon bg-blue"><i class="fa fa-tasks"></i></span>
	
		<div class="info-box-content">
		  <span class="info-box-text"><?php echo lang('c_91'); ?></span>
		  <span class="info-box-number">
		  <?php if(isset($projects) && is_array($projects) && count($projects)) {
				$project_ids = get_objects_ids($projects); 
				$open_tasks_count = $this->ProjectTasks->countByProject($project_ids);
			} else {
				$open_tasks_count = 0;				
			} 
			echo $open_tasks_count; ?></span>
		</div>
	  </div>
	
	</div>
	
	<div class="col-md-3 col-sm-6 col-xs-12">
	  <div class="info-box">
		<span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
	
		<div class="info-box-content">
		  <span class="info-box-text"><?php echo lang('c_92'); ?></span>
		  <span class="info-box-number"><?php echo (isset($online_users) && is_array($online_users) ? count($online_users) : 0); ?></span>
		</div>
	  </div>
	</div>

</div>

<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>

<div class="row">
<div class="col-md-12">
  <div class="box">
	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo lang('c_439'); ?></h3>

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
		<div class="col-sm-4 col-xs-6">
		  <div class="description-block border-right">
			<span class="description-percentage <?php echo ($income_percentage == 0 ? 'text-yellow' : ($income_percentage > 0 ? 'text-green' : 'text-red')); ?>"><i class="fa fa-caret-<?php echo ($income_percentage == 0 ? 'left' : ($income_percentage > 0 ? 'up' : 'down')); ?>"></i> <?php echo $income_percentage; ?>%</span>
			<h5 class="description-header"><?php echo config_option('default_currency', "$"); ?><?php echo number_format($totalIncomeForYear, 2); ?></h5>
			<span class="description-text"><?php echo lang('c_308'); ?></span>
		  </div>
		</div>
		<div class="col-sm-4 col-xs-6">
		  <div class="description-block border-right">
			<span class="description-percentage <?php echo ($expenses_percentage == 0 ? 'text-yellow' : ($expenses_percentage > 0 ? 'text-green' : 'text-red')); ?>"><i class="fa fa-caret-<?php echo ($expenses_percentage == 0 ? 'left' : ($expenses_percentage > 0 ? 'up' : 'down')); ?>"></i> <?php echo $expenses_percentage; ?>%</span>
			<h5 class="description-header"><?php echo config_option('default_currency', "$"); ?><?php echo number_format($totalExpenses, 2); ?></h5>
			<span class="description-text"><?php echo lang('c_309'); ?></span>
		  </div>
		</div>
		<div class="col-sm-4 col-xs-6">
		  <div class="description-block border-right">
			<span class="description-percentage <?php echo ($profit_percentage == 0 ? 'text-yellow' : ($profit_percentage > 0 ? 'text-green' : 'text-red')); ?>"><i class="fa fa-caret-<?php echo ($profit_percentage == 0 ? 'left' : ($profit_percentage > 0 ? 'up' : 'down')); ?>"></i> <?php echo $profit_percentage; ?>%</span>
			<h5 class="description-header"><?php echo config_option('default_currency', "$"); ?><?php echo number_format($totalIncomeForYear-$totalExpenses, 2);?></h5>
			<span class="description-text"><?php echo lang('c_310'); ?></span>
		  </div>
		</div>
		
	  </div>

	</div>

  </div>

</div>

</div>	  

<?php endif; ?>

<div class="row">

<div class="col-md-7">

	<div class="box">
	<div class="box-header">
	  <h3 class="box-title"><?php echo lang('c_95'); ?></h3>
	  <div class="box-tools pull-right">
		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	  </div>
	</div>
	
	<div class="box-body no-padding">
	  <table class="table table-striped">
		<tbody><tr>
		  <th><?php echo lang('c_23'); ?></th>
		  <th><?php echo lang('c_80'); ?></th>
		  <th class="custom-width40"><?php echo lang('c_440'); ?></th>
		</tr>
		<?php if(isset($projects) && is_array($projects) && count($projects)) :
		foreach($projects as $project) :?>
		<tr>
		  <td><a href="<?php echo get_page_base_url($project->getObjectURL()); ?>"><?php echo $project->getName(); ?></a></td>
		  <td><?php echo $project->getCreatedForCompany()->getName(); ?></td>
		  <td>
		  <?php $project_label = $project->getLabel();
			if(isset($project_label) && $project_label->getIsActive()) : ?>
			  <span class="badge" style="background-color:#<?php echo $project_label->getBgColorHex(); ?>;"><?php echo $project_label->getName(); ?></span>
		  <?php else: ?>
			&mdash;
		  <?php endif; ?>
		  </td>
		</tr>
		<?php endforeach;
		else : ?>
		<tr><td colspan="3">
			<?php echo lang('e_2'); ?>
		</td></tr>
		<?php endif; ?>
	  </tbody></table>
	  
	</div>
	</div>

	<div class="box">
	<div class="box-header">
	  <h3 class="box-title"><?php echo lang('c_93'); ?></h3>
	  <div class="box-tools pull-right">
		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	  </div>
	</div>
	
	<div class="box-body">

		<?php $activity_logs = null;	
		if(isset($project_ids) && is_array($project_ids) && count($project_ids)) {
		
			$include_private = logged_user()->isMember();
			$include_hidden = logged_user()->isAdmin() || logged_user()->isOwner();

			$activity_logs = $this->ActivityLogs->getAll($include_private, $include_hidden, $project_ids, 10, 0);
		
		}
		
		if(isset($activity_logs) && is_array($activity_logs) && count($activity_logs)) : ?>

		<ul class="timeline">

			<?php foreach($activity_logs as $activity_log) : 
			list($date_element_class, $log_date_value) = get_date_format_array($activity_log->getCreatedAt()); ?>

            <li>
              <i class="<?php echo get_activity_icon($activity_log->getModel()); ?>"></i>

              <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> <?php echo $log_date_value; ?></span>

                <div class="timeline-body <?php echo $date_element_class; ?>"><?php 
				
				$created_by_name = '';
				$create_by = $activity_log->getCreatedBy();
				
				if($create_by) {
					$created_by_name = ($create_by->getId() == logged_user()->getId() ? lang('c_24') : '<u>'.$create_by->getName().'</u>').' ';
				}
				
				$log_object = $activity_log->getObject();
				$raw_data = unserialize_data($activity_log->getRawData());
				
				$log_row = $created_by_name.' '.$raw_data['message'].' '.(isset($log_object) && $log_object->getObjectURL() ? '<a href="'.get_page_base_url($log_object->getObjectURL()).'">'.$raw_data['title'].'</a>' : $raw_data['title']);
				
				echo $log_row;?>
							
				</div>
				
              </div>
            </li>
			
			<?php endforeach; ?>
			
		</ul>

		<p class="text-right"><a href="<?php echo get_page_base_url('activity'); ?>"><?php echo lang('c_94'); ?> &raquo;</a></p>

		<?php else : ?>
		<p><?php echo lang('e_2'); ?></p>
		<?php endif; ?>

     </div>
	</div>       

</div>

<div class="col-md-5">

	<div class="box box-success">
	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo lang('c_92'); ?></h3>
	  <div class="box-tools pull-right">
		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	  </div>
	</div>

	<div class="box-body no-padding">

		<p class="custom-p-10"><?php echo lang('c_97'); ?></p>
		<?php if(isset($online_users) && is_array($online_users) && count($online_users)) :?>
		<ul class="users-list clearfix">
		<?php foreach($online_users as $online_user) :?>
		<li>
		  <img src="<?php echo $online_user->getAvatar(); ?>" class="img-circle-md" title="<?php echo $online_user->getName(); ?>">
		  <span class="users-list-name"><?php echo $online_user->getName(); ?></span>
		  <span class="users-list-date"><?php if($online_user->isOwner()) : ?>Owner<?php 
		   elseif($online_user->isAdmin()) : ?>Admin<?php 
		   elseif($online_user->isMember()) : ?>Member<?php 
		   else : ?>Client<?php endif; ?></span>
		</li>
		
		<?php endforeach; ?>
		</ul>
		<?php endif; ?>
				
	</div>

   </div>

	<?php if(logged_user()->isMember()) : ?>

	<div class="box">
	<div class="box-header">
	  <h3 class="box-title"><?php echo lang('c_100'); ?></h3>
	  <div class="box-tools pull-right">
		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	  </div>
	</div>
	
	<div class="box-body">
	  <div class="table-responsive">
		<?php if(isset($my_tasks) && is_array($my_tasks) && count($my_tasks)) : ?>
		<table class="table table-striped">
		<tbody><tr>
		  <th><?php echo lang('c_183'); ?></th>
		  <th><?php echo lang('c_23'); ?></th>
		  <th class="custom-width40"><?php echo lang('c_440'); ?></th>
		</tr>
		<?php foreach($my_tasks as $my_task) :?>
		<tr>
		  <td><a href="<?php echo get_page_base_url($my_task->getObjectURL()); ?>"><?php echo $my_task->getName(); ?></a><br>
		  <small><?php echo $my_task->getTaskList()->getName(); ?></small></td>
		  <td><?php echo $my_task->getProject()->getName(); ?></td>
		  <td>
		  <?php $task_label = $my_task->getLabel();
			if(isset($task_label) && $task_label->getIsActive()) : ?>
			  <span class="badge" style="background-color:#<?php echo $task_label->getBgColorHex(); ?>;"><?php echo $task_label->getName(); ?></span>
		  <?php else: ?>
			&mdash;
		  <?php endif; ?>
		  </td>
		</tr>
		<?php endforeach; ?>
	    </tbody></table>
		<p class="text-right"><a href="<?php echo get_page_base_url('mywork'); ?>"><?php echo lang('c_101'); ?> &raquo;</a></p>
		
		<?php else : ?>
		<tr><td colspan="3">
			<?php echo lang('e_2'); ?>
		</td></tr>
		<?php endif; ?>
	  </div>
	</div>
	</div>

	<div class="box">
	<div class="box-header">
	  <h3 class="box-title"><?php echo lang('c_102'); ?></h3>
	  <div class="box-tools pull-right">
		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	  </div>
	</div>
	
	<div class="box-body">
	  <div class="table-responsive">
		<?php if(isset($my_tickets) && is_array($my_tickets) && count($my_tickets)) : ?>
		<table class="table table-striped">
		<tbody><tr>
		  <th><?php echo lang('c_183'); ?></th>
		  <th><?php echo lang('c_23'); ?></th>
		  <th class="custom-width40"><?php echo lang('c_440'); ?></th>
		</tr>
		<?php foreach($my_tickets as $my_ticket) :?>
		<tr>
		  <td><a href="<?php echo get_page_base_url($my_ticket->getObjectURL()); ?>"><?php echo $my_ticket->getName(); ?></a><br>
		  <small><?php echo $my_ticket->getTicketType()->getName(); ?></small></td>
		  <td><?php echo $my_ticket->getProject()->getName(); ?></td>
		  <td>
		  <?php $ticket_label = $my_ticket->getLabel();
			if(isset($ticket_label) && $ticket_label->getIsActive()) : ?>
			  <span class="badge" style="background-color:#<?php echo $ticket_label->getBgColorHex(); ?>;"><?php echo $ticket_label->getName(); ?></span>
		  <?php else: ?>
			&mdash;
		  <?php endif; ?>
		  </td>
		</tr>
		<?php endforeach; ?>
		</tbody></table>
		<p class="text-right"><a href="<?php echo get_page_base_url('mywork'); ?>"><?php echo lang('c_103'); ?> &raquo;</a></p>
		<?php else : ?>
		<p><?php echo lang('e_2'); ?></p>
		<?php endif; ?>
	  </div>
	</div>
	</div>
			
	<?php endif; ?>


	<div class="box">
	<div class="box-header">
	  <h3 class="box-title"><?php echo lang('c_98'); ?></h3>
	  <div class="box-tools pull-right">
		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	  </div>
	</div>
	
	<div class="box-body">
	  <div class="table-responsive">
		<?php if(isset($events) && is_array($events) && count($events)) : ?>
		<table class="table no-margin">
		<tbody>
		<?php foreach($events as $event) :
			$start_date = format_date($event->getStart(), 'j M. Y H:i');
			$end_date = format_date($event->getEnd(), 'j M. Y H:i');
			$event_date = ($start_date == $end_date ? $start_date : $start_date." &mdash; ".$end_date); ?>
		<tr>
		  <td><p><b><a href="<?php echo $event->getObjectURL(); ?>"><?php echo $event->getTitle(); ?></a></b><br>
		  <?php echo shorter($event->getDescription(), 100); ?></p>
		  <span class="label <?php echo $event->getClassname(); ?>"><?php echo $event_date; ?></span></td>
		</tr>
		<?php endforeach; ?>
	    </tbody></table>
		<p class="text-right"><a href="<?php echo get_page_base_url('calendar'); ?>"><?php echo lang('c_99'); ?> &raquo;</a></p>
		<?php else : ?>
		<p><?php echo lang('e_2'); ?></p>
		<?php endif; ?>
	  </div>
	</div>
	</div>

</div>
</div>
