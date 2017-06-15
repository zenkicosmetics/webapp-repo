<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Group model
 */
class envelope_shipping_request_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->dbprefix('envelope_shipping_request');
        $this->primary_key = 'id';
    }
    
}