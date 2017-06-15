<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DungNT
 */
class MobileContext {
    
    /**
     * The Settings Construct
     */
    public function __construct() {
    }
    
    /**
     * Get customer logged information.
     */
    public static function getCustomerLoggedIn() {
        $customer = ci()->session->userdata(APConstants::SESSION_MOBILE_CUSTOMER_KEY);
        return $customer;
    }
    
    /**
     * Get customer logged information.
     */
    public static function getSessionKey() {
    	$key = ci()->session->userdata(APConstants::SESSION_MOBILE_KEY);
    	return $key;
    }
    
    /**
     * Get customer logged information.
     */
    public static function getCustomerIDLoggedIn() {
    	$customer = ci()->session->userdata(APConstants::SESSION_MOBILE_CUSTOMER_KEY);
    	if (!empty($customer)) {
    	    return $customer->customer_id;
    	}
    	return '';
    }
    
    /**
     * Get customer logged information.
     */
    public static function getParentCustomerIDLoggedIn() {
    	$customer = ci()->session->userdata(APConstants::SESSION_MOBILE_CUSTOMER_KEY);
    	if (!empty($customer)) {
            if (empty($customer->parent_customer_id)) {
                return $customer->customer_id;
            } else {
                return $customer->parent_customer_id;
            }
    	}
    	return '';
    }

    /**
     * Get admin logged information.
     */
    public static function isAdminUser()
    {
        $user = ci()->session->userdata(APConstants::SESSION_MOBILE_ADMIN_KEY);
        $groups = ci()->session->userdata(APConstants::SESSION_MOBILE_GROUP_USERS_ROLE);
        
        $message = "User: ".json_encode($user);
        $message .= "\n \n groups: ".json_encode($groups);

        log_audit_message(APConstants::LOG_ERROR, $message, FALSE, 'MobileContext_isAdminUser_');
        if($user != null && $groups && (in_array("0", $groups) || in_array("1", $groups) ) ){
            return true;
        }
        return false;
    }

    /**
     * Get admin logged information. (Does not have this role now)
     */
    public static function isAdminParner()
    {
        $user = ci()->session->userdata(APConstants::SESSION_MOBILE_ADMIN_KEY);
        $groups = ci()->session->userdata(APConstants::SESSION_MOBILE_GROUP_USERS_ROLE);
        
        if($user != null && $groups && in_array("3", $groups) ){
            return true;
        }
        return false;
    }

    /**
     * Get admin logged information.
     */
    public static function isAdminLocation()
    {
        $user = ci()->session->userdata(APConstants::SESSION_MOBILE_ADMIN_KEY);
        $groups = ci()->session->userdata(APConstants::SESSION_MOBILE_GROUP_USERS_ROLE);
        
        if($user != null && $groups && in_array("4", $groups) ){
            return true;
        }
        
        return false;
    }

    /**
     * Get worker admin logged information.
     */
    public static function isWorkerAdmin()
    {
        $user = ci()->session->userdata(APConstants::SESSION_MOBILE_ADMIN_KEY);
        $groups = ci()->session->userdata(APConstants::SESSION_MOBILE_GROUP_USERS_ROLE);
        
        if($user != null && $groups && in_array("2", $groups) ){
            return true;
        }
        
        return false;
    }
    /**
     * Get admin logged information.
     */
    public static function getAdminLoggedIn()
    {
        $user = ci()->session->userdata(APConstants::SESSION_MOBILE_ADMIN_KEY);
        return $user;
    }
    
    /**
     * Get admin logged information.
     */
    public static function getAdminIdLoggedIn() {
        $user = ci()->session->userdata(APConstants::SESSION_MOBILE_ADMIN_KEY);
        if ($user) {
            return $user->id;
        }

        return "";
    }

    /**
     * check primary enterprise customer.
     */
    public static function isPrimaryCustomerUser(){
        $customer = ci()->session->userdata(APConstants::SESSION_MOBILE_CUSTOMER_KEY);
        
        if($customer->account_type == APConstants::ENTERPRISE_TYPE && empty($customer->parent_customer_id)){
            return true;
        }
        
        return false;
    }
}