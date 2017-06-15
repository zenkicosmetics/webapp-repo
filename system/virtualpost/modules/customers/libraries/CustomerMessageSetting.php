<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class CustomerMessageSetting {
    public function __construct() {
        ci()->load->model("customers/customer_message_m");
    }
    
    /**
     * Gets customer setting by key
     * @param type $customer_id
     * @param type $key
     */
    public static function create($customer_id, $message, $message_type){
        ci()->customer_message_m->insert(array(
            "customer_id" => $customer_id,
            "message" => $message,
            "message_type" => $message_type,
            "read_flag" => APConstants::OFF_FLAG,
            "created_date" => now(),
            "read_date" => null
        ));
    }
    
    /**
     * Read message.
     * 
     * @param type $customer_id
     * @param type $start
     * @param type $limit
     */
    public static function get($customer_id, $start, $limit) {
        $array_condition = array(
            'customer_id' => $customer_id
        );
        $query = ci()->customer_message_m->get_paging($array_condition, $start, $limit, 'created_date', 'desc');
        return $query;
    }
}
