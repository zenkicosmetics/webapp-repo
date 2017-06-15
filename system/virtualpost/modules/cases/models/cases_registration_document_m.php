<?php

defined('BASEPATH') or exit('No direct script access allowed');

class cases_registration_document_m extends MY_Model {

    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('cases_registration_document');
        $this->primary_key = 'id';
    }
}