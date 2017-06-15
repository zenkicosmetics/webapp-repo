<?php defined('BASEPATH') or exit('No direct script access allowed');

class case_phone_number_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->db->dbprefix('cases_phone_number');
        $this->primary_key = 'id';
    }
}