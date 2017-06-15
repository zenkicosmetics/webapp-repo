<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DungNT
 */
class app_external_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('app_external');
        $this->primary_key = 'id';
    }
    
}