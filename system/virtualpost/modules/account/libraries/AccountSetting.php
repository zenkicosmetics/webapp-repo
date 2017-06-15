<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class AccountSetting{
    public function __construct() {
        ci()->load->model("customers/customer_setting_m");
    }
    
    /**
     * Gets customer setting by key
     * @param type $customer_id
     * @param type $key
     */
    public static function get($customer_id, $key){
        $result = ci()->customer_setting_m->get_by_many(array(
            "parent_customer_id" => $customer_id,
            "setting_key" => $key
        ));
        if(!empty($result)){
            return $result->setting_value;
        }
        return "";
    }
    
    /**
     * set customers setting by key.
     */
    public static function set($customer_id, $key, $value){
        $check = ci()->customer_setting_m->get_by_many(array(
            "parent_customer_id" => $customer_id,
            "setting_key" => $key
        ));
        
        if(empty($check)){
            ci()->customer_setting_m->insert(array(
                "parent_customer_id" => $customer_id,
                "setting_key" => $key,
                "setting_value" => $value,
                'created_date' => now()
            ));
        }else{
            ci()->customer_setting_m->update_by_many(array(
                "parent_customer_id" => $customer_id,
                "setting_key" => $key
            ), array(
                "setting_value" => $value
            ));
        }
        return true;
    }
    
    public static function get_many($customer_id){
        $result = ci()->customer_setting_m->get_many_by_many(array(
            "parent_customer_id" => $customer_id,
        ));
        
        return $result;
    }
    
    public static function get_alias01($customer_id, $key, $alias){
        $result = ci()->customer_setting_m->get_by_many(array(
            "parent_customer_id" => $customer_id,
            "setting_key" => $key,
            "(alias01 = '".$alias."' OR alias01 = 'all')" => null
        ));
        if(!empty($result)){
            return $result->setting_value;
        }
        return "";
    }
    
    public static function set_alias01($customer_id, $key, $value, $alias){
        $check = ci()->customer_setting_m->get_by_many(array(
            "parent_customer_id" => $customer_id,
            "setting_key" => $key,
            "alias01" => $alias
        ));
        
        if(empty($check)){
            ci()->customer_setting_m->insert(array(
                "parent_customer_id" => $customer_id,
                "setting_key" => $key,
                "setting_value" => $value,
                "alias01" => $alias,
                'created_date' => now()
            ));
        }else{
            ci()->customer_setting_m->update_by_many(array(
                "parent_customer_id" => $customer_id,
                "setting_key" => $key,
                "alias01" => $alias
            ), array(
                "setting_value" => $value,
            ));
        }
        return true;
    }
    
    public static function get_alias02($customer_id, $key){
        $result = ci()->customer_setting_m->get_by_many(array(
            "parent_customer_id" => $customer_id,
            "setting_key" => $key
        ));
        if(!empty($result)){
            return $result->alias02;
        }
        return "";
    }
    
    public static function set_alias02($customer_id, $key, $alias){
        $check = ci()->customer_setting_m->get_by_many(array(
            "parent_customer_id" => $customer_id,
            "setting_key" => $key
        ));
        
        if(empty($check)){
            ci()->customer_setting_m->insert(array(
                "parent_customer_id" => $customer_id,
                "setting_key" => $key,
                "alias02" => $alias,
                'created_date' => now()
            ));
        }else{
            ci()->customer_setting_m->update_by_many(array(
                "parent_customer_id" => $customer_id,
                "setting_key" => $key,
            ), array(
                "alias02" => $alias
            ));
        }
        return true;
    }
    
    public static function get_alias03($customer_id, $key){
        $result = ci()->customer_setting_m->get_by_many(array(
            "parent_customer_id" => $customer_id,
            "setting_key" => $key
        ));
        if(!empty($result)){
            return $result->alias03;
        }
        return "";
    }
    
    public static function set_alias03($customer_id, $key, $alias){
        $check = ci()->customer_setting_m->get_by_many(array(
            "parent_customer_id" => $customer_id,
            "setting_key" => $key
        ));
        
        if(empty($check)){
            ci()->customer_setting_m->insert(array(
                "parent_customer_id" => $customer_id,
                "setting_key" => $key,
                "alias03" => $alias,
                'created_date' => now()
            ));
        }else{
            ci()->customer_setting_m->update_by_many(array(
                "parent_customer_id" => $customer_id,
                "setting_key" => $key,
            ), array(
                "alias03" => $alias
            ));
        }
        return true;
    }
    
    /**
     * Gets setting list of customer
     * @param type $customer_id
     * @return array
     */
    public static function get_list_setting_by($customer_id){
        $result = array();
        $rows = ci()->customer_setting_m->get_many_by_many(array(
            "parent_customer_id" => $customer_id,
        ));
        
        foreach($rows as $row){
            $result[$row->setting_key] = $row;
        }
        
        return $result;
    }
    
    /**
     * Gets by setting key and value.
     * @param type $key
     * @param type $value
     * @return type
     */
    public static function getSettingByValue($key, $value){
        $result = ci()->customer_setting_m->get_by_many(array(
            "setting_key" => $key,
            "setting_value" => $value
        ));

        return $result;
    }
}
