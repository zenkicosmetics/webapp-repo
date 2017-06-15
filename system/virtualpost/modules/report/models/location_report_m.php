<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author Nguyen Dung
 */
class location_report_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->profile_table = $this->db->dbprefix('location_report');
        $this->primary_key = 'id';
    }
    
   
}