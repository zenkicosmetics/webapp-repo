<?php defined('BASEPATH') or exit('No direct script access allowed');

class cloud_api
{
    function __construct() {
        ci()->load->model(array(
            "cloud/cloud_m",
            "cloud/customer_cloud_m",
            'mailbox/postbox_setting_m'
        ));
    }
    
    public static function save_accounting_email($customer_id, $postbox_id, $accountingEmail, $interface_id, $auto_send_pdf){
        $accounting_setting = array(  array(
            "postbox_id" => $postbox_id,
            "email" => $accountingEmail,
            "interface_id" => $interface_id
        ));

        // Check exist cloud id
        $customer_cloud = ci()->customer_cloud_m->get_by_many(array (
                'cloud_id' => APConstants::CLOUD_ACCOUNTING_EMAIL_CODE,
                'customer_id' => $customer_id 
        ));

        if (empty($customer_cloud)) {
            // Add accounting cloud setting
            ci()->customer_cloud_m->insert(array (
                    'cloud_id' => APConstants::CLOUD_ACCOUNTING_EMAIL_CODE,
                    'customer_id' => $customer_id,
                    'settings' => json_encode($accounting_setting)
            ));
        } else {
            //Merge old accouting email setting
            if (!empty($customer_cloud->settings)){
                //Check exites setting for this postbox
                $settings = json_decode($customer_cloud->settings, true);
                $update_setting = false;
                foreach ($settings as &$setting){
                    if ($setting['postbox_id'] == $postbox_id) {
                        $setting['email'] = $accountingEmail;
                        $setting['interface_id'] = $interface_id;
                        $update_setting = true;
                        break;
                    }
                }
                //If add new setting for this postbox
                if (!$update_setting){
                    $settings = array_merge($settings, $accounting_setting);
                }

                //Update accouting cloud setting
                ci()->customer_cloud_m->update_by_many( array(
                    'cloud_id' => APConstants::CLOUD_ACCOUNTING_EMAIL_CODE,
                    'customer_id' => $customer_id
                ), array(
                    'settings' => json_encode($settings)
                ));
            }
        }
        
        ci()->postbox_setting_m->update($postbox_id, array('always_mark_invoice' => $auto_send_pdf));
        
        //Add cloud history
        LogUtils::log_customer_cloud_history($customer_id);
    }
    
    /**
     * delete interface by id.
     * @param type $customer_id
     * @param type $cloud_id
     */
    public static function delete_interface($customer_id, $cloud_id, $postbox_id){
        // Delete accounting setting
        if ($cloud_id == APConstants::CLOUD_ACCOUNTING_EMAIL_CODE){
             //Get accounting email of this postbox
           $customer_setting = ci()->customer_cloud_m->get_by_many(array(
               'cloud_id' => APConstants::CLOUD_ACCOUNTING_EMAIL_CODE,
               'customer_id' => $customer_id
           ));

           if (!empty($customer_setting) && !empty($customer_setting->settings)) {
                //Remove setting 
                $settings = json_decode($customer_setting->settings, true);
                foreach ($settings as $key => $setting) {
                    if ($setting['postbox_id'] == $postbox_id) {
                        unset($settings[$key]);
                    }
                }
               
               if (empty($settings)) {
                    //Delete cloud setting record if does not remain any setting
                    ci()->customer_cloud_m->delete_by_many(array (
                        'cloud_id' => $cloud_id,
                        'customer_id' => $customer_id 
                    ));
               } else {
                    //Update accouting cloud setting
                    ci()->customer_cloud_m->update_by_many(
                        array(
                            'cloud_id' => APConstants::CLOUD_ACCOUNTING_EMAIL_CODE,
                            'customer_id' => $customer_id
                        ), array(
                            'settings' => json_encode($settings)
                    ));
               }
           }
        } else {
            //Delete other cloud service setting
            ci()->customer_cloud_m->delete_by_many(array (
                'cloud_id' => $cloud_id,
                'customer_id' => $customer_id 
            ));
        }
        //Add cloud history
        LogUtils::log_customer_cloud_history($customer_id);
    }
}