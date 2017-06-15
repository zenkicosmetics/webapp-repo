<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class web_message_log_m extends MY_Model {
    
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('web_message_log');
        $this->primary_key = 'id';
    }

}

/* End of file log_audit_message_m.php */
