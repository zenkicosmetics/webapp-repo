<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Group model
 */
class Partner_customer_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->dbprefix('partner_customers');
        $this->primary_key = 'id';
    }
}