<?php

defined('BASEPATH') or exit('No direct script access allowed');

class cases_checklist_m extends MY_Model {

    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('cases_checklist');
        $this->primary_key = 'id';
    }
}