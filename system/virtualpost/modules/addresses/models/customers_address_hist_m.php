<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author Dung
 */
class customers_address_hist_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('customers_address_hist');
        $this->primary_key = 'customers_address_hist_id';
    }
}