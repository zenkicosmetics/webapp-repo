<?php defined('BASEPATH') or exit('No direct script access allowed');

class Case_usps_officer_m extends MY_Model
{
    

    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->db->dbprefix('case_usps_officer');
        $this->primary_key = 'id';
    }
}