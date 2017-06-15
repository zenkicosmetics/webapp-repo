<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DungNT
 */
class payment_job_hist_test_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('payment_job_hist_test');
        $this->primary_key = 'id';
    }
}