<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 */
class customers_forward_address_hist_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->profile_table = $this->db->dbprefix('customers_forward_address_hist');
        $this->primary_key = 'id';
    }
}