<?php

defined('BASEPATH') or exit('No direct script access allowed');

class postbox_invoice_email_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('postbox_invoice_emails');
        $this->primary_key = 'id';
    }
}