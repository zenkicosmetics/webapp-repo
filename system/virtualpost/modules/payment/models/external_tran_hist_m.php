<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class external_tran_hist_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('external_tran_hist');
        $this->primary_key = 'id';
    }
}