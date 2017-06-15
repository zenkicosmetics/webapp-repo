<?php

defined('BASEPATH') or exit('No direct script access allowed');

class digital_devices_setting_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('partner_digital_devices_setting');
        $this->primary_key = 'panel_code';
    }
}