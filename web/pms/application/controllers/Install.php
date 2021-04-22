<?php defined('BASEPATH') or exit('No direct script access allowed');
class Install extends My_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('IUsers');    
        $this->load->model('IConfigurations');    
        $this->setLayout("dialog");

        $iusers = $this->IUsers->count();
	    if(isset($iusers) && is_array($iusers) && count($iusers)) {
	        exit("Already installed.");
	    }
    }
    public function index()
    {
	$main_product = new MainProduct();
	$bg = $main_product->getProductSettingsOption("bg");
	if ($bg) redirect("access/login");

        $name = input_post_request('name');
        tpl_assign("name", $name);
        
        $email = input_post_request('email');
        tpl_assign("email", $email);

        $purchase_key = input_post_request('purchase_key');
        tpl_assign("purchase_key", $purchase_key);

        $password = input_post_request('password');
        $is_submited = input_post_request('submited') == 'submited';
        if ($is_submited)
        {

            $this
                ->form_validation
                ->set_rules('name', 'Name', 'trim|required|max_length[30]');
            $this
                ->form_validation
                ->set_rules('email', 'Email', 'trim|valid_email|required|max_length[100]');            
            $this
                ->form_validation
                ->set_rules('purchase_key', lang('c_533'), 'trim|required|max_length[100]');
            $this
                ->form_validation
                ->set_rules('password', 'Loing Password', 'trim|required|min_length[8]|max_length[20]');

            if ($this
                ->form_validation
                ->run() == false)
            {
                $error_msg = validation_errors();
            }
            else
            {
                $response = $main_product->validateKey($purchase_key);
                if (isset($response['status']) && $response['status'] == 'valid')
                {
                    try
                    {

                        $this->db->trans_begin();

                        $user = new IUser();
                        $user->setName($name);
                        $user->setEmail($email);
                        $user->setPassword($password);
                        $user->setIsActive(true);
                        $user->save();

                        $main_product->setRawProductSettings(["bg" => $response['code']]);

                        $this->db->trans_commit();

                        set_flash_success(lang('c_532'));
                        redirect("admin/access/login");
                    }
                    catch(Exception $e)
                    {
                        
                        $this->db->trans_rollback();
                        $main_product->setRawProductSettings();

                        $error_msg = lang('e_1');
                    }
                }
                else
                {
                    $error_msg = lang('e_4');
                }
            }
            tpl_assign('error', $error_msg);
        }
    }
}

