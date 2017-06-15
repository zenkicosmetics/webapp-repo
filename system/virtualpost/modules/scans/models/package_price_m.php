<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Group model
 */
class Package_price_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->dbprefix('package_prices');
        $this->primary_key = 'id';
    }
}