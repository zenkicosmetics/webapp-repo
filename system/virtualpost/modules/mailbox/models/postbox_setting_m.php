<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class postbox_setting_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('postbox_settings');
        $this->primary_key = 'postbox_id';
    }
}