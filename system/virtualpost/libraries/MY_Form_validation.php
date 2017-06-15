<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed.');
class MY_Form_validation extends CI_Form_validation {
    /**
     * The model class to call with callbacks
     */
    private $_model;
    function __construct($rules = array()) {
        parent::__construct($rules);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Alpha-numeric with underscores dots and dashes
     * 
     * @access public
     * @param
     *            string
     * @return bool
     */
    function alpha_dot_dash($str) {
        return (! preg_match("/^([-a-z0-9_\-\.])+$/i", $str)) ? FALSE : TRUE;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Sneaky function to get field data from
     * the form validation libraru
     * 
     * @access public
     * @param
     *            string
     * @return bool
     */
    function field_data($field) {
        return (isset($this->_field_data [$field])) ? $this->_field_data [$field] : null;
    }
    // --------------------------------------------------------------------
    
    /**
     * Formats an UTF-8 string and removes potential harmful characters
     * 
     * @access public
     * @param
     *            string
     * @return string
     * @author Jeroen v.d. Gulik
     * @since v1.0-beta1
     * @todo Find decent regex to check utf-8 strings for harmful characters
     */
    function utf8($str) {
        // If they don't have mbstring enabled (suckers) then we'll have to do
        // with what we got
        if (! function_exists('mb_convert_encoding')) {
            return $str;
        }
        
        $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }
    
    // --------------------------------------------------------------------
    /**
     * Get array of error.
     * 
     * @return multitype:
     */
    public function error_array() {
        return $this->_error_array;
    }
    
    /**
     * Get error json
     */
    public function error_json() {
        // Generate the error string
        $str = '';
        if (count($this->_error_array) > 0) {
            foreach ( $this->_error_array as $val ) {
                if ($val != '') {
                    $str .= $val . "</br>";
                }
            }
            
            return array (
                    "status" => FALSE,
                    "message" => $str 
            );
        } else {
            return array (
                    "status" => TRUE,
                    "message" => $str 
            );
        }
    }
    
    /**
     * Sets the model to be used for validation callbacks.
     * It's set dynamically in MY_Model
     * 
     * @access private
     * @param
     *            string	The model class name
     * @return void
     */
    public function set_model($model) {
        if ($model) {
            $this->_model = strtolower($model);
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Format an error in the set error delimiters
     * 
     * @access public
     * @param
     *            string
     * @return void
     */
    public function format_error($error) {
        return $this->_error_prefix . $error . $this->_error_suffix;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Valid URL
     * 
     * @access public
     * @param
     *            string
     * @return void
     */
    public function valid_url($str) {
        if (filter_var($str, FILTER_VALIDATE_URL)) {
            return true;
        } else {
            $this->set_message('valid_url', $this->CI->lang->line('valid_url'));
            return false;
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Executes the Validation routines
     * Modified to work with HMVC -- Phil Sturgeon
     * Modified to work with callbacks in the calling model -- Jerel Unruh
     * 
     * @access private
     * @param
     *            array
     * @param
     *            array
     * @param
     *            mixed
     * @param
     *            integer
     * @return mixed
     */
    protected function _execute($row, $rules, $postdata = NULL, $cycles = 0) {
        // If the $_POST data is an array we will run a recursive call
        if (is_array($postdata)) {
            foreach ( $postdata as $key => $val ) {
                $this->_execute($row, $rules, $val, $cycles);
                $cycles ++;
            }
            
            return;
        }
        
        // --------------------------------------------------------------------
        
        // If the field is blank, but NOT required, no further tests are
        // necessary
        $callback = FALSE;
        if (! in_array('required', $rules) and is_null($postdata)) {
            // Before we bail out, does the rule contain a callback?
            if (preg_match("/(callback_\w+(\[.*?\])?)/", implode(' ', $rules), $match)) {
                $callback = TRUE;
                $rules = (array (
                        '1' => $match [1] 
                ));
            } else {
                return;
            }
        }
        
        // --------------------------------------------------------------------
        
        // Isset Test. Typically this rule will only apply to checkboxes.
        if (is_null($postdata) and $callback == FALSE) {
            if (in_array('isset', $rules, TRUE) or in_array('required', $rules)) {
                // Set the message type
                $type = (in_array('required', $rules)) ? 'required' : 'isset';
                
                if (! isset($this->_error_messages [$type])) {
                    if (FALSE === ($line = $this->CI->lang->line($type))) {
                        $line = 'The field was not set';
                    }
                } else {
                    $line = $this->_error_messages [$type];
                }
                
                // Build the error message
                $message = sprintf($line, $this->_translate_fieldname($row ['label']));
                
                // Save the error message
                $this->_field_data [$row ['field']] ['error'] = $message;
                
                if (! isset($this->_error_array [$row ['field']])) {
                    $this->_error_array [$row ['field']] = $message;
                }
            }
            
            return;
        }
        
        // --------------------------------------------------------------------
        
        // Cycle through each rule and run it
        foreach ( $rules as $rule ) {
            $_in_array = FALSE;
            
            // We set the $postdata variable with the current data in our master
            // array so that
            // each cycle of the loop is dealing with the processed data from
            // the last cycle
            if ($row ['is_array'] == TRUE and is_array($this->_field_data [$row ['field']] ['postdata'])) {
                // We shouldn't need this safety, but just in case there isn't
                // an array index
                // associated with this cycle we'll bail out
                if (! isset($this->_field_data [$row ['field']] ['postdata'] [$cycles])) {
                    continue;
                }
                
                $postdata = $this->_field_data [$row ['field']] ['postdata'] [$cycles];
                $_in_array = TRUE;
            } else {
                $postdata = $this->_field_data [$row ['field']] ['postdata'];
            }
            
            // --------------------------------------------------------------------
            
            // Is the rule a callback?
            $callback = FALSE;
            if (substr($rule, 0, 9) == 'callback_') {
                $rule = substr($rule, 9);
                $callback = TRUE;
            }
            
            // Strip the parameter (if exists) from the rule
            // Rules can contain a parameter: max_length[5]
            $param = FALSE;
            if (preg_match("/(.*?)\[(.*)\]/", $rule, $match)) {
                $rule = $match [1];
                $param = $match [2];
            }
            
            // Call the function that corresponds to the rule
            if ($callback === TRUE) {
                // first check in the controller scope
                if (method_exists(CI::$APP->controller, $rule)) {
                    $result = call_user_func(array (
                            new CI::$APP->controller(),
                            $rule 
                    ), $postdata, $param);
                }                 // it wasn't in the controller. Did MY_Model specify a valid
                  // model in use?
                elseif ($this->_model) {
                    // moment of truth. Does the callback itself exist?
                    if (method_exists(CI::$APP->{$this->_model}, $rule)) {
                        $result = call_user_func(array (
                                CI::$APP->{$this->_model},
                                $rule 
                        ), $postdata, $param);
                    } else {
                        throw new Exception('Undefined callback ' . $rule . ' Not found in ' . $this->_model);
                    }
                } else {
                    throw new Exception('Undefined callback "' . $rule . '" in ' . CI::$APP->controller);
                }
                
                // Re-assign the result to the master data array
                if ($_in_array == TRUE) {
                    $this->_field_data [$row ['field']] ['postdata'] [$cycles] = (is_bool($result)) ? $postdata : $result;
                } else {
                    $this->_field_data [$row ['field']] ['postdata'] = (is_bool($result)) ? $postdata : $result;
                }
                
                // If the field isn't required and we just processed a callback
                // we'll move on...
                if (! in_array('required', $rules, TRUE) and $result !== FALSE) {
                    continue;
                }
            } else {
                if (! method_exists($this, $rule)) {
                    // If our own wrapper function doesn't exist we see if a
                    // native PHP function does.
                    // Users can use any native PHP function call that has one
                    // param.
                    if (function_exists($rule)) {
                        $result = $rule($postdata);
                        
                        if ($_in_array == TRUE) {
                            $this->_field_data [$row ['field']] ['postdata'] [$cycles] = (is_bool($result)) ? $postdata : $result;
                        } else {
                            $this->_field_data [$row ['field']] ['postdata'] = (is_bool($result)) ? $postdata : $result;
                        }
                    } else {
                        log_message('debug', "Unable to find validation rule: " . $rule);
                    }
                    
                    continue;
                }
                
                $result = $this->$rule($postdata, $param);
                
                if ($_in_array == TRUE) {
                    $this->_field_data [$row ['field']] ['postdata'] [$cycles] = (is_bool($result)) ? $postdata : $result;
                } else {
                    $this->_field_data [$row ['field']] ['postdata'] = (is_bool($result)) ? $postdata : $result;
                }
            }
            
            // Did the rule test negatively? If so, grab the error.
            if ($result === FALSE) {
                if (! isset($this->_error_messages [$rule])) {
                    if (FALSE === ($line = $this->CI->lang->line($rule))) {
                        $line = 'Unable to access an error message corresponding to your field name.' . $rule;
                    }
                } else {
                    $line = $this->_error_messages [$rule];
                }
                
                // Is the parameter we are inserting into the error message the
                // name
                // of another field? If so we need to grab its "field label"
                if (isset($this->_field_data [$param]) and isset($this->_field_data [$param] ['label'])) {
                    $param = $this->_translate_fieldname($this->_field_data [$param] ['label']);
                }
                
                // Build the error message
                $message = sprintf($line, $this->_translate_fieldname($row ['label']), $param);
                
                // Save the error message
                $this->_field_data [$row ['field']] ['error'] = $message;
                
                if (! isset($this->_error_array [$row ['field']])) {
                    $this->_error_array [$row ['field']] = $message;
                }
                
                return;
            }
        }
    }
    
    // --------------------------------------------------------------------------
    
    /**
     * Check Recaptcha callback
     * Used for streams but can be used in other
     * recaptcha situations.
     * 
     * @access public
     * @param
     *            string
     * @return bool
     */
    function check_recaptcha($val) {
        if ($this->CI->recaptcha->check_answer($this->CI->input->ip_address(), $this->CI->input->post('recaptcha_challenge_field'), $val)) {
            return true;
        } else {
            $this->set_message('check_captcha', $this->CI->lang->line('recaptcha_incorrect_response'));
            
            return false;
        }
    }
    
    /**
     * Postcode Validation Callback
     */
    public function postcode( $str )
    {
        if ( ! preg_match('/^([A-Za-z0-9 -])+$/', $str) )
        {
            $this->set_message(__FUNCTION__, 'Please enter valid %s.');
            return FALSE;
        }
    
        return TRUE;
    }
    
    /**
     * Phone number Validation Callback
     */
    public function phong_number( $str )
    {
        if (empty($str)) {
            return TRUE;
        }
    	if ( ! preg_match('/^([0-9\+\-\(\) ])+$/', $str) )
    	{
    		$this->set_message(__FUNCTION__, 'Please enter valid %s.');
    		return FALSE;
    	}
    
    	return TRUE;
    }
    
    /**
     * Postcode Validation Callback
     */
    public function validname( $str )
    {
        return TRUE;
    }
    
    /**
     * Postcode Validation Callback
     */
    public function validname02( $str )
    {
        if (empty($str)) {
            return TRUE;
        }
        if (preg_match('/[\'^!£$%*()}{@~?><>,|=+¬]/', $str) )
        {
            $this->set_message(__FUNCTION__, 'Please enter valid %s.');
            return FALSE;
        }
        $firstchar = substr($str, 0, 1);
        if ( preg_match('/^([0-9]+)$/', $firstchar) )
        {
            if (strlen($str) >= 2) {
                $twotchar = substr($str, 0, 2);
                if ( preg_match('/^([0-9]+)$/', $twotchar) )
                {
                    $this->set_message(__FUNCTION__, 'Please enter valid %s.');
                    return FALSE;
                }
            } else {
                $this->set_message(__FUNCTION__, 'Please enter valid %s.');
                return FALSE;
            }
        }
        /**
        if ( ! preg_match('/^([a-zA-Z\+_\-]+)$/', $str) )
        {
            $this->set_message(__FUNCTION__, 'Please enter valid %s.');
            return FALSE;
        }
        */
        return TRUE;
    }
    
    /**
     * Postcode Validation Callback
     */
    public function valid_companyname( $str )
    {
        return TRUE;
    }
    
    /**
     * Postcode Validation Callback
     */
    public function valid_companyname02( $str )
    {
        if (empty($str)) {
            return TRUE;
        }
        if (preg_match('/[\'^!£$%*()}{@~?><>,|=+¬]/', $str) )
        {
            $this->set_message(__FUNCTION__, 'Please enter valid %s.');
            return FALSE;
        }
        $firstchar = substr($str, 0, 1);
        if ( ! preg_match('/^([a-zA-Z\+_\-]+)$/', $firstchar) )
        {
            $this->set_message(__FUNCTION__, 'Please enter valid %s.');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Overwrite common rule messages by multi language
     */
    public function overwrite_validation_messages()
    {
        $this->set_message("required", language('MY_Form_validation_ValidationMessRequired'));
        $this->set_message("isset", language('MY_Form_validation_ValidationMessIsset'));
        $this->set_message("valid_email", language('MY_Form_validation_ValidationMessValidEmail'));
        $this->set_message("valid_emails", language('MY_Form_validation_ValidationMessValidEmails'));
        $this->set_message("valid_url", language('MY_Form_validation_ValidationMessValidUrl'));
        $this->set_message("valid_ip", language('MY_Form_validation_ValidationMessValidIp'));
        $this->set_message("min_length", language('MY_Form_validation_ValidationMessMinLength'));
        $this->set_message("max_length", language('MY_Form_validation_ValidationMessMaxLength'));
        $this->set_message("exact_length", language('MY_Form_validation_ValidationMessExactLength'));
        $this->set_message("alpha", language('MY_Form_validation_ValidationMessAlpha'));
        $this->set_message("alpha_numeric", language('MY_Form_validation_ValidationMessAlphaNumeric'));
        $this->set_message("alpha_dash", language('MY_Form_validation_ValidationMessAlphaDash'));
        $this->set_message("numeric", language('MY_Form_validation_ValidationMessNumeric'));
        $this->set_message("is_numeric", language('MY_Form_validation_ValidationMessIsNumeric'));
        $this->set_message("integer", language('MY_Form_validation_ValidationMessInteger'));
        $this->set_message("regex_match", language('MY_Form_validation_ValidationMessRegexMatch'));
        $this->set_message("matches", language('MY_Form_validation_ValidationMessMatches'));
        $this->set_message("is_unique", language('MY_Form_validation_ValidationMessIsUnique'));
        $this->set_message("is_natural", language('MY_Form_validation_ValidationMessIsNatural'));
        $this->set_message("is_natural_no_zero", language('MY_Form_validation_ValidationMessIsNaturalNoZero'));
        $this->set_message("decimal", language('MY_Form_validation_ValidationMessDecimal'));
        $this->set_message("less_than", language('MY_Form_validation_ValidationMessLessThan'));
        $this->set_message("greater_than", language('MY_Form_validation_ValidationMessGreaterThan'));
    }

    /**
     * Get error message by rule name
     * @param $error
     * @return null
     */
    public function get_error_message($error)
    {
        $message = null;
        if (isset($this->_error_messages[$error])) {
            $message = $this->_error_messages[$error];
        }
        return $message;
    }

    public function _translate_fieldname($fieldname)
    {
        if (strpos($fieldname, 'lang:') === FALSE) {
            return admin_language($fieldname);
        }else{
            return parent::_translate_fieldname($fieldname);
        }
    }
}

/* End of file MY_Form_validation.php */