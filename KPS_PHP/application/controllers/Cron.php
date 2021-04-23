<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {
    
	function __construct() {
	
		parent::__construct();
        $this->load->model("Invoices");
        $this->load->model("SearchableObjects");
        $this->load->model("Projects");
        $this->load->model("InvoiceItems");
        $this->load->model("UserNotifications");
        $this->load->model("Companies");
        $this->load->model("Configurations");
        $this->load->model("Users");
        
    }
	
	public function index() {
        
        $this->createRecurringInvoices();
        $this->sendInvoiceDuePreReminder();
        $this->sendInvoiceDueLateReminder();
        
    }
    
    private function sendInvoiceDuePreReminder() {

        $reminder_days = (int) config_option("send_due_date_invoice_reminder_before_days");
        if ($reminder_days) {

            $reminder_date = add_period_to_timestamp(time(), $reminder_days, "days");
            $invoices = $this->Invoices->getDueReminders($reminder_date);
            if(isset($invoices) && is_array($invoices) && count($invoices)) {

                foreach ($invoices as $invoice) {            

                    try {

                        $this->db->trans_begin();

                        // Send notification ..
                        $notify_user_object = $invoice->getClient();
                        if(isset($notify_user_object)) {

                            $notify_subject = lang('c_523.106');
                            $notify_message = $this->load->view("emails/invoice_due_pre_reminder", 
                            array("invoice_no" => $invoice->getInvoiceNo(), "invoice_due_date" => $reminder_date,
                            "invoice_link" => get_page_base_url($invoice->getObjectURL())), true);

                            $this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
                            send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
                                
                        }

                        $invoice->setDueReminderDate($reminder_date);
                        $invoice->save();
                     
                        $this->db->trans_commit();

                    } catch(Exception $e) {
                        $this->db->trans_rollback();
                    }                        

                }    

            }
        }
    }

    private function sendInvoiceDueLateReminder() {

        $reminder_days = (int) config_option("send_due_date_invoice_reminder_after_days");
        if ($reminder_days) {

            $reminder_date = subtract_period_to_timestamp(time(), $reminder_days, "days");
            $invoices = $this->Invoices->getDueReminders($reminder_date);
            if(isset($invoices) && is_array($invoices) && count($invoices)) {

                foreach ($invoices as $invoice) {            

                    try {

                        $this->db->trans_begin();

                        // Send notification ..
                        $notify_user_object = $invoice->getClient();
                        if(isset($notify_user_object)) {

                            $notify_subject = lang('c_523.107');
                            $notify_message = $this->load->view("emails/invoice_due_late_reminder", 
                            array("invoice_no" => $invoice->getInvoiceNo(),
                            "invoice_link" => get_page_base_url($invoice->getObjectURL())), true);

                            $this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
                            send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
                                
                        }

                        $invoice->setDueReminderDate($reminder_date);
                        $invoice->save();
                     
                        $this->db->trans_commit();

                    } catch(Exception $e) {
                        $this->db->trans_rollback();
                    }                        

                }    

            }
        }
    }

    private function createRecurringInvoices() {

        $recurring_invoices = $this->Invoices->getRenewableRecurring();
        if (isset($recurring_invoices) && is_array($recurring_invoices) && count($recurring_invoices)) {
            foreach ($recurring_invoices as $recurring_invoice) {
                $this->createNewInvoice($recurring_invoice);
            }
        }
    
    }

    private function createNewInvoice($invoice) {
 
        try {

            $this->db->trans_begin();

            $issue_date_timestamp = $invoice->getNextRecurringDate();
            $due_date_timestamp = $issue_date_timestamp + ($invoice->getDueAfterDays()*86400);

            $new_invoice = clone_invoice($invoice);

            $new_invoice->setIssueDate(format_date($issue_date_timestamp, 'Y-m-d'));
            $new_invoice->setDueDate(format_date($due_date_timestamp, 'Y-m-d'));
            $new_invoice->setRecurringInvoiceId($invoice->getId());

            $new_invoice->save();

            $no_of_cycles_completed = $invoice->getNoOfCyclesCompleted() + 1;
            $next_recurring_date  = add_period_to_timestamp($issue_date_timestamp, $invoice->getRecurringValue(), $invoice->getRecurringType());

            $invoice->setNoOfCyclesCompleted($no_of_cycles_completed);
            $invoice->setNextRecurringDate($next_recurring_date);

            $invoice->save();

            // Send notification to owner ..
            $notify_user_object = $this->Companies->getOwnerCompany()->getCreatedBy();
            if(isset($notify_user_object)) {

                $notify_subject = lang('c_523.102');
                $notify_message = $this->load->view("emails/recurring_invoice_created", 
                array("invoice_no" => $new_invoice->getInvoiceNo(), 
                "invoice_link" => get_page_base_url($new_invoice->getObjectURL())), true);

                $this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
                send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
                    
            }

            // Send notification to client ..
            $notify_user_object = $new_invoice->getClient();
            if(isset($notify_user_object)) {

                $notify_subject = lang('c_171');
                $notify_message = $this->load->view("emails/invoice", 
                array("invoice_no" => $new_invoice->getInvoiceNo(), 
                "invoice_link" => get_page_base_url($new_invoice->getObjectURL())), true);
    
                $this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
                send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
                    
            }

            $this->db->trans_commit();

        } catch(Exception $e) {
            $this->db->trans_rollback();
        }

    }
	
}