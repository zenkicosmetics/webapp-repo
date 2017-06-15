<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class log_audit_message_m extends MY_Model {
    
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('log_audit_message');
        $this->primary_key = 'id';
    }

}

/* End of file log_audit_message_m.php */
