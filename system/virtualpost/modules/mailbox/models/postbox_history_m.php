<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author Hung
 */
class postbox_history_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('postbox_history');
        $this->primary_key = 'id';
    }

}