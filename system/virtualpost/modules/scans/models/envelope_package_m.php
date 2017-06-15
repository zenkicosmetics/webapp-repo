<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Group model
 */
class Envelope_package_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->dbprefix('envelope_package');
        $this->primary_key = 'package_id';
    }
}