<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DungNT
 */
class phone_area_code_latest_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('phone_area_code_latest');
        $this->primary_key = 'id';
    }

    /**
     * Truncate table
     *
     * @author Phil Sturgeon
     * @param array $primary_values
     * @return bool
     */
    public function truncate()
    {
        $this->db->truncate('phone_area_code_latest');
    }
}