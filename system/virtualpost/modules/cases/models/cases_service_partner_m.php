<?php defined('BASEPATH') or exit('No direct script access allowed');

class cases_service_partner_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('cases_service_partner');
        $this->primary_key = 'partner_id';
    }
}