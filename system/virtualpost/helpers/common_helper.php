<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Load make, model, year.
 */
if (! function_exists('make_model')) {
    /**
     * Generate dropdown form from [code]
     * 
     * @param $var =
     *            array(
     *            "code" => $code, // code of dropdown
     *            "value" => $value, // selected value
     *            "name" => $name, //name of select box
     *            "id"	=> $id, // id of select box
     *            "clazz" => $cssClass // css class of select box
     *            "style" =>$style //style
     *            );
     */
    function make_model($var = '') {
        $ci = & get_instance();
        // Load model
        $ci->load->model('make_m');
        $makes = $ci->make_m->get_all();
        /**
         * Sort array by DESC
         * Enter description here ...
         * @param unknown_type $cmp_score1
         * @param unknown_type $cmp_score2
         */
        function cmp_result_asc($obj1, $obj2) {
            $a = trim($obj1->Make);
            $b = trim($obj2->Make);
            return strcmp($a, $b);
        }
        usort($makes, 'cmp_result_asc');
        $data = array (
                "makes" => $makes 
        );
        
        $html = $ci->load->view('common/make_model', $data, TRUE);
        
        return $html;
    }
}

if (! function_exists ( 'code_master_form_dropdown' )) {
    /**
     * Generate dropdown form from [code]
     *
     * @param $var =
     *            array(
     *            "code" => $code, // code of dropdown
     *            "value" => $value, // selected value
     *            "name" => $name, //name of select box
     *            "id"	=> $id, // id of select box
     *            "clazz" => $cssClass // css class of select box
     *            "style" => $style // style inline
     *            "has_empty" => $has_empty // Add default empty option
     *            "option_default" => $option_default // Add default empty option
     *            );
     */
    function code_master_form_dropdown($var = array()) {
        $ci = & get_instance ();

        // Select list items from code_master table
        $items = Settings::get_list( $var['code'] );

        if( isset($var['location_id']) && (!empty($var['location_id']))){
            $items = Settings::get_list_by_location($var['location_id']);
        }

        $html = "<select name=\"" . $var['name'] . "\" id=\"" . $var['id'] . "\" class=\"" . $var['clazz'] . "\" style=\"".$var['style']."\" >";
        if ($var['has_empty']) {
            $option_default = "";
            if(array_key_exists('option_default', $var)){
                $option_default = $var['option_default'];
            }
            $html = $html . "<option value=\"\">" . $option_default . "</option>";
        }
        foreach ( $items as $item ) {
            $selected = $item->ActualValue == $var['value'] ? "selected=\"selected\"" : "";               
            $html = $html . "<option value=\"" . $item->ActualValue . "\" " . $selected . ">" .$item->LabelValue . "</option>";
        }
        $html = $html . "</select>";

        return $html;
    }



}


if (! function_exists ( 'dropdown_list_shipping_service_by_location' )) {
    
    function dropdown_list_shipping_service_by_location($var = array()) {
        
        $ci = & get_instance ();
        // Select list items from code_master table
        $items =  $var['data'];
        $html_option = "";
        if(isset($var['html_option'])){
            $html_option = $var['html_option'];
        }
        $html = "<select name=\"" . $var['name'] . "\" id=\"" . $var['id'] . "\" class=\"" . $var['clazz'] . "\" style=\"".$var['style']."\" ".$html_option." >";
        if ($var['has_empty']) {
            $option_default = "";
            if(array_key_exists('option_default', $var)){
            	$option_default = $var['option_default'];
            }
            $html = $html . "<option value=\"\">" . $option_default . "</option>";
        }
        foreach ( $items as $item ) {
            $selected = (!empty($item->$var['value_key']) && $item->$var['value_key'] == $var['value']) ? "selected=\"selected\"" : "";
            $only_express_shipping = (isset($var['show_only_express_shipping']) && ($var['show_only_express_shipping'] == "1") )&& (isset($item->only_express_shipping_flag)) && ($item->only_express_shipping_flag == "1") ? ' (only express shipping)':"";     
            $html = $html . "<option value=\"" . $item->$var['value_key'] . "\" " . $selected . ">" .$item->$var['label_key']. $only_express_shipping . "</option>";
        }
        $html = $html . "</select>";
        return $html;
    }
}

