<?php defined('BASEPATH') or exit('No direct script access allowed');
class MainProduct
{
    private $product_settings = false;
    public function __construct()
    {
        $this->setProductSettings();
    }
    public function initialize($initial_data)
    {
//         $bg = $this->getProductSettingsOption("bg");
//         if ($bg)
//         {
//             $do = input_get_request('do');
//             if (isset($do) && $do == "rbg")
//             {
//                 $response = $this->validateBg($bg);
//                 if (isset($response['status']) && $response['status'] == 'invalid')
//                 {
//                     $this->setRawProductSettings();
//                     exit(null);
//                 }
//             }
//             // edited 2021-04-21 by shendi
//             // if (md5(base64_decode("cHJvbXNfc2Fhcw==") . base_url()) != $bg)
//             // {
//             //     exit(null);
//             // }
//         }
//         else
//         {
// +           redirect('install');
//         }
      
        $CI = & get_instance();
        $CI
            ->load
            ->library($initial_data);
            
    }
    public function getProductSettingsJsonFilePath()
    {
        return FCPATH . "public/assets/config/product_settings.json";
    }
    public function validateBg($bg)
    {   
        $response = @file_get_contents(base64_decode("aHR0cDovLzc4Nm5ldC5jb20vcHJvbXNfc2Fhc19hY2Nlc3MvdmFsaWRhdGUucGhwP2JnPQ==") . $bg);
        return ($response ? json_decode($response, true) : false);
    }
    public function validateKey($key)
    {
        $response = @file_get_contents(base64_decode("aHR0cDovLzc4Nm5ldC5jb20vcHJvbXNfc2Fhc19hY2Nlc3MvYWRkLnBocD9rZXk9") . $key . "&dm=" . base_url());
        return ($response ? json_decode($response, true) : false);
    }
    public function getRawProductSettings()
    {
        $raw_product_settings = @file_get_contents($this->getProductSettingsJsonFilePath());
        return ($raw_product_settings ? json_decode($raw_product_settings, true) : false);
    }
    public function setRawProductSettings($setting_vars = array())
    {
        file_put_contents($this->getProductSettingsJsonFilePath() , json_encode($setting_vars));
    }
    protected function setProductSettings()
    {
        $this->product_settings = $this->getRawProductSettings();
    }
    public function getProductSettingsOption($option)
    {
        return array_key_value($this->product_settings, $option, false);
    }
}