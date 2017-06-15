<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class supper_admin_m extends MY_SupperAdminModel {
    // Declare supprt admin database instance
    private $supperadmin_db;
    
    function __construct() {
        parent::__construct();
        $this->supperadmin_db = $this->load->database('supper_admin', TRUE);
        $this->_table = $this->profile_table = $this->supperadmin_db->dbprefix('supper_admin');
        $this->primary_key = 'id';
    }
}