<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", $project->getName().' : '.lang('c_172')); 

$sort_by = input_get_request('sort_by');
$project_ids = array($project->getId());
$current_page_base_url = get_page_base_url($project->getObjectURL('invoices'));

if(logged_user()->isOwner()) {
    
    switch($sort_by) {

        case 'recurring' : 
            $invoices = $this->Invoices->getRecurring($project_ids);
            break;
        
        case 'paid_cancelled':
            $invoices = $this->Invoices->getPaidAndCancelled($project_ids);
            break;

        default:
            $invoices = $this->Invoices->getAvailable($project_ids);
            break;

    }
    
} else {
    
    switch($sort_by) {
        
        case 'paid_cancelled':
            $invoices = $this->Invoices->getPaidAndCancelledClientOnly(logged_user(), $project_ids);
            break;

        default:
            $invoices = $this->Invoices->getAvailableByClientOnly(logged_user(), $project_ids);
            break;

    }

}

$is_project_panel = true;

include(APPPATH.'views/invoices/index.php');

?>