<?php

defined('BASEPATH') or exit('No direct script access allowed');

class envelope_storage_fee_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('envelope_storage_fee');
        $this->primary_key = 'id';
    }
    
}