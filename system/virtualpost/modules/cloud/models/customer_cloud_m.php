<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class customer_cloud_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('customer_cloud');
        $this->primary_key = 'id';
    }
    
    /**
     * Get all cloud by customer_id.
     *
     * @param unknown_type $customer_id
     */
    function get_all_cloud($list_customer_id) {
        $this->load->model('mailbox/postbox_m');
        $this->load->model('mailbox/postbox_setting_m');
        
        $this->db->select('clouds.cloud_name, customer_cloud.*, customers.customer_code');
        $this->db->join('clouds', 'clouds.cloud_id = customer_cloud.cloud_id');
        $this->db->join('customers', 'customers.customer_id = customer_cloud.customer_id', 'left');
        $this->db->where_in("customer_cloud.customer_id", $list_customer_id);
        $query_result = $this->db->get($this->_table)->result();
        // Parse accounting email setting
        $result = array();
        if (!empty($query_result)){
            foreach ($query_result as $item){
                if ($item->cloud_id == APConstants::CLOUD_ACCOUNTING_EMAIL_CODE && !empty($item->settings)){
                    $settings = json_decode($item->settings, true);
                    foreach ($settings as $setting){
                        $array_item = new stdClass();
                        $array_item->interface_type = 'Interface';
                        $array_item->cloud_name = $setting['interface_id'] ;
                        $array_item->customer_id = $item->customer_id;
                        $array_item->cloud_id = $item->cloud_id;
                        $array_item->postbox_id = $setting['postbox_id'];
                        $array_item->email = $setting['email'];
                        $array_item->customer_code = $item->customer_code;
                        $p_setting = $this->postbox_setting_m->get($setting['postbox_id']);
                        $array_item->auto_save_flag = !empty($p_setting) ? $p_setting->always_mark_invoice : 0;
                        $result[] = $array_item;
                    }
                } else {
                    $item->interface_type = 'Cloud';
                    $result[] = $item;
                }
            }
        }
        //Return result
        return $result;
    }
}