if (! function_exists ( 'my_form_dropdown_only_express_shipping' )) {
    /**
     * Generate dropdown form from [code]
     *
     * @param $var =
     *            array(
     *            "data" => $data, // code of dropdown
     *            "value_key" => $value_key, // code of dropdown
     *            "label_key" => $label_key, // code of dropdown
     *            "value" => $value, // selected value
     *            "name" => $name, //name of select box
     *            "id"	=> $id, // id of select box
     *            "clazz" => $cssClass // css class of select box
     *            "style" => $style // style inline
     *            "has_empty" => $has_empty // Add default empty option
     *            "option_default" => $option_default // Add default empty option
     *            "html_option" => $html_option // html attributes for dropdownlist.
     *            );
     */
    function my_form_dropdown_only_express_shipping($var = array()) {
        $ci = & get_instance ();

        // Select list items from code_master table
        $items =  $var['data'];
        
        $html_option = "";
        if(isset($var['html_option'])){
            $html_option = $var['html_option'];
        }
        $html = "<select name=\"" . $var['name'] . "\" id=\"" . $var['id'] . "\" class=\"" . $var['clazz'] . "\" style=\"".$var['style']."\" ".$html_option." >";
        if ($var['has_empty']) {
            $option_default = "";
            if(array_key_exists('option_default', $var)){
            	$option_default = $var['option_default'];
            }
            //$html = $html . "<option value=\"\" data-text='".$option_default."' ></option>";
            $html = $html . "<option value=\"\">" . $option_default . "</option>";
        }
        foreach ( $items as $item ) {
            
            $selected = (!empty($item->$var['value_key']) && $item->$var['value_key'] == $var['value']) ? "selected=\"selected\"" : "";
            //$only_express_shipping = (isset($var['show_only_express_shipping']) && ($var['show_only_express_shipping'] == "1") )&& (isset($item->only_express_shipping_flag)) && ($item->only_express_shipping_flag == "1") ? '<font style="color: red;"> (only express shipping)</font> ':"";
            $only_express_shipping = (isset($var['show_only_express_shipping']) && ($var['show_only_express_shipping'] == "1") )&& (isset($item->only_express_shipping_flag)) && ($item->only_express_shipping_flag == "1") ? ' (only express shipping)':"";     
            //$html = $html . "<option data-text=' ".$item->$var['label_key'] . $only_express_shipping . " ' value=\"" . $item->$var['value_key'] . "\" " . $selected . "  ></option>";
            $html = $html . "<option value=\"" . $item->$var['value_key'] . "\" " . $selected . $only_express_shipping . "  ></option>"; 
        }
        $html = $html . "</select>";
         

        return $html;
    }
}

if (! function_exists ( 'my_form_dropdown' )) {
    /**
     * Generate dropdown form from [code]
     *
     * @param $var =
     *            array(
     *            "data" => $data, // code of dropdown
     *            "value_key" => $value_key, // code of dropdown
     *            "label_key" => $label_key, // code of dropdown
     *            "value" => $value, // selected value
     *            "name" => $name, //name of select box
     *            "id"	=> $id, // id of select box
     *            "clazz" => $cssClass // css class of select box
     *            "style" => $style // style inline
     *            "has_empty" => $has_empty // Add default empty option
     *            "option_default" => $option_default // Add default empty option
     *            "html_option" => $html_option // html attributes for dropdownlist.
     *            );
     */
    function my_form_dropdown($var = array()) {
        $ci = & get_instance ();

        // Select list items from code_master table
        $items =  $var['data'];

        $html_option = "";
        if(isset($var['html_option'])){
            $html_option = $var['html_option'];
        }
        $html = "<select name=\"" . $var['name'] . "\" id=\"" . $var['id'] . "\" class=\"" . $var['clazz'] . "\" style=\"".$var['style']."\" ".$html_option." >";
        if ($var['has_empty']) {
            $option_default = "";
            if(array_key_exists('option_default', $var)){
            	$option_default = $var['option_default'];
            }
            $html = $html . "<option value=\"\">" . $option_default . "</option>";
        }
        if(!empty($items)){
            
            foreach ( $items as $item ) {
                $selected = (!empty($item->$var['value_key']) && $item->$var['value_key'] == $var['value']) ? "selected=\"selected\"" : "";
                $only_express_shipping = (isset($var['show_only_express_shipping']) && ($var['show_only_express_shipping'] == "1") )&& (isset($item->only_express_shipping_flag)) && ($item->only_express_shipping_flag == "1") ? ' (only express shipping)':"";     
                $html = $html . "<option value=\"" . $item->$var['value_key'] . "\" " . $selected . ">" .$item->$var['label_key']. $only_express_shipping . "</option>";
            }
        }
        $html = $html . "</select>";
        

        return $html;
    }
}

