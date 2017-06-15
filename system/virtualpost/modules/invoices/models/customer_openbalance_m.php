<?php

defined('BASEPATH') or exit('No direct script access allowed');

class customer_openbalance_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('customer_openbalance');
        $this->primary_key = 'customer_id';
    }
}