<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cases_verification_personal_identity_m extends MY_Model
{

    function __construct()
    {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('cases_verification_personal_identity');
        $this->primary_key = 'id';
    }
}