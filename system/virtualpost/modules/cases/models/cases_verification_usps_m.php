<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cases_verification_usps_m extends MY_Model
{

    function __construct()
    {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('cases_verification_usps');
        $this->primary_key = 'id';
    }
}