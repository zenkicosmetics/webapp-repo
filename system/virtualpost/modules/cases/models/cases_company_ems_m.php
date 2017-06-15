<?php defined('BASEPATH') or exit('No direct script access allowed');

class cases_company_ems_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->db->dbprefix('cases_company_ems');
        $this->primary_key = 'id';
    }
}