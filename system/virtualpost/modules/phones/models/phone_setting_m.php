<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DuNT
 */
class phone_setting_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('phone_settings');
        $this->primary_key = 'id';
    }
}