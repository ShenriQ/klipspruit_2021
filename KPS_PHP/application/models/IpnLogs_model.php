<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* IpnLogs Model */

class IpnLogs_model extends Application_model {

    function __construct() {
        parent::__construct('ipn_logs', 'IpnLog');
    }

	public function create($data, $is_payment_processed) {

		$ipn_log = new IpnLog();

		$ipn_log->setData($data);
		$ipn_log->setIsPaymentProcessed($is_payment_processed);
		$ipn_log->setIp($_SERVER['REMOTE_ADDR']);

		$ipn_log->save();
		
	}


}

/* IpnLog Object */

class IpnLog extends Application_object {
	
	function __construct() {
		parent::__construct('ipn_logs');
	}
	
}