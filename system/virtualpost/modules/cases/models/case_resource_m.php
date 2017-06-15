<?php defined('BASEPATH') or exit('No direct script access allowed');

class case_resource_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->db->dbprefix('cases_resources');
        $this->primary_key = 'id';
    }
}