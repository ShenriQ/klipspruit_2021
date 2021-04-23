<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(!logged_user()->isOwner()) redirect('dashboard');
				
	}
	
	public function time_tracking() {

		$year = date('Y', time()); 

 		$start = input_post_request('start', $year."-01-01");
		tpl_assign("stats_start_short", $start);
 		
 		$end = input_post_request('end', $year."-12-31");
		tpl_assign("stats_end_short", $end);

		tpl_assign("stats_start", date('Y/m/d', human_to_unix($start.' 00:00')));
		tpl_assign("stats_end", date('Y/m/d', human_to_unix($end.' 00:00')));

		$members = $this->Users->getAllMembers();
		tpl_assign("members", $members);

		$member_ids = get_objects_ids($members);
 		$member_id = (int) input_post_request('member_id');
		
		if($member_id > 0 && isset($member_ids) && is_array($member_ids) && count($member_ids)
		&& in_array($member_id, $member_ids)) {
			$request_member_ids = array($member_id);
			tpl_assign("member_id", $member_id);		
		} else {
			$request_member_ids = $member_ids;
			tpl_assign("member_id", 0);		
		}	
		
		$timelog_stats = $this->ProjectTimelogs->getTimelogStats($request_member_ids, $start, $end);
		tpl_assign("timelog_stats", $timelog_stats);

	}
	
	public function index() {
		
		$year = date('Y', time()); 

 		$start = input_post_request('start', $year."-01-01");
		tpl_assign("stats_start_short", $start);
 		
 		$end = input_post_request('end', $year."-12-31");
		tpl_assign("stats_end_short", $end);

		tpl_assign("stats_start", date('Y/m/d', human_to_unix($start.' 00:00')));
		tpl_assign("stats_end", date('Y/m/d', human_to_unix($end.' 00:00')));

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
			
			if($i != "12"){ 
				$line1 .= ","; $line2 .= ","; $labels .= ",";
			} // if

		} // for
		
		tpl_assign("totalIncomeForYear", $totalIncomeForYear);
		tpl_assign("totalExpenses", $totalExpenses);

		tpl_assign("labels", $labels);
		tpl_assign("line1", $line1);
		tpl_assign("line2", $line2);
		tpl_assign("totalProfit", $totalIncomeForYear-$totalExpenses);
	
	}

	
}
