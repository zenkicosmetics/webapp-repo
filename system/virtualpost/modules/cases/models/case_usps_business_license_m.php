<?php defined('BASEPATH') or exit('No direct script access allowed');

class Case_usps_business_license_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->db->dbprefix('case_usps_business_license');
        $this->primary_key = 'id';
    }
}