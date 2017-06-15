<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Group model
 */
class envelope_prepayment_cost_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->dbprefix('envelope_prepayment_cost');
        $this->primary_key = 'id';
    }
    
}