if (! function_exists ( 'display_group_search' )) {
    
    /**
     * Display group search.
     * 
     * @param unknown_type $var have structure
     * {
     *     NodeIDLevel1 => NodeIDLevel1
     *     DescriptionLevel1 => DescriptionLevel1
     *     TotalProduct => 1000
     *     childs => array(
     *         NodeIDLevel2 => NodeIDLevel2,
     *         DescriptionLevel2 => DescriptionLevel2
     *     )
     * }
     * 
     */
    function display_group_search($var, $tree_type = '001') {
        $ci = & get_instance ();
        $data = array (
                "data" => $var,
                "tree_type" => $tree_type
        );
        
        $html = $ci->load->view('common/display_group_search', $data, TRUE);
        
        return $html;
    }
}

// ------------------------------------------------------------------------

/**
 * Error Logging Interface
 *
 * We use this as a simple mechanism to access the logging
 * class and send messages to be logged.
 *
 * @access	public
 * @return	void
 */
if ( ! function_exists('log_audit_message'))
{
    function log_audit_message($level = 'error', $message, $php_error = FALSE, $prefix_file_name='auditlog-')
    {
        $ci = & get_instance();
        $isCustomer = APContext::isCustomerLoggedIn();
        $customer_id = $isCustomer ? APContext::getCustomerCodeLoggedIn() : '0';
        
        $log_message = array(
            'level' => $level,
            'type' => $prefix_file_name,
            'message' => $message,
            'created_date' => now(),
            'created_by' => $customer_id,
            'created_by_type' => $isCustomer ? 'customer': ''
        );
        $ci->log_audit_message_m->insert($log_message);
    }
}

/**
 * Return language text in user side
 */
if ( ! function_exists('language'))
{
    function language($language_key, $data = array())
    {
        return multi_language($language_key, 'user', $data);
    }
}


/**
 * Echo language text in user side
 */
if ( ! function_exists('language_e'))
{
    function language_e($language_key, $data = array())
    {
        echo multi_language($language_key, 'user', $data);;
    }
}

/**
 * Return language text in admin side
 */
if ( ! function_exists('admin_language'))
{
    function admin_language($language_key, $data = array())
    {
        return multi_language($language_key, 'admin', $data);
    }
}


/**
 * Echo language text in admin side
 */
if ( ! function_exists('admin_language_e'))
{
    function admin_language_e($language_key, $data = array())
    {
        echo multi_language($language_key, 'admin', $data);
    }
}

/**
 * Get language text by context
 */
if ( ! function_exists('multi_language'))
{
    function multi_language($language_key, $context, $data = array())
    {
        ci()->load->model('settings/language_text_m');
       
        $site_language = 'English';
        
        if ($context == 'admin') {
            $admin = APContext::getAdminLoggedIn();
            $site_language = empty($admin->language) ? 'English' : $admin->language;
        } else if ($context == 'user') {
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $customer_language = CustomerProductSetting::get($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'language');
            $site_language = empty($customer_language) ? 'English' : $customer_language;
        }
        
        $language = ci()->language_text_m->language($site_language, $language_key);
        
        if (!empty($language)) {
            return APUtils::parserString($language->value, $data);
        } else {
            $language = ci()->language_text_m->language('English', $language_key);
            return empty($language) ? $language_key : APUtils::parserString($language->value, $data);
        }
    }
}

// ------------------------------------------------------------